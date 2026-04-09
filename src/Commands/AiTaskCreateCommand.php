<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use ITHilbert\LaravelKit\Jobs\Ai\RunGeminiTaskJob;
use ITHilbert\LaravelKit\Jobs\Ai\RunPhpUnitJob;
use ITHilbert\LaravelKit\Models\AiTask;

class AiTaskCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:task-create {title} {module} {description} {--urgent : Ob der Task hohe Priorität hat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pusht einen neuen Task elegant in die AI Pipeline Queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $title = $this->argument('title');
        $module = $this->argument('module');
        $description = $this->argument('description');
        $urgent = $this->option('urgent');

        try {
            $task = AiTask::create([
                'title' => $title,
                'description' => $description,
                'module' => $module,
                'status' => 'pending',
            ]);

            $chain = Bus::chain([
                new RunGeminiTaskJob($task, 1),
                new RunPhpUnitJob($task, 1),
            ]);

            if ($urgent) {
                $chain->onQueue('ai_pipeline_high');
            } else {
                $chain->onQueue('ai_pipeline');
            }

            $chain->dispatch();

            $this->info("Erfolg: Task #{$task->id} ('{$title}') wurde in die Queue übergeben!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Fehler beim Erstellen des AI-Tasks: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
