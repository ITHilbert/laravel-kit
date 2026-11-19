<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use ITHilbert\LaravelKit\Logs\LogDateienLoeschen;

class LogPruneCommand extends Command
{
    protected $signature = 'log:prune
                            {--days= : Dateien älter als N Tage löschen (Standard: laravelkit.logs.tage, 30)}
                            {--dry-run : Nur anzeigen, nichts löschen}';

    protected $description = 'Löscht .log-Dateien in storage/logs, die älter als N Tage sind';

    public function handle(LogDateienLoeschen $loeschen): int
    {
        $tage = $this->tage();

        if ($tage === null) {
            $this->error('--days muss eine ganze Zahl größer 0 sein.');

            return self::FAILURE;
        }

        $trocken = (bool) $this->option('dry-run');
        $ergebnis = $loeschen->ausfuehren(storage_path('logs'), $tage, $trocken);
        $anzahl = count($ergebnis['geloescht']);

        foreach ($ergebnis['geloescht'] as $datei) {
            $this->line(($trocken ? 'würde gelöscht: ' : 'gelöscht: ').$datei);
        }

        foreach ($ergebnis['fehlgeschlagen'] as $datei) {
            $this->warn('Nicht löschbar: '.$datei);
        }

        if ($anzahl === 0 && $ergebnis['fehlgeschlagen'] === []) {
            $this->info('nichts zu tun');
        } elseif ($anzahl === 1) {
            $this->info('1 Datei '.($trocken ? 'würde gelöscht' : 'gelöscht'));
        } else {
            $this->info($anzahl.' Dateien '.($trocken ? 'würden gelöscht' : 'gelöscht'));
        }

        return $ergebnis['fehlgeschlagen'] === [] ? self::SUCCESS : self::FAILURE;
    }

    private function tage(): ?int
    {
        $wert = $this->option('days');

        if ($wert === null || $wert === '') {
            $wert = config('laravelkit.logs.tage');
            if ($wert === null || $wert === '') {
                return 30;
            }
        }

        $wert = (string) $wert;

        return ctype_digit($wert) && (int) $wert > 0 ? (int) $wert : null;
    }
}
