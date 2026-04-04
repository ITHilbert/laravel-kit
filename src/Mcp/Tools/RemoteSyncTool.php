<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Process;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Synchronisiert lokale Dateien mit dem Live-Server via rsyc. Lädt Modifikationen selektiv auf das Live-System.')]
class RemoteSyncTool extends Tool
{
    public function handle(Request $request): Response
    {
        $files = $request->input('files');
        
        if (!is_array($files) || empty($files)) {
            return Response::text("Fehler: Mindestens eine Datei muss im files-Array übergeben werden.");
        }

        $host = config('mcp_remote.host');
        $user = config('mcp_remote.user');
        $path = config('mcp_remote.path');
        $port = config('mcp_remote.port', 22);

        if (empty($host) || empty($user) || empty($path)) {
            return Response::text("Fehler: MCP-Remote Konfiguration fehlt. Bitte LIVE_SERVER_HOST, LIVE_SERVER_USER und LIVE_SERVER_PATH in der .env setzen.");
        }

        $escapedFiles = array_map('escapeshellarg', $files);
        $filesString = implode(' ', $escapedFiles);
        
        // Rsync mit -R behält die Pfadstruktur bei
        $rsyncCommand = "rsync -avzR -e 'ssh -p {$port}' {$filesString} {$user}@{$host}:{$path}";
        
        try {
            $result = Process::timeout(300)->run($rsyncCommand);
            
            if ($result->successful()) {
                return Response::text("Erfolg! Dateien synchronisiert.\n\nRsync-Output:\n" . $result->output());
            } else {
                return Response::text("Fehler beim Synchronisieren!\n\nRsync Exit Code: " . $result->exitCode() . "\n\nError Output:\n" . $result->errorOutput() . "\n\nStandardausgabe:\n" . $result->output());
            }
        } catch (\Exception $e) {
            return Response::text("Kritischer Fehler beim Rsync-Aufruf: " . $e->getMessage());
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'files' => $schema->array()->description('Array von lokalen relativen Dateipfaden, die auf den Server geladen werden sollen (z.B. ["app/Http/Controllers/HomeController.php", "resources/views/home.blade.php"])')->required(),
        ];
    }
}
