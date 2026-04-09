<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use ITHilbert\LaravelKit\Models\AiTask;
use ITHilbert\LaravelKit\Models\AiTaskRun;

class AiMonitorCommand extends Command
{
    protected $signature = 'ai:monitor';

    protected $description = 'Überwacht laufende AI-Tasks und setzt abgestürzte auf failed.';

    public function handle()
    {
        $this->info('Prüfe auf abgestürzte AI-Tasks...');

        $timeout = now()->subSeconds(120);

        // Tasks mit Status 'running', deren Heartbeat älter als 120s ist
        $zombieTasks = AiTask::where('status', 'running')
            ->where(function ($query) use ($timeout) {
                $query->where('last_heartbeat_at', '<', $timeout)
                    ->orWhereNull('last_heartbeat_at');
            })
            ->get();

        foreach ($zombieTasks as $task) {
            $this->warn("Task #{$task->id} ist ein Zombie (kein Heartbeat > 120s). Setze auf failed.");

            $task->update([
                'status' => 'failed',
                'agent_feedback' => 'Task durch ai:monitor als failed markiert (Heartbeat Timeout).',
            ]);

            // Auch den letzten Run auf failed setzen
            $lastRun = AiTaskRun::where('ai_task_id', $task->id)
                ->where('status', 'processing')
                ->latest()
                ->first();

            if ($lastRun) {
                $lastRun->update([
                    'status' => 'failed',
                    'stderr_log' => "Abgebrochen durch Monitor: Kein Heartbeat seit 120 Sekunden.\n".$lastRun->stderr_log,
                    'finished_at' => now(),
                ]);
            }
        }

        $this->info("Prüfung abgeschlossen. {$zombieTasks->count()} Tasks bereinigt.");
    }
}
