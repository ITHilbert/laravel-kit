<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'ai_sqlite';

    public function up()
    {
        if (!file_exists(config('database.connections.ai_sqlite.database'))) return;

        Schema::table('ai_tasks', function (Blueprint $table) {
            $table->text('agent_feedback')->nullable()->after('tags');
        });
    }

    public function down()
    {
        if (!file_exists(config('database.connections.ai_sqlite.database'))) return;

        Schema::table('ai_tasks', function (Blueprint $table) {
            $table->dropColumn('agent_feedback');
        });
    }
};
