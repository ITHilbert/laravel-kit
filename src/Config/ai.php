<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | Hier werden alle KI-bezogenen Einstellungen für das System gebündelt,
    | um sie nicht in jedem Modul (Datenschutz, Impressum, Cookie-Richtlinie)
    | separat pflegen zu müssen.
    |
    */

    'default_provider' => env('DEFAULT_AI_PROVIDER', 'gemini'),

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),

        // Unterstützte Versionen & Modelle zur Auswahl im Backend (später für UI)
        'allowed_versions' => ['v1', 'v1beta'],
        'allowed_models' => [
            'gemini-2.5-flash',
            'gemini-2.5-pro',
            'gemini-1.5-flash',
            'gemini-1.5-pro',
        ],

        // Modellspezifische Zuordnung für die Framework-Prozesse (z.B. Revision, Dokumentation, Code-Generierung)
        'models' => [
            'revision' => [
                'model' => 'gemini-2.5-pro',
                'version' => 'v1beta',
            ],
            'dokumentation' => [
                'model' => 'gemini-2.5-flash',
                'version' => 'v1beta',
            ],
            'code_generation' => [
                'model' => 'gemini-2.5-pro',
                'version' => 'v1beta',
            ],
        ],


        // Globale Debug-Einstellung (Ausgabe des Prompts statt API Call)
        'debug' => env('DATENSCHUTZ_KI_DEBUG', false),

        // E-Mail für Fehlermeldungen (z.B. API Limits, 404 Model Not Found)
        'admin_notification_mail' => env('MAIL_ADMIN_NOTIFICATION', ''),

        // --- Kostenkontrolle & Limiting ---
        // 1. Rate Limiting (Schutz vor Skripten/Bots): Wie viele Anfragen pro Minute pro User?
        'rate_limit_per_minute' => env('GEMINI_RATE_LIMIT_PER_MINUTE', 5),

        // 2. Weiches Kontingent: Wie viele Anfragen darf ein User pro Monat ansammeln?
        'monthly_limit_per_user' => env('GEMINI_MONTHLY_LIMIT', 100),

        // 3. Caching: Sollen Antworten für identische Eingaben aus der lokalen Datenbank kommen? (spart bares Geld)
        'enable_caching' => env('GEMINI_ENABLE_CACHING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mistral API Configuration
    |--------------------------------------------------------------------------
    */
    'mistral' => [
        'api_key' => env('MISTRAL_API_KEY', ''),

        // Unterstützte Versionen & Modelle
        'allowed_versions' => ['v1'],
        'allowed_models' => [
            'mistral-large-latest',
            'mistral-medium-latest',
            'mistral-small-latest',
            'open-mixtral-8x22b',
        ],

        // Modellspezifische Zuordnung (Mistral) für Framework-Prozesse
        'models' => [
            'revision' => [
                'model' => 'mistral-large-latest',
                'version' => 'v1',
            ],
            'dokumentation' => [
                'model' => 'mistral-small-latest',
                'version' => 'v1',
            ],
            'code_generation' => [
                'model' => 'open-mixtral-8x22b',
                'version' => 'v1',
            ],
        ],

        'debug' => env('DATENSCHUTZ_KI_DEBUG', false),
        'admin_notification_mail' => env('MAIL_ADMIN_NOTIFICATION', ''),

        // Limits & Caching
        'rate_limit_per_minute' => env('GEMINI_RATE_LIMIT_PER_MINUTE', 5), // Limit bleibt vorerst geteilt nutzbar
        'monthly_limit_per_user' => env('GEMINI_MONTHLY_LIMIT', 100),
        'enable_caching' => env('GEMINI_ENABLE_CACHING', true),
    ],
];