<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AuditFeedbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:feedback {audit_paper_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Holt das KI-Feedback für ein Audit-Paper. Startet ggf. einen neuen Audit-Run und speichert das Ergebnis als storage/audit/feedback.md.';

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

        // Schritt 1: Prüfen, ob bereits ein erfolgreiches Feedback vorhanden ist
        $this->info("Prüfe ob bereits KI-Feedback für Paper #{$auditPaperId} vorhanden ist...");

        $feedbackResponse = Http::withToken($apiToken)
            ->get("{$apiUrl}/api/v1/audit/papers/{$auditPaperId}/feedback");

        if ($feedbackResponse->status() === 404) {
            // Kein Feedback vorhanden → Audit-Run starten
            $this->warn('Kein Feedback vorhanden. Starte neuen Audit-Run...');

            $runExitCode = $this->call('audit:run', ['audit_paper_id' => $auditPaperId]);

            if ($runExitCode !== Command::SUCCESS) {
                $this->error('Audit-Run fehlgeschlagen. Breche ab.');

                return Command::FAILURE;
            }

            // Erneut Feedback abfragen nach erfolgreichem Run
            $feedbackResponse = Http::withToken($apiToken)
                ->get("{$apiUrl}/api/v1/audit/papers/{$auditPaperId}/feedback");
        }

        if ($feedbackResponse->failed()) {
            $this->error('Fehler beim Abrufen des Feedbacks: '.$feedbackResponse->body());

            return Command::FAILURE;
        }

        $feedbackData = $feedbackResponse->json('data');
        $findings = $feedbackData['findings']['data'] ?? $feedbackData['findings'] ?? [];

        // Schritt 2: Findings als Markdown-Report formatieren
        $markdown = "# 🤖 KI-Audit Feedback\n\n";
        $markdown .= '**Run ID:** '.($feedbackData['run_id'] ?? '—')."\n";
        $markdown .= '**Revision ID:** '.($feedbackData['revision_id'] ?? '—')."\n";
        $markdown .= '**Status:** '.($feedbackData['status'] ?? '—')."\n\n";
        $markdown .= "---\n\n";

        if (empty($findings)) {
            $markdown .= "✅ Keine Findings — Architektur sieht gut aus!\n";
        } else {
            foreach ($findings as $finding) {
                $severity = strtoupper($finding['severity'] ?? 'INFO');
                $type = $finding['type'] ?? 'unknown';
                $title = $finding['title'] ?? 'Kein Titel';
                $description = $finding['description'] ?? '';
                $recommendation = $finding['recommendation'] ?? '';

                $markdown .= "## [{$severity}] {$title}\n\n";
                $markdown .= "**Typ:** `{$type}`\n\n";
                $markdown .= $description."\n\n";

                if (! empty($recommendation)) {
                    $markdown .= "**💡 Empfehlung:**\n\n".$recommendation."\n\n";
                }

                $markdown .= "---\n\n";
            }
        }

        // Schritt 3: Speichern
        $auditDir = storage_path('audit');
        if (! File::isDirectory($auditDir)) {
            File::makeDirectory($auditDir, 0755, true);
        }

        $filePath = storage_path('audit/feedback.md');
        File::put($filePath, $markdown);

        $findingCount = count($findings);
        $this->info("✅ Feedback mit {$findingCount} Finding(s) gespeichert unter: storage/audit/feedback.md");

        return Command::SUCCESS;
    }
}
