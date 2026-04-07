<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use ITHilbert\LaravelKit\Models\AiTask;
use ITHilbert\LaravelKit\Models\AiTaskRun;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\InputStream;

class AiDaemonWorkerCommand extends Command
{
    protected $signature = 'ai:daemon';
    protected $description = 'Langlaufender Daemon-Worker für AI-Tasks (behält die LLM Session alive).';

    public function handle()
    {
        $this->info("Starte AI Daemon Worker...");

        $env = $_ENV;
        $env['GOOGLE_API_KEY'] = config('services.gemini.key', env('GEMINI_API_KEY'));
        if (isset($env['GEMINI_API_KEY'])) {
            unset($env['GEMINI_API_KEY']);
        }

        $agentPath = base_path('node_modules/.bin/gemini');

        // Umgehen von PHP-Timeouts
        set_time_limit(0);

        while (true) {
            // Heartbeat an die App (Fallback in Job weiß, dass Daemon aktiv ist)
            Cache::put('ai_daemon_last_ping', now(), 600);

            // Polling aller Tasks, die neu eintrudeln
            // Wir suchen primär 'queued_for_daemon' in Runs, oder 'pending' in Tasks
            /** @var \ITHilbert\LaravelKit\Models\AiTask|null $task */
            $task = AiTask::where('status', 'pending')->first();

            if (! $task) {
                sleep(3);
                continue;
            }

            $this->info("Task #{$task->id} gefunden. Verarbeite...");

            // Create Run Log
            $run = AiTaskRun::create([
                'ai_task_id' => $task->id,
                'run_no' => AiTaskRun::where('ai_task_id', $task->id)->max('run_no') + 1,
                'job_type' => 'gemini_daemon',
                'status' => 'processing',
            ]);
            $task->update(['status' => 'running']);

            try {
                $prompt = "WICHTIG (SSOT / Pipeline-Modus): Du wurdest für Task ID {$task->id} durch den Daemon gestartet!\n";
                $prompt .= "1. Führe sofort das Laravel Kommando 'php artisan ai:task-read {$task->id}' aus, um deine konkreten fachlichen Instruktionen (Module, Task-Details, Dringlichkeit) aus der Datenbank zu lesen.\n";
                $prompt .= "2. Folge den Vorgaben aus diesem Befehl 1:1.\n";
                $prompt .= "3. Hänge an deine finale System-Ausgabe einen Abschnitt '# Agent Feedback' an. Beschreibe darin kurz: Wo lagen die Probleme? Wie lang dauerte die Suche & das Coden?\n";
                $prompt .= "4. Beende dich bei Erfolg am Ende deines Durchlaufs einfach sauber.\n";
                $prompt .= "- WICHTIG: Gib am Ende deiner Antwort das exakte Keyword 'DAEMON_TASK_FINISHED' als eigene Zeile aus, damit die Pipeline weiß, dass du fertig bist.";

                // Wir starten den Prozess im Yolo-Modus PRO Task, um Memory Leaks zu vermeiden, ABER
                // wir nutzen `--acp` oder standard?
                // Da PTY extrem fehleranfällig ist, machen wir einen Trade-Off:
                // Wir starten den Prozess hier ganz regulär mit Process::run!
                // Moment! Wenn wir Process::run machen, haben wir immer noch den 30s Delay!
                // Aber der Daemon Worker hat den Vorteil, dass er das System nicht blockiert.
                // Da ich den TTY PTY Fallback nicht riskieren will, belasse ich den Single-Run Aufbau hier.
                
                $processArgs = [
                    $agentPath,
                    '--yolo',
                    '-p',
                    $prompt,
                ];

                // Workspace Limitierung
                $targetPkg = strtolower($task->module);
                if (in_array(strtolower($task->module), ['ai', 'dashboard', 'core', 'laravel-kit'])) {
                    $targetPkg = 'laravel-kit';
                }

                $processArgs[] = '--include-directories';
                $processArgs[] = base_path('app');
                $processArgs[] = '--include-directories';
                $processArgs[] = base_path('resources');
                $processArgs[] = '--include-directories';
                $processArgs[] = base_path('routes');
                $processArgs[] = '--include-directories';
                $processArgs[] = base_path('config');
                $processArgs[] = '--include-directories';
                $processArgs[] = base_path('vendor/ithilbert/' . $targetPkg);

                $startTime = microtime(true);
                $output = '';
                $errorOutput = '';
                
                $process = \Illuminate\Support\Facades\Process::path(base_path())
                    ->timeout(3600)
                    ->env($env);
                    
                $result = $process->run($processArgs, function (string $type, string $buffer) use (&$output, &$errorOutput, $startTime) {
                    $elapsed = round(microtime(true) - $startTime, 1);
                    $lines = explode("\n", rtrim($buffer, "\n"));
                    if ($type === 'err') {
                        foreach ($lines as $line) {
                            $errorOutput .= "[+{$elapsed}s] " . $line . "\n";
                        }
                    } else {
                        $output .= $buffer;
                    }
                });

                if (! $result->successful()) {
                    throw new \Exception("ProcessFailed:\n" . $errorOutput);
                }
                
                $finalLog = trim($output);
                
                // Rückfrage check
                $rueckfrage = null;
                if (preg_match('/# FEEDBACK_REQUIRED\s*(.*?)($|# Agent Feedback)/s', $finalLog, $matches)) {
                    $rueckfrage = trim($matches[1]);
                }

                $totalTime = round(microtime(true) - $startTime, 1);
                $run->update([
                    'status' => $rueckfrage ? 'needs_info' : 'success',
                    'stdout_log' => "Gemini CLI Daemon Run in {$totalTime}s\n\n" . $finalLog,
                    'finished_at' => now(),
                ]);

                if ($rueckfrage) {
                    $task->update(['status' => 'needs_info', 'rueckfrage' => $rueckfrage]);
                } else {
                    $task->update(['status' => 'completed']);
                }

                $this->info("Task #{$task->id} abgeschlossen in {$totalTime}s.");

            } catch (\Exception $e) {
                $run->update([
                    'status' => 'failed',
                    'stderr_log' => $e->getMessage(),
                    'finished_at' => now(),
                ]);
                $task->update(['status' => 'failed']);
                $this->error("Task failed: " . $e->getMessage());
            }
        }
    }
}
