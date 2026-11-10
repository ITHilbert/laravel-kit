<?php

return [
    'name' => 'LaravelKit',

    /*
    |--------------------------------------------------------------------------
    | Theme der Kit-Components (<x-kit::…>)
    |--------------------------------------------------------------------------
    |
    | Bestimmt das Markup aller Kit-Components. Erlaubt: tailwind, bootstrap5.
    | Ein anderer oder leerer Wert führt beim Rendern zu einer Exception.
    |
    */
    'theme' => env('LARAVELKIT_THEME', 'tailwind'),

    /*
    |--------------------------------------------------------------------------
    | Fehleranzeige der Formular-Components
    |--------------------------------------------------------------------------
    |
    | Standardmäßig aus. Pro Feld mit dem Attribut `fehler` (:fehler="false" schaltet aus)
    | oder für alle Felder mit true. Ein Attribut error="Text" aktiviert das jeweilige Feld.
    |
    */
    'fehleranzeige' => filter_var(env('LARAVELKIT_FEHLERANZEIGE', false), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Log-Tabelle
    |--------------------------------------------------------------------------
    |
    | Standardmäßig an: Die Migration der Tabelle `log` wird geladen. Mit false
    | (Env LARAVELKIT_LOG_TABELLE=false) wird nur diese Migration übersprungen.
    |
    */
    'log_tabelle' => filter_var(env('LARAVELKIT_LOG_TABELLE', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Rechtstexte (Impressum, Datenschutz, AGB, Cookie-Richtlinie)
    |--------------------------------------------------------------------------
    |
    | Basis-URL des Zentralsystems, von dem Rechtstexte per REST-API abgerufen
    | werden. Kann in der Projekt-.env via config() überschrieben werden.
    |
    */
    'legal_text_base_url' => env('LARAVELKIT_LEGAL_TEXT_BASE_URL', 'https://center.it-hilbert.com'),
    'legal_text_timeout' => env('LARAVELKIT_LEGAL_TEXT_TIMEOUT', 5),

    'breadcrumb' => [
        'root_name' => env('APP_NAME'),
    ],

    'backup_seeders' => [
        'ignore_tables' => [
            'jobs',
            'failed_jobs',
            'job_batches',
            'cache',
            'cache_locks',
            'sessions',
            'migrations',
            'personal_access_tokens',
            'password_reset_tokens',
            'pulse_aggregates',
            'pulse_entries',
            'pulse_values',
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
        ],
    ],
];
