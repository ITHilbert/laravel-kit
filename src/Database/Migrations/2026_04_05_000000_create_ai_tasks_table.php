<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'ai_sqlite';

    public function up()
    {
        $dbPath = config('database.connections.ai_sqlite.database');
        if (!file_exists($dbPath)) {
            return;
        }

        Schema::create('ai_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('pending'); // pending, running, completed, failed, blocked
            $table->unsignedBigInteger('depends_on_task_id')->nullable();
            $table->string('module')->default('Core');
            $table->json('tags')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_task_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_task_id')->constrained('ai_tasks')->cascadeOnDelete();
            $table->unsignedInteger('run_no')->default(1);
            $table->string('job_type'); // builder, test, critic
            $table->string('status')->default('queued'); // queued, processing, success, failed
            $table->string('prompt_hash')->nullable();
            $table->string('git_branch')->nullable();
            $table->string('git_commit_hash')->nullable();
            $table->longText('stdout_log')->nullable();
            $table->longText('stderr_log')->nullable();
            $table->longText('result_diff')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        $dbPath = config('database.connections.ai_sqlite.database');
        if (!file_exists($dbPath)) {
            return;
        }

        Schema::dropIfExists('ai_task_runs');
        Schema::dropIfExists('ai_tasks');
    }
};
