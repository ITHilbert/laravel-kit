<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'ai_sqlite';

    public function up()
    {
        Schema::table('ai_tasks', function (Blueprint $table) {
            $table->timestamp('last_heartbeat_at')->nullable()->after('status');
        });

        Schema::table('ai_task_runs', function (Blueprint $table) {
            $table->timestamp('last_heartbeat_at')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('ai_tasks', function (Blueprint $table) {
            $table->dropColumn('last_heartbeat_at');
        });

        Schema::table('ai_task_runs', function (Blueprint $table) {
            $table->dropColumn('last_heartbeat_at');
        });
    }
};
