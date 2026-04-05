<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use ITHilbert\LaravelKit\Models\AiTask;

#[Description('Fragt den Status der AI-Tasks ab (Queue-Radar) um Stale-States zu vermeiden.')]
class AiTaskStatusTool extends Tool
{
    public function handle(Request $request): Response
    {
        try {
            $openTasks = AiTask::whereIn('status', ['pending', 'running'])->count();
            $recentFailures = AiTask::where('status', 'failed')->orderBy('id', 'desc')->take(3)->get();
            
            $msg = "Aktuell offene Tasks in der Queue: {$openTasks}\n";
            
            if ($openTasks > 0) {
                $msg .= "WARNUNG: Es gibt noch abzuarbeitende Tasks! Bitte plane keine tiefen Architekturänderungen, bis diese abgeschlossen sind.\n";
            }

            if ($recentFailures->isNotEmpty()) {
                $msg .= "\nLetzte fehlgeschlagene Tasks:\n";
                foreach ($recentFailures as $task) {
                    $msg .= "- Task #{$task->id}: {$task->title}\n";
                }
            }

            return Response::text($msg);
        } catch (\Exception $e) {
            return Response::text("Fehler beim Abrufen des Status: " . $e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return []; // Keine Parameter benötigt
    }
}
