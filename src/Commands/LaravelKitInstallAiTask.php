<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;

class LaravelKitInstallAiTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:ai-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installiert und konfiguriert die AI Task Pipeline (Configs, Migrations, Gemini CLI, ENV)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Publishing Konfigurationsdateien (ai.php, laravelkit.php etc.)...');
        exec('php artisan vendor:publish --provider="ITHilbert\LaravelKit\LaravelKitServiceProvider" --tag="config"');

        $this->info('Starte Datenbank-Migrationen für Task-Tabellen...');
        exec('php artisan migrate');

        $this->info('Installiere lokales NPM Paket für @gemini-cli/gemini...');
        exec('npm install -D @gemini-cli/gemini');

        $this->info('Konfiguriere .env für Daemon Ausführung...');
        $this->updateEnvironmentFile();

        $this->info('===============================================');
        $this->info('AI Task Pipeline Installation erfolgreich abgeschlossen!');
        $this->info('Bitte vergiss nicht deinen GEMINI_API_KEY in der .env zu setzen,');
        $this->info('und danach den AI-Worker mit `php artisan ai:daemon` zu starten.');
        $this->info('===============================================');

        return 0;
    }

    /**
     * Updated die .env Datei wenn der Ausführungsmodus fehlt.
     */
    protected function updateEnvironmentFile()
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            if (strpos($envContent, 'AI_EXECUTION_MODE=') === false) {
                file_put_contents($envPath, $envContent . "\nAI_EXECUTION_MODE=daemon\n");
                $this->info(' -> AI_EXECUTION_MODE=daemon erfolgreich in .env eingetragen.');
            } else {
                $this->info(' -> AI_EXECUTION_MODE existiert bereits in der .env.');
            }
        }
    }
}
