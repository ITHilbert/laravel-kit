<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AuditFetchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:fetch {audit_paper_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Holt den reinen Fach-Text der aktuellsten Revision eines Audit-Papers und speichert ihn als storage/audit/paper.md.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $auditPaperId = $this->argument('audit_paper_id');

        // IDs aus active_audit.json laden, wenn kein Parameter gesetzt
        $activeAuditPath = storage_path('audit/active_audit.json');
        if (! $auditPaperId && File::exists($activeAuditPath)) {
            $data = json_decode(File::get($activeAuditPath), true);
            $auditPaperId = $data['audit_paper_id'] ?? null;
        }

        if (! $auditPaperId) {
            $this->error('Keine audit_paper_id angegeben und keine aktive Konfiguration (active_audit.json) gefunden.');

            return Command::FAILURE;
        }

        $apiUrl = config('services.auditai.url', env('AUDITAI_URL', 'http://localhost'));
        $apiToken = config('services.auditai.api_token', env('AUDITAI_API_TOKEN', ''));

        if (empty($apiToken)) {
            $this->error('Kein AUDITAI_API_TOKEN konfiguriert.');

            return Command::FAILURE;
        }

        $this->info("Lade Revision-Text für Paper #{$auditPaperId} von {$apiUrl}...");

        $response = Http::withToken($apiToken)->get("{$apiUrl}/api/v1/audit/papers/{$auditPaperId}");

        if ($response->failed()) {
            $this->error('Fehler beim Abrufen des Papers: '.$response->body());

            return Command::FAILURE;
        }

        $paper = $response->json('data');
        $revisions = $paper['revisions'] ?? [];

        if (empty($revisions)) {
            $this->error('Das Audit-Paper hat noch keine Revisionen.');

            return Command::FAILURE;
        }

        // Neueste Revision (höchste ID)
        usort($revisions, fn ($a, $b) => $b['id'] <=> $a['id']);
        $latestRevision = $revisions[0];

        $content = $latestRevision['content'] ?? '';

        if (empty($content)) {
            $this->warn('Die aktuellste Revision enthält keinen Text.');
        }

        // Zielverzeichnis und Datei sicherstellen
        $auditDir = storage_path('audit');
        if (! File::isDirectory($auditDir)) {
            File::makeDirectory($auditDir, 0755, true);
        }

        $filePath = storage_path('audit/paper.md');
        File::put($filePath, $content);

        $this->info("✅ Revision #{$latestRevision['id']} gespeichert unter: storage/audit/paper.md");

        return Command::SUCCESS;
    }
}
