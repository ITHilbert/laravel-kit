<?php

namespace ITHilbert\LaravelKit\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Artisan;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Backups all core database tables into seeders files using the iseed package. Run this before moving to another PC to ensure latest state is saved in the repository.')]
class BackupSeedersTool extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        Artisan::call('db:backup-seeders');
        $output = Artisan::output();

        return Response::text("Backup completed gracefully:\n" . $output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
