<?php

namespace ITHilbert\LaravelKit\Contracts;

/**
 * Port für den Abruf von Rechtstexten (Impressum, Datenschutz, AGB, Cookie-Richtlinie)
 * aus einem externen Zentralsystem.
 */
interface LegalTextPort
{
    /**
     * Liefert den HTML-Inhalt des angeforderten Rechtstext-Typs.
     *
     * @param  string  $type  Identifier des Rechtstexts, z.B. 'impressum', 'datenschutz', 'agb', 'cookie-richtlinie'
     * @return string|null HTML-Inhalt oder null, wenn die API nicht erreichbar ist (Fallback auf iframe)
     */
    public function fetch(string $type): ?string;
}
