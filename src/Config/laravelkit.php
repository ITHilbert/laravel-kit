<?php

return [
    'name' => 'LaravelKit',
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
        ]
    ],
];
