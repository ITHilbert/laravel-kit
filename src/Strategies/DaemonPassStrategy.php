<?php

namespace ITHilbert\LaravelKit\Strategies;

use Closure;
use ITHilbert\LaravelKit\Contracts\AiExecutionStrategy;

class DaemonPassStrategy implements AiExecutionStrategy
{
    public function execute($aiTask, $run, Closure $failClosure)
    {
        // Wir markieren den Run als bereit für den Daemon.
        $run->update([
            'status' => 'queued_for_daemon',
            'stdout_log' => "Task wurde an den dauerhaft laufenden Daemon übergeben (DaemonPassStrategy).\nDies eliminiert den 30s Cold Boot der Gemini API.",
        ]);
        
        // WICHTIG: Die ai_tasks.status bleibt 'pending', da der Daemon nach 'pending' sucht.
        // Der Daemon wird die Aufgabe aufgreifen, den Run ggf. übernehmen und den Status auf 'running' ändern.
    }
}
