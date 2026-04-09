<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use ITHilbert\LaravelKit\Models\AiTask;

class AiTaskReadCommand extends Command
{
    protected $signature = 'ai:task-read {id : Die ID des abzurufenden AI-Tasks}';

    protected $description = 'Liest einen Task und formatiert ihn als System-Prompt für die Gemini CLI aus.';

    public function handle()
    {
        $id = $this->argument('id');

        $task = AiTask::findOrFail($id);

        $this->info("# Task #{$task->id}: {$task->title}");
        $this->info("## Module: {$task->module}");
        if ($task->is_urgent) {
            $this->warn('## Dringlichkeit: URGENT / HIGH PRIORITY');
        }

        $this->line('');
        $this->info('## Arbeitsauftrag / Instruktionen:');
        $this->line($task->description);

        $this->line('');
        $this->info('## System Rules (Automatisierte Pipeline):');
        $this->line('- Führe keine Dialoge und stelle keine Fragen.');
        $this->line('- Aktiviere unter keinen Umständen den Planning Mode.');
        $this->line('- Warte auf KEIN User-Okay, sondern setze alle Schritte blind um.');
        $this->line("- Falls eine Rückfrage/Klarstellung absolut zwingend ist, trage das Feedback via 'php artisan ai:task-feedback {$task->id}' ein und beende den Prozess (FAILED).");

        return Command::SUCCESS;
    }
}
