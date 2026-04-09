<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Die Datenbankverbindung für die Migration.
     *
     * @var string
     */
    protected $connection = 'ai_sqlite';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_tasks', 'prompt')) {
                $table->text('prompt')->nullable()->after('module');
            }
            if (!Schema::hasColumn('ai_tasks', 'is_urgent')) {
                $table->boolean('is_urgent')->default(false)->after('status');
            }
            if (!Schema::hasColumn('ai_tasks', 'agent_feedback')) {
                $table->text('agent_feedback')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('ai_tasks', 'git_branch')) {
                $table->string('git_branch')->nullable()->after('agent_feedback');
            }
            if (!Schema::hasColumn('ai_tasks', 'git_commit_hash')) {
                $table->string('git_commit_hash')->nullable()->after('git_branch');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'prompt',
                'is_urgent',
                'agent_feedback',
                'git_branch',
                'git_commit_hash',
            ]);
        });
    }
};
