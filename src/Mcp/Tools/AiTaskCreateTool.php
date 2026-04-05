<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use ITHilbert\LaravelKit\Models\AiTask;
use Illuminate\Support\Facades\Bus;
use ITHilbert\LaravelKit\Jobs\Ai\RunCursorBuilderJob;
use ITHilbert\LaravelKit\Jobs\Ai\RunPhpUnitJob;
use ITHilbert\LaravelKit\Jobs\Ai\RunCriticReviewJob;

#[Description('Erstellt einen neuen autonomen AI-Task (Cursor -> PHPUnit -> Critic) in der ai_pipeline Queue.')]
class AiTaskCreateTool extends Tool
{
    public function handle(Request $request): Response
    {
        $title = $request->input('title');
        $description = $request->input('description');
        $module = $request->input('module', 'Core');
        $dependsOn = $request->input('depends_on_task_id');

        try {
            $task = AiTask::create([
                'title' => $title,
                'description' => $description,
                'module' => $module,
                'depends_on_task_id' => $dependsOn,
                'status' => 'pending',
            ]);

            // Dispatch the Job Chain
            Bus::chain([
                new RunCursorBuilderJob($task, 1),
                new RunPhpUnitJob($task, 1),
                new RunCriticReviewJob($task, 1),
            ])->dispatch();

            return Response::text("Erfolg! AI-Task #{$task->id} wurde in die Queue eingereiht.");
        } catch (\Exception $e) {
            return Response::text("Fehler beim Erstellen des AI-Tasks: " . $e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Kurzer Titel der Aufgabe')->required(),
            'description' => $schema->string()->description('Ausführliche Instruktionen für Cursor')->required(),
            'module' => $schema->string()->description('Betroffenes Modul (z.B. Invoice, Frontend) oder Core')->required(),
            'depends_on_task_id' => $schema->integer()->description('ID des vorherigen Tasks (falls dieser Task auf einem anderen aufbaut)')->nullable(),
        ];
    }
}
