<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use ITHilbert\LaravelKit\Models\AiTask;
use ITHilbert\LaravelKit\Jobs\Ai\RunPhpUnitJob;

class AiUpdateTaskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:task-status {id} {status} {--log=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aktualisiert den Status eines AI-Tasks (für Aufrufe durch CLI-Bots gedacht)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $status = $this->argument('status');
        $log = $this->option('log');

        $task = AiTask::find($id);

        if (!$task) {
            $this->error("Task #{$id} nicht gefunden.");
            return Command::FAILURE;
        }

        $task->update(['status' => $status]);
        
        $this->info("Task #{$id} wurde erfolgreich auf Status '{$status}' gesetzt.");

        if ($log) {
            $this->info("Log gespeichert (Optionales Handling).");
            // Optional: Log an den Task anhängen
        }

        // Wenn der Bot DONE/completed meldet, können wir den nächsten Step der Pipeline zünden!
        if (in_array(strtolower($status), ['done', 'completed', 'success'])) {
            $this->info("Zünde RunPhpUnitJob für Task #{$id}...");
            RunPhpUnitJob::dispatch($task, 1);
        }

        return Command::SUCCESS;
    }
}
