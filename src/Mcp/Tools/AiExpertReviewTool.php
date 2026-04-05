<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Evaluiert als Senior Architekt einen Code-Draft über die OpenAI API auf Clean Code, Pattern und Security.')]
class AiExpertReviewTool extends Tool
{
    public function handle(Request $request): Response
    {
        $content = $request->input('content');
        
        $apiKey = config('laravelkit.ai.openai_api_key');
        $model = config('laravelkit.ai.model', 'gpt-4o');

        if (empty($apiKey)) {
            return Response::text("Fehler: OPENAI_API_KEY fehlt in der .env des Projekts.");
        }

        $systemPrompt = "Du bist ein rigoroser Senior Enterprise Architekt und Laravel-Experte. Ein Unter-Agent (AI) wird dir gleich einen Architekturentwurf oder Code präsentieren. Prüfe diesen strikt auf Clean Architecture, Entkopplung, SOLID-Prinzipien und Sicherheitslücken. Antworte ausschließlich auf Deutsch. Mache klare Verbesserungsvorschläge, lobe nicht unnötig, sondern sei präzise und lösungsorientiert in deiner Kritik.";

        try {
            $response = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $content],
                ],
                'temperature' => 0.2,
            ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content');
                return Response::text("--- ChatGPT / OpenAI Architektur Review ---\n\n" . $reply);
            }

            return Response::text("API Fehler bei der Evaluierung: " . $response->body());
        } catch (\Exception $e) {
            return Response::text("Verbindungsfehler zur OpenAI API: " . $e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'content' => $schema->string()->description('Der zu prüfende Code, Plan oder Architektur-Draft')->required(),
        ];
    }
}
