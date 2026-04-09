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
    // E-Mail für Fehlermeldungen (z.B. API Limits, 404 Model Not Found)
    'admin_notification_mail' => env('MAIL_ADMIN_NOTIFICATION', ''),
    // --- Kostenkontrolle, Limiting & Caching ---
    'limits' => [
        // Genereller An/Aus-Schalter für jegliche Limitierungen
        'enabled' => env('AI_USE_LIMITS', false),

        // 1. Rate Limiting (Schutz vor Skripten/Bots): Wie viele Anfragen pro Minute pro User?
        'rate_limit_per_minute' => env('AI_RATE_LIMIT_PER_MINUTE', 5),

        // 2. Weiches Kontingent: Wie viele Anfragen darf ein User pro Monat ansammeln?
        'monthly_limit_per_user' => env('AI_MONTHLY_LIMIT', 100),
    ],

    'caching' => [
        // Sollen Antworten für identische Eingaben aus der lokalen Datenbank kommen? (spart bares Geld)
        'enabled' => env('AI_ENABLE_CACHING', false),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),

        // Unterstützte Modelle und ihre verfügbaren API-Versionen
        'allowed_models' => [
            // Gemini 3.x (Aktuelle State-of-the-Art Modelle)
            'gemini-3.1-pro' => ['v1beta'],
            'gemini-3.1-flash-lite' => ['v1beta'],
            'gemini-3.0-flash' => ['v1beta'],

            // Gemini 2.5
            'gemini-2.5-pro' => ['v1beta'],
            'gemini-2.5-flash' => ['v1', 'v1beta'],
            'gemini-2.5-flash-lite' => ['v1', 'v1beta'],

            // Gemini 2.0
            'gemini-2.0-pro-exp-02-05' => ['v1beta'],
            'gemini-2.0-flash-thinking-exp-01-21' => ['v1beta'],
            'gemini-2.0-flash-lite-preview-02-27' => ['v1beta'],
            'gemini-2.0-flash' => ['v1', 'v1beta'],

            // Gemini 1.5 (Legacy/Stable)
            'gemini-1.5-pro' => ['v1', 'v1beta'],
            'gemini-1.5-flash' => ['v1', 'v1beta'],
            'gemini-1.5-flash-8b' => ['v1', 'v1beta'],
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
                'model' => 'gemini-3.1-pro',
                'version' => 'v1beta',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY', ''),

        // Unterstützte Modelle und ihre verfügbaren API-Versionen
        'allowed_models' => [
            // GPT-5.4 Models (Aktuelle Flaggschiff-Generation)
            'gpt-5.4-pro' => ['v1'],
            'gpt-5.4-thinking' => ['v1'],
            'gpt-5.4-mini' => ['v1'],
            'gpt-5.4-nano' => ['v1'],
            'gpt-5.3-instant' => ['v1'],

            // Legacy GPT-4o & Reasoning (o1/o3) Models
            'o3-mini' => ['v1'],
            'o1' => ['v1'],
            'o1-mini' => ['v1'],
            'gpt-4o' => ['v1'],
            'gpt-4o-mini' => ['v1'],
        ],

        // Modellspezifische Zuordnung (OpenAI) für Framework-Prozesse
        'models' => [
            'revision' => [
                'model' => env('LARAVELKIT_AI_MODEL', 'gpt-5.4-pro'),
                'version' => 'v1',
            ],
            'dokumentation' => [
                'model' => 'gpt-5.4-mini',
                'version' => 'v1',
            ],
            'code_generation' => [
                'model' => 'gpt-5.4-thinking',
                'version' => 'v1',
            ],
        ],
    ],

    /*
|--------------------------------------------------------------------------
| Mistral API Configuration
|--------------------------------------------------------------------------
*/
    'mistral' => [
        'api_key' => env('MISTRAL_API_KEY', ''),

        // Unterstützte Modelle und ihre verfügbaren API-Versionen
        'allowed_models' => [
            // Flagship & Universal
            'mistral-large-latest' => ['v1'],
            'mistral-small-latest' => ['v1'],
            'open-mistral-nemo' => ['v1'],

            // Specialized & Vision
            'pixtral-large-latest' => ['v1'],
            'pixtral-12b-2409' => ['v1'],
            'codestral-latest' => ['v1'],

            // Legacy / Mixtral
            'open-mixtral-8x22b' => ['v1'],
            'open-mixtral-8x7b' => ['v1'],
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
                'model' => 'codestral-latest',
                'version' => 'v1',
            ],
        ],
    ],
];
