<?php

namespace ITHilbert\LaravelKit\Infrastructure\Adapters;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;

/**
 * Holt Rechtstexte (Impressum, Datenschutz, AGB, Cookie-Richtlinie) über die
 * REST-API des IT-Hilbert Center-Systems.
 *
 * Basis-URL und Timeout sind über config('laravelkit.legal_text_base_url') und
 * config('laravelkit.legal_text_timeout') überschreibbar — das macht den Adapter testbar.
 */
class CenterLegalTextAdapter implements LegalTextPort
{
    /**
     * {@inheritDoc}
     */
    public function fetch(string $type): ?string
    {
        $baseUrl = rtrim(config('laravelkit.legal_text_base_url', 'https://center.it-hilbert.com'), '/');
        $timeout = (int) config('laravelkit.legal_text_timeout', 5);

        try {
            $response = Http::timeout($timeout)->get("{$baseUrl}/api/{$type}");

            if (! $response->successful()) {
                Log::warning('LegalTextAdapter: API antwortete mit Fehler-Status.', [
                    'type' => $type,
                    'status' => $response->status(),
                ]);

                return null;
            }

            $content = $response->json('content');

            if (empty($content)) {
                Log::warning('LegalTextAdapter: API-Antwort enthält kein "content"-Feld.', [
                    'type' => $type,
                ]);

                return null;
            }

            return (string) $content;
        } catch (ConnectionException $e) {
            Log::warning('LegalTextAdapter: Verbindung zur API fehlgeschlagen.', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return null;
        } catch (RequestException $e) {
            Log::warning('LegalTextAdapter: HTTP-Request-Fehler bei der API.', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
