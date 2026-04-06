<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Process;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Zieht eine zweite Meinung von Codex ein. Führt den lokalen Codex CLI für analytische Code- oder Architektur-Reviews aus.')]
class CodexReviewTool extends Tool
{
    public function handle(Request $request): Response
    {
        $prompt = $request->input('prompt');
        $file = $request->input('file');
        
        $command = env('AI_CODEX_CMD', 'codex');
        
        $args = [$command];
        
        if (!empty($file)) {
            $prompt .= "\nKontext/Datei: " . $file;
        }
        
        $args[] = $prompt;

        try {
            // Process::run erwartet ein array von argumenten oder einen string
            // base_path() stellt sicher, dass wir uns im aktuellen Laravel-Root befinden
            $result = Process::path(base_path())->run($args);

            if ($result->successful()) {
                return Response::text("--- Codex CLI Review ---\n\n" . $result->output());
            }

            return Response::text("Fehler bei der Ausführung von Codex CLI: \n" . $result->errorOutput());
        } catch (\Exception $e) {
            return Response::text("Systemfehler beim Aufruf der Codex CLI (Ist es installiert?): " . $e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'prompt' => $schema->string()->description('Die Anweisung oder Architektur-Frage an Codex')->required(),
            'file' => $schema->string()->description('Absoluter oder relativer Dateipfad zum Kontext (wird an Prompt angehängt)')->nullable(),
        ];
    }
}
