<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Process;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Führt einen Bash-Befehl auf dem Live-Server des aktuellen Projekts aus. Basiert auf der mcp_remote config und lokalen SSH-Keys.')]
class RemoteExecuteTool extends Tool
{
    public function handle(Request $request): Response
    {
        $command = escapeshellarg($request->input('command'));
        
        $host = config('mcp_remote.host');
        $user = config('mcp_remote.user');
        $path = config('mcp_remote.path');
        $port = config('mcp_remote.port', 22);

        if (empty($host) || empty($user) || empty($path)) {
            return Response::text("Fehler: MCP-Remote Konfiguration fehlt. Bitte LIVE_SERVER_HOST, LIVE_SERVER_USER und LIVE_SERVER_PATH in der .env setzen.");
        }

        $sshCommand = "ssh -p {$port} {$user}@{$host} 'cd {$path} && {$command}'";
        
        try {
            $result = Process::timeout(120)->run($sshCommand);
            
            if ($result->successful()) {
                return Response::text("Erfolg!\n\nStandardausgabe:\n" . $result->output());
            } else {
                return Response::text("Fehler beim Ausführen des Befehls!\n\nExit Code: " . $result->exitCode() . "\n\nStandardfehlerausgabe:\n" . $result->errorOutput() . "\n\nStandardausgabe:\n" . $result->output());
            }
        } catch (\Exception $e) {
            return Response::text("Kritischer Fehler beim SSH-Aufruf: " . $e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'command' => $schema->string()->description('Der auszuführende Serverbefehl (z.B. php artisan optimize, composer install)')->required(),
        ];
    }
}
