<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Live Server Connection Settings
    |--------------------------------------------------------------------------
    |
    | Hier werden die SSH-Daten für den Live-Server definiert, auf den die MCP
    | Remote-Tools (Sync, Execute) exklusiv zugreifen. Das lokale System muss
    | über ssh-keys (ohne Passworteingabe) auf den Remote-User berechtigt sein.
    |
    */

    'host' => env('LIVE_SERVER_HOST', ''),

    'user' => env('LIVE_SERVER_USER', 'root'),

    'path' => env('LIVE_SERVER_PATH', '/var/www/html'),

    'port' => env('LIVE_SERVER_PORT', 22),

];
