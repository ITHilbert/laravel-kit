<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;

class OpenFtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Öffnet eine interaktive SFTP Verbindung zum Live-Server (basiert auf LIVE_SERVER_.env).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = config('mcp_remote.host');
        $user = config('mcp_remote.user');
        $path = config('mcp_remote.path');
        $port = config('mcp_remote.port', 22);

        if (empty($host) || empty($user)) {
            $this->error('Fehler: MCP-Remote Konfiguration fehlt. Bitte LIVE_SERVER_HOST und LIVE_SERVER_USER in der .env setzen.');
            return Command::FAILURE;
        }

        $target = "{$user}@{$host}";
        if (!empty($path)) {
            // Mit :pfad springt SFTP nach dem Login direkt ins richtige Projektverzeichnis
            $target .= ":{$path}";
        }

        $this->info("Verbinde via SFTP mit {$target} auf Port {$port}...");
        
        $cmd = "sftp -P {$port} {$target}";
        
        // passthru gibt die Kontrolle über Standard-In/Out direkt ans Terminal des Users weiter
        passthru($cmd);

        return Command::SUCCESS;
    }
}
