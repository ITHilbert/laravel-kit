<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!file_exists(config('database.connections.ai_sqlite.database'))) return;

        Schema::connection('ai_sqlite')->table('ai_tasks', function (Blueprint $table) {
            $table->dropColumn('agent_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!file_exists(config('database.connections.ai_sqlite.database'))) return;

        Schema::connection('ai_sqlite')->table('ai_tasks', function (Blueprint $table) {
            $table->text('agent_feedback')->nullable()->after('tags');
        });
    }
};
