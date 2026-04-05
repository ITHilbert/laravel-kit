<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AiWatcherCommand extends Command
{
    protected $signature = 'ai:watch';
    protected $description = 'Startet den Queue-Worker für die asynchronen AI-Tasks (ai_pipeline queue).';

    public function handle()
    {
        $this->info('Starting AI Watcher...');
        
        $dbPath = storage_path('devtools_ai.sqlite');
        if (!file_exists($dbPath)) {
            touch($dbPath);
            $this->info('Created missing devtools_ai.sqlite file.');
            
            // Note: In real app, standard migrations path would be used or we specifically run the package one
            // Handled automatically if user runs artisan migrate.
        }

        $this->info('Listening for AI Tasks on queues: [ai_pipeline_high, ai_pipeline]...');
        
        // Wir übergeben das Terminal an den native queue worker, damit Logs live gestreamt werden
        passthru('php artisan queue:work --queue=ai_pipeline_high,ai_pipeline --stop-when-empty=false');
        
        return Command::SUCCESS;
    }
}
