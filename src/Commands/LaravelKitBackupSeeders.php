<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaravelKitBackupSeeders extends Command
{
    private const SPERRE_MARKIERUNG = '// laravelkit:seed-sperre';

    protected $signature = 'db:backup-seeders';

    protected $description = 'Sichert alle Kern-Tabellen über iseed in Seeder-Dateien (Globales Kit Command)';

    public function handle(): int
    {
        $tabellen = $this->exportTabellen();

        if (count($tabellen) === 0) {
            $this->warn('Keine Tabellen zum Exportieren gefunden.');

            return self::SUCCESS;
        }

        $this->info('Starte Export für: '.count($tabellen).' Tabellen...');

        $fehler = [];
        foreach ($tabellen as $tabelle) {
            $sortierung = $this->sortierSpalte($tabelle);

            $exitCode = Artisan::call('iseed', [
                'tables' => $tabelle,
                '--force' => true,
                '--orderby' => $sortierung,
                '--skip-fk-checks' => true,
            ]);

            if ($exitCode !== 0) {
                $fehler[] = $tabelle;
                $this->error("❌ Fehler beim Exportieren der Tabelle {$tabelle}.");
                $this->error(trim(Artisan::output()));
            }
        }

        $this->sperreEinfuegen();

        if ($fehler !== []) {
            $this->error('Fehlgeschlagen: '.implode(', ', $fehler));

            return self::FAILURE;
        }

        $this->info('✅ Alle Tabellen erfolgreich in Database/Seeders exportiert.');

        return self::SUCCESS;
    }

    /** @return array<int, string> */
    private function exportTabellen(): array
    {
        $connection = config('database.default');
        $dbName = DB::connection($connection)->getDatabaseName();
        $ignore = config('laravelkit.backup_seeders.ignore_tables', []);

        $export = [];
        foreach (Schema::connection($connection)->getTableListing() as $tabelle) {
            $name = $tabelle;

            if (str_contains($tabelle, '.')) {
                [$schema, $name] = explode('.', $tabelle, 2);
                // Tabellen fremder Datenbanken überspringen; "main" ist das SQLite-Schema
                if ($schema !== $dbName && $schema !== 'main') {
                    continue;
                }
            }

            if (! in_array($name, $ignore, true)) {
                $export[] = $name;
            }
        }

        return $export;
    }

    private function sortierSpalte(string $tabelle): string
    {
        $schema = Schema::connection(config('database.default'));

        foreach ($schema->getIndexes($tabelle) as $index) {
            if (($index['primary'] ?? false) && count($index['columns']) === 1) {
                return $index['columns'][0];
            }
        }

        $erste = $schema->getColumnListing($tabelle)[0] ?? 'id';
        $this->warn("Tabelle {$tabelle}: kein einfacher Primärschlüssel, sortiert nach Spalte {$erste}.");

        return $erste;
    }

    private function sperreEinfuegen(): void
    {
        $pfad = database_path('seeders/DatabaseSeeder.php');

        if (! is_file($pfad)) {
            $this->warn('DatabaseSeeder.php nicht gefunden – Sperre für db:seed nicht eingefügt.');

            return;
        }

        $inhalt = (string) file_get_contents($pfad);

        if (str_contains($inhalt, self::SPERRE_MARKIERUNG)) {
            return;
        }

        $sperre = <<<'PHP'

        // laravelkit:seed-sperre
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('db:seed ist in der Umgebung "'.app()->environment().'" gesperrt: iseed-Seeder löschen vorhandene Daten.');
        }

PHP;

        $neu = preg_replace('/(function\s+run\s*\([^)]*\)\s*(?::\s*\w+\s*)?\{\R)/', '$1'.addcslashes($sperre, '\\$'), $inhalt, 1, $anzahl);

        if ($anzahl !== 1 || $neu === null) {
            $this->warn('DatabaseSeeder.php: run() nicht gefunden – Sperre nicht eingefügt.');

            return;
        }

        file_put_contents($pfad, $neu);
        $this->info('Sperre in DatabaseSeeder.php eingefügt.');
    }
}
