<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AuditRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:run {audit_paper_id?} {audit_revision_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starte einen AI-Audit via AuditAI Zentralsystem.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $auditPaperId = $this->argument('audit_paper_id');
        $auditRevisionId = $this->argument('audit_revision_id');

        // Versuch IDs aus active_audit.json zu laden, wenn keine Parameter gesetzt wurden
        $activeAuditPath = storage_path('audit/active_audit.json');
        if (! $auditPaperId && File::exists($activeAuditPath)) {
            $data = json_decode(File::get($activeAuditPath), true);
            $auditPaperId = $data['audit_paper_id'] ?? null;
            $auditRevisionId = $data['audit_revision_id'] ?? null;
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

        $this->info("Starte Audit-Lauf für Paper #{$auditPaperId} / Revision #".($auditRevisionId ?? 'NEWEST')." via {$apiUrl}");

        // Endpoint: Startet Run (z.B. POST /api/v1/audit/revisions/{id}/runs)
        // Wenn Revision nicht angegeben, müssen wir die neueste herausfinden

        // Da das Backend z.Zt. so aufgebaut ist, dass man revision_id für den Run braucht,
        // implementieren wir hier eine weiche Fallback-Logik oder senden die PaperId und das Backend löst es auf.
        $postUrl = "{$apiUrl}/api/v1/audit/runs";

        $response = Http::withToken($apiToken)->post($postUrl, [
            'audit_paper_id' => $auditPaperId,
            'audit_revision_id' => $auditRevisionId,
        ]);

        if ($response->failed()) {
            $this->error('Fehler beim Starten des Audits: '.$response->body());

            return Command::FAILURE;
        }

        $run = $response->json('data');
        $runId = $run['id'];

        $this->info("Audit Run #{$runId} wurde gestartet. Warte auf KI-Antwort...");

        // Polling loop
        $status = 'pending';
        $bar = $this->output->createProgressBar(100);
        $bar->start();
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

        while (in_array($status, ['pending', 'processing'])) {
            sleep(2);
            $pollResponse = Http::withToken($apiToken)->get("{$apiUrl}/api/v1/audit/runs/{$runId}");

            if ($pollResponse->failed()) {
                $this->error('Fehler beim Polling: '.$pollResponse->body());

                return Command::FAILURE;
            }

            $status = $pollResponse->json('data.status');
            $bar->setMessage("Status: {$status}");
            $bar->advance(5);
        }

        $bar->finish();
        $this->newLine();

        if ($status === 'success') {
            $this->info('Audit erfolgreich abgeschlossen!');

            // Hole Findings
            $findingsResponse = Http::withToken($apiToken)->get("{$apiUrl}/api/v1/audit/runs/{$runId}/findings");
            $findings = $findingsResponse->json('data') ?? [];

            if (count($findings) === 0) {
                $this->info('Keine Findings gefunden. Die Architektur sieht gut aus!');
            } else {
                $headers = ['Type', 'Severity', 'Title', 'Recommendation'];
                $rows = array_map(function ($f) {
                    return [
                        $f['type'],
                        $f['severity'],
                        $f['title'],
                        wordwrap($f['recommendation'] ?? '', 50, "\n", true),
                    ];
                }, $findings);

                $this->table($headers, $rows);
            }
        } else {
            $this->error("Audit fehlgeschlagen mit Status: {$status}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
