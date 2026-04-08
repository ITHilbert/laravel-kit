<?php

namespace ITHilbert\LaravelKit\Jobs\Ai;

use Illuminate\Support\Facades\Process;

class RunGeminiTaskJob extends AbstractAiJob
{
    protected function getJobType(): string
    {
        return 'gemini_worker';
    }

    public function handle()
    {
        $run = $this->getRunLog();
        $run->update(['status' => 'processing']);

        if (env('AI_EXECUTION_MODE', 'sync') === 'daemon') {
            $this->aiTask->update(['status' => 'pending']);
            while (true) {
                $status = $this->aiTask->fresh()->status;
                if (in_array($status, ['completed', 'failed', 'needs_info'])) {
                    if ($status === 'failed') {
                        $run->update(['status' => 'failed', 'finished_at' => now(), 'stderr_log' => 'Failed by daemon']);
                        $this->fail(new \Exception("Task failed via daemon"));
                    }
                    $run->update(['status' => 'success', 'finished_at' => now(), 'stdout_log' => 'Handled by daemon']);
                    return;
                }
                sleep(2);
            }
        }

        $this->aiTask->update(['status' => 'running']);

        try {
            $agentPath = base_path('node_modules/.bin/gemini');
            if (! file_exists($agentPath)) {
                throw new \Exception("gemini-cli ist nicht installiert. Bitte 'npm install @google/gemini-cli -D' in ".base_path().' ausführen.');
            }

            $prompt = "WICHTIG (SSOT / Pipeline-Modus): Du wurdest für Task ID {$this->aiTask->id} gestartet!\n";
            $prompt .= "1. Führe sofort das Laravel Kommando 'php artisan ai:task-read {$this->aiTask->id}' aus, um deine konkreten fachlichen Instruktionen (Module, Task-Details, Dringlichkeit) aus der Datenbank zu lesen. (Mache dies immer, auch wenn du den Task 'schon zu kennen' glaubst)\n";
            $prompt .= "2. Folge den Vorgaben aus diesem Befehl 1:1.\n";
            $prompt .= "3. Hänge an deine finale System-Ausgabe einen Abschnitt '# Agent Feedback' an. Beschreibe darin kurz: Wo lagen die Probleme? Wie lang dauerte die Suche & das Coden?\n";
            $prompt .= "4. Beende dich bei Erfolg am Ende deines Durchlaufs einfach sauber.";

            $processArgs = [
                $agentPath,
                '--yolo',
                '-p',
                $prompt,
            ];

            // Workspace-Erweiterung für Pakete: Wir binden nur noch selektiv ein, um den Gemini-Context extrem zu verkleinern!
            $ithilbertVendor = base_path('vendor/ithilbert');
            $targetPkg = strtolower($this->aiTask->module);
            
            // Map common modules to the laravel-kit package just in case
            if (in_array(strtolower($this->aiTask->module), ['ai', 'dashboard', 'core', 'laravel-kit'])) {
                $targetPkg = 'laravel-kit';
            }

            if (is_dir($ithilbertVendor)) {
                foreach (scandir($ithilbertVendor) as $pkg) {
                    if ($pkg !== '.' && $pkg !== '..') {
                        $pkgPath = $ithilbertVendor . '/' . $pkg;
                        
                        // Nur das konkrete Modul-Paket einbinden, um nicht zig MB Quellcode bei jedem Request hochzuladen
                        if ($pkg !== $targetPkg && $targetPkg !== 'all') {
                            continue;
                        }

                        if (is_link($pkgPath)) {
                            $realPath = realpath($pkgPath);
                            if ($realPath) {
                                $processArgs[] = '--include-directories';
                                $processArgs[] = base_path('app');
                                $processArgs[] = '--include-directories';
                                $processArgs[] = base_path('resources');
                                $processArgs[] = '--include-directories';
                                $processArgs[] = base_path('routes');
                                $processArgs[] = '--include-directories';
                                $processArgs[] = base_path('config');
                                $processArgs[] = '--include-directories';
                                $processArgs[] = $realPath;
                            }
                        }
                    }
                }
            }

            // Environment Variablen injecten (Auth)
            $env = $_ENV;
            $env['GOOGLE_API_KEY'] = config('services.gemini.key', env('GEMINI_API_KEY'));
            
            // Verhindern, dass die gemini-cli "Both GOOGLE_API_KEY and GEMINI_API_KEY are set" meckert
            if (isset($env['GEMINI_API_KEY'])) {
                unset($env['GEMINI_API_KEY']);
            }

            file_put_contents(base_path("job_args.log"), json_encode($processArgs)); $startTime = microtime(true);
            $output = '';
            $errorOutput = '';
            
            $result = \Illuminate\Support\Facades\Process::path(base_path())
                ->timeout(3600)
                ->env($env)
                ->run($processArgs, function (string $type, string $buffer) use (&$output, &$errorOutput, $startTime) {
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
                $exitCode = $result->exitCode();
                throw new \Exception("ProcessFailed with exit code {$exitCode}:\n--- STDOUT ---\n{$output}\n--- STDERR ---\n{$errorOutput}");
            }
            
            $finalLog = trim($output) ?: trim($result->output());
            
            $finalError = trim($errorOutput) ?: trim($result->errorOutput());
            if (!empty($finalError)) {
                $finalLog .= "\n\n--- CLI LOGS (Stderr) ---\n" . $finalError;
            }
            
            // Checken, falls die CLI (z.B. durch andere Configs) doch noch JSON geliefert hat
            $decoded = json_decode($output, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['response'])) {
                $finalLog = "Gemini Response:\n".$decoded['response'];
                if (!empty($errorOutput)) {
                    $finalLog .= "\n\n--- CLI LOGS (Stderr) ---\n" . trim($errorOutput);
                }
            }

            // Check for feedback required
            $rueckfrage = null;
            if (preg_match('/# FEEDBACK_REQUIRED\s*(.*?)($|# Agent Feedback)/s', $finalLog, $matches)) {
                $rueckfrage = trim($matches[1]);
            }

            $totalTime = round(microtime(true) - $startTime, 1);
            $run->update([
                'status' => $rueckfrage ? 'needs_info' : 'success',
                'stdout_log' => "Gemini CLI wurde in {$totalTime}s ausgeführt!\n\n"
                              . "--- URSPRÜNGLICHE AUFGABE ---\n"
                              . trim($this->aiTask->description) . "\n\n"
                              . "--- OUTPUT ---\n"
                              . $finalLog,
                'stderr_log' => null,
                'finished_at' => now(),
            ]);

            if ($rueckfrage) {
                $this->aiTask->update([
                    'status' => 'needs_info',
                    'rueckfrage' => $rueckfrage,
                ]);
            } else {
                $this->aiTask->update([
                    'status' => 'completed',
                ]);
            }

        } catch (\Exception $e) {
            $errorDetails = $e->getMessage();
            if (isset($output) && trim($output) !== '' && !str_contains($errorDetails, '--- STDOUT ---')) {
                $errorDetails .= "\n\nCaptured STDOUT before crash:\n" . $output;
            }
            if (isset($errorOutput) && trim($errorOutput) !== '' && !str_contains($errorDetails, '--- STDERR ---')) {
                $errorDetails .= "\n\nCaptured STDERR before crash:\n" . $errorOutput;
            }

            $run->update([
                'status' => 'failed',
                'stderr_log' => $errorDetails,
                'finished_at' => now(),
            ]);
            $this->aiTask->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}
