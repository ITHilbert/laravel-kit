<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;

class OpenSsh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssh:open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Öffnet eine interaktive SSH Verbindung zum Live-Server (basiert auf LIVE_SERVER_.env).';

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

        $this->info("Verbinde via SSH mit {$user}@{$host} auf Port {$port}...");

        $cmd = "ssh -p {$port} {$user}@{$host}";

        // Wechsel direkt in den Projektpfad, falls angegeben. (-t zwingt interaktives PTY)
        if (! empty($path)) {
            $cmd .= " -t 'cd {$path} && exec bash -l'";
        }

        // passthru übergibt In/Out/Error an den realen Terminal-Prozess
        passthru($cmd);

        return Command::SUCCESS;
    }
}
