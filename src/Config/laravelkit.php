<?php

return [
    'name' => 'LaravelKit',

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
