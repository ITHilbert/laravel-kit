<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaravelKitBackupSeeders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-seeders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sichert alle Kern-Tabellen über iseed in Seeder-Dateien (Globales Kit Command)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Alle Tabellen aus der Datenbank abrufen
        $connection = config('database.default');
        
        $tables = [];
        try {
            $tables = Schema::connection($connection)->getTableListing();
        } catch (\Exception $e) {
            // Fallback für ältere Laravel Versionen
            $tables = DB::connection($connection)->getDoctrineSchemaManager()->listTableNames();
        }

        // Ignorierte Tabellen laden
        // Wenn keine Config gefunden wird, nehmen wir ein leeres Array
        $ignoreTables = config('laravelkit.backup_seeders.ignore_tables', []);

        $exportTables = [];
        foreach ($tables as $table) {
            if (!in_array($table, $ignoreTables)) {
                $exportTables[] = $table;
            }
        }

        if (count($exportTables) === 0) {
            $this->warn('Keine Tabellen zum Exportieren gefunden.');
            return;
        }

        $this->info('Starte Export für: ' . count($exportTables) . ' Tabellen...');

        $tableString = implode(',', $exportTables);

        $exitCode = Artisan::call('iseed', [
            'tables' => $tableString,
            '--force' => true,
        ]);

        if ($exitCode === 0) {
            $this->info(Artisan::output());
            $this->info('✅ Alle Tabellen erfolgreich in Database/Seeders exportiert.');
        } else {
            $this->error('❌ Es gab einen Fehler beim Exportieren der Tabellen.');
            $this->error(Artisan::output());
        }
    }
}
