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

    /**
     * Maximale Anzahl automatischer Wiederholungen bei **RPM**-Fehlern (z.B. 429 Rate Limit pro Minute).
     * Bei täglich erschöpftem Quota greift dieser Wert nicht – der Task wird sofort geparkt.
     */
    private const MAX_TRANSIENT_RETRIES = 3;

    /**
     * Wartezeit in Sekunden vor dem ersten RPM-Retry.
     * Wird exponentiell verdoppelt: 60s → 120s → 240s.
     */
    private const INITIAL_RETRY_DELAY_SECONDS = 60;

    /**
     * Status, den der Task erhält, wenn das tägliche Quota erschöpft ist.
     * Dieser Status wird vom Daemon beim nächsten Start als 'pending' behandelt.
     */
    private const STATUS_QUOTA_PAUSED = 'quota_paused';

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

            /** @var \ITHilbert\LaravelKit\Models\AiTask|null $task */
            // Behandle 'quota_paused' wie 'pending' – wird beim nächsten Run automatisch aufgegriffen
            $task = AiTask::whereIn('status', ['pending', self::STATUS_QUOTA_PAUSED])->first();

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

            $this->executeWithTransientRetry($task, $run, $env, $agentPath);
        }
    }

    /**
     * Führt den Gemini-CLI-Prozess aus und wiederholt bei transienten Fehlern (429 etc.)
     * mit exponentiellem Backoff. Erst nach Ausschöpfen aller Retries wird der Task
     * auf 'failed' gesetzt.
     */
    private function executeWithTransientRetry(AiTask $task, AiTaskRun $run, array $env, string $agentPath): void
    {
        $retryDelay = self::INITIAL_RETRY_DELAY_SECONDS;

        for ($attempt = 1; $attempt <= self::MAX_TRANSIENT_RETRIES; $attempt++) {
            try {
                $this->runGeminiProcess($task, $run, $env, $agentPath, $attempt);

                return; // Erfolg – keine weiteren Versuche nötig
            } catch (DailyQuotaExhaustedException $e) {
                // Täglich erschöpftes Quota – sofort parken, kein Retry sinnvoll
                $this->warn("Task #{$task->id}: Tägliches API-Quota erschöpft. Task wird geparkt und beim nächsten Start automatisch fortgesetzt.");
                $run->update([
                    'status' => 'quota_paused',
                    'stderr_log' => "Tägliches Quota erschöpft (Versuch {$attempt}). Task automatisch geparkt.\n" . $e->getMessage(),
                    'finished_at' => now(),
                ]);
                // WICHTIG: Status 'quota_paused' wird beim Daemon-Start wie 'pending' behandelt!
                $task->update(['status' => self::STATUS_QUOTA_PAUSED]);

                return;
            } catch (TransientApiException $e) {
                $remainingAttempts = self::MAX_TRANSIENT_RETRIES - $attempt;

                if ($remainingAttempts <= 0) {
                    $this->error("Task #{$task->id}: Alle {$attempt} RPM-Retry-Versuche erschöpft. Task wird geparkt.");
                    $run->update([
                        'status' => 'quota_paused',
                        'stderr_log' => "RPM-Fehler nach {$attempt} Versuchen erschöpft:\n" . $e->getMessage(),
                        'finished_at' => now(),
                    ]);
                    $task->update(['status' => self::STATUS_QUOTA_PAUSED]);

                    return;
                }

                $this->warn("Task #{$task->id}: Transient API Fehler (Versuch {$attempt}). Warte {$retryDelay}s vor Retry...");
                $this->warn("Fehlerdetail: " . substr($e->getMessage(), 0, 200));

                // Run-Status auf 'retrying' setzen mit Info
                $run->update([
                    'status' => 'processing',
                    'stderr_log' => "Versuch {$attempt} fehlgeschlagen (transient). Retry in {$retryDelay}s.\n" . $e->getMessage(),
                ]);

                sleep($retryDelay);
                $retryDelay *= 2; // Exponentieller Backoff

            } catch (\Exception $e) {
                // Fataler (nicht-transienter) Fehler → sofort auf 'failed', kein Retry
                $run->update([
                    'status' => 'failed',
                    'stderr_log' => $e->getMessage(),
                    'finished_at' => now(),
                ]);
                $task->update(['status' => 'failed']);
                $this->error("Task #{$task->id} fatal fehlgeschlagen: " . $e->getMessage());

                return;
            }
        }
    }

    /**
     * Führt den eigentlichen Gemini-CLI-Prozess aus.
     *
     * @throws TransientApiException Bei transienten Fehlern (429 Rate Limit, Quota exhausted) die einen Retry erlauben.
     * @throws \Exception Bei fatalen, nicht wiederholbaren Fehlern.
     */
    private function runGeminiProcess(AiTask $task, AiTaskRun $run, array $env, string $agentPath, int $attempt = 1): void
    {
        $prompt = "WICHTIG (SSOT / Pipeline-Modus): Du wurdest für Task ID {$task->id} durch den Daemon gestartet!\n";
        $prompt .= "1. Führe sofort das Laravel Kommando 'php artisan ai:task-read {$task->id}' aus, um deine konkreten fachlichen Instruktionen (Module, Task-Details, Dringlichkeit) aus der Datenbank zu lesen.\n";
        $prompt .= "2. Folge den Vorgaben aus diesem Befehl 1:1.\n";
        $prompt .= "3. Hänge an deine finale System-Ausgabe einen Abschnitt '# Agent Feedback' an. Beschreibe darin kurz: Wo lagen die Probleme? Wie lang dauerte die Suche & das Coden?\n";
        $prompt .= "4. Beende dich bei Erfolg am Ende deines Durchlaufs einfach sauber.\n";
        $prompt .= "- WICHTIG: Gib am Ende deiner Antwort das exakte Keyword 'DAEMON_TASK_FINISHED' als eigene Zeile aus, damit die Pipeline weiß, dass du fertig bist.";

        $processArgs = [
            $agentPath,
            '--yolo',
            '-p',
            $prompt,
        ];

        $targetPkg = strtolower($task->module);
        if (in_array(strtolower($task->module), ['ai', 'dashboard', 'core', 'laravel-kit', 'laravelkit'])) {
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

        $pkgPath = 'vendor/ithilbert/' . $targetPkg;
        if (file_exists(base_path($pkgPath))) {
            $processArgs[] = '--include-directories';
            $processArgs[] = base_path($pkgPath);
        }

        $startTime = microtime(true);
        $output = '';
        $errorOutput = '';

        $process = \Illuminate\Support\Facades\Process::path(base_path())
            ->timeout(3600)
            ->env($env)
            ->start($processArgs, function (string $type, string $buffer) use (&$output, &$errorOutput, $startTime) {
                $elapsed = round(microtime(true) - $startTime, 1);
                if ($type === 'err') {
                    $lines = explode("\n", rtrim($buffer, "\n"));
                    foreach ($lines as $line) {
                        $errorOutput .= "[+{$elapsed}s] " . $line . "\n";
                    }
                } else {
                    $output .= $buffer;
                }
            });

        // Initialer Heartbeat
        $timestamp = now();
        $run->update(['last_heartbeat_at' => $timestamp]);
        $task->update(['last_heartbeat_at' => $timestamp]);

        $lastLogUpdate = time();
        $lastHeartbeatUpdate = time();
        $attemptLabel = $attempt > 1 ? " (Versuch {$attempt})" : '';

        while ($process->running()) {
            $currentTime = time();

            // Update Log every 5 seconds
            if ($currentTime - $lastLogUpdate >= 5) {
                $run->update([
                    'stdout_log' => "Gemini CLI Daemon Run (Streaming...){$attemptLabel}\n\n" . $output,
                    'stderr_log' => $errorOutput,
                ]);
                $lastLogUpdate = $currentTime;
            }

            // Update Heartbeat every 60 seconds
            if ($currentTime - $lastHeartbeatUpdate >= 60) {
                $timestamp = now();
                $run->update(['last_heartbeat_at' => $timestamp]);
                $task->update(['last_heartbeat_at' => $timestamp]);
                $lastHeartbeatUpdate = $currentTime;
            }

            usleep(500000); // 0.5s pause to reduce CPU load

            if (str_contains($output, 'DAEMON_TASK_FINISHED') || str_contains($output, '# FEEDBACK_REQUIRED')) {
                $process->stop(3, 15); // Send SIGTERM
                break;
            }
        }

        $result = $process->wait();

        // Fehler-Klassifizierung: Daily-Quota > Transient > Fatal
        if (! $result->successful() && ! str_contains($output, 'DAEMON_TASK_FINISHED') && ! str_contains($output, '# FEEDBACK_REQUIRED')) {
            if ($this->isDailyQuotaExhausted($errorOutput)) {
                throw new DailyQuotaExhaustedException("Tägliches Quota erschöpft:\n" . $errorOutput);
            }
            if ($this->isTransientError($errorOutput)) {
                throw new TransientApiException("ProcessFailed (Transient/RPM):\n" . $errorOutput);
            }
            throw new \Exception("ProcessFailed (Fatal):\n" . $errorOutput);
        }

        $finalLog = trim($output);

        // Rückfrage check
        $rueckfrage = null;
        if (preg_match('/# FEEDBACK_REQUIRED\s*(.*?)($|# Agent Feedback)/s', $finalLog, $matches)) {
            $rueckfrage = trim($matches[1]);
        }

        // Extract agent feedback
        $agentFeedback = null;
        if (preg_match_all('/# Agent Feedback\s*(.*?)(DAEMON_TASK_FINISHED|$)/s', $finalLog, $matches)) {
            $agentFeedback = trim(end($matches[1]));
        }

        $totalTime = round(microtime(true) - $startTime, 1);
        $run->update([
            'status' => $rueckfrage ? 'needs_info' : 'success',
            'stdout_log' => "Gemini CLI Daemon Run in {$totalTime}s{$attemptLabel}\n\n" . $finalLog,
            'finished_at' => now(),
        ]);

        if ($rueckfrage) {
            $task->update(['status' => 'needs_info', 'rueckfrage' => $rueckfrage, 'agent_feedback' => $agentFeedback]);
        } else {
            $task->update(['status' => 'completed', 'agent_feedback' => $agentFeedback]);
        }

        $this->info("Task #{$task->id} abgeschlossen in {$totalTime}s{$attemptLabel}.");
    }

    /**
     * Erkennt ob das **tägliche** API-Quota erschöpft ist (kein Retry sinnvoll, parken).
     * Erkennungsmerkmal: Gemini CLI selbst hat keine weiteren Retries mehr (`retryDelayMs: undefined`).
     */
    private function isDailyQuotaExhausted(string $errorOutput): bool
    {
        return str_contains($errorOutput, 'retryDelayMs: undefined')
            && str_contains($errorOutput, '429');
    }

    /**
     * Erkennt kurzfristige RPM-Fehler (Rate Limit pro Minute), bei denen ein Retry nach Wartezeit sinnvoll ist.
     */
    private function isTransientError(string $errorOutput): bool
    {
        // Daily-Quota hat Vorrang – nicht als RPM-Fehler behandeln
        if ($this->isDailyQuotaExhausted($errorOutput)) {
            return false;
        }

        $transientPatterns = [
            'RESOURCE_EXHAUSTED',
            'status: 429',
            'Too Many Requests',
            'quota',
            'RetryableQuotaError',
        ];

        foreach ($transientPatterns as $pattern) {
            if (str_contains($errorOutput, $pattern)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Interne Exception für kurzfristige RPM-Fehler (Rate Limit pro Minute), die einen Retry erlauben.
 * Wird nur innerhalb des AiDaemonWorkerCommand verwendet.
 */
class TransientApiException extends \RuntimeException {}

/**
 * Interne Exception für täglich erschöpftes API-Quota.
 * Task wird geparkt ('quota_paused') und beim nächsten Daemon-Start automatisch fortgesetzt.
 * Wird nur innerhalb des AiDaemonWorkerCommand verwendet.
 */
class DailyQuotaExhaustedException extends \RuntimeException {}
