<?php

namespace ITHilbert\LaravelKit\Classes;

use Illuminate\Support\Facades\Config;

class AiModelResolver
{
    /**
     * Löst das zu verwendende KI-Modell, die Version und den Provider für einen gegebenen Prozess auf.
     *
     * Beispiel: AiModelResolver::resolve('audit') liefert das konfigurierte Modell für "audit".
     *
     * @param string $processType Der Prozesstyp (z. B. 'audit', 'revision', 'code_generation')
     * @param string|null $provider Optional: Überschreibt den Provider (z. B. 'openai', 'gemini')
     * @return array{provider: string, model: string, version: string|null}|null
     */
    public static function resolve(string $processType, ?string $provider = null): ?array
    {
        // 1. Provider bestimmen
        // Wenn kein Provider explizit übergeben wurde, prüfen wir, ob es auf Root-Ebene
        // in config/ai.php einen expliziten Override gibt (z. B. config('ai.audit')).
        // Ansonsten fällt es auf den 'default_provider' zurück.
        if ($provider === null) {
            $provider = Config::get('ai.' . $processType, Config::get('ai.default_provider', 'gemini'));
        }

        if (empty($provider)) {
            return null;
        }

        // 2. Modellspezifische Konfiguration laden
        $modelConfig = Config::get("ai.{$provider}.models.{$processType}");

        if (!$modelConfig) {
            return null; // Für diesen Prozess ist in diesem Provider kein Modell hinterlegt
        }

        // 3. Return der gesammelten Daten
        return [
            'provider' => $provider,
            'model'    => $modelConfig['model'] ?? null,
            'version'  => $modelConfig['version'] ?? null,
        ];
    }
}
