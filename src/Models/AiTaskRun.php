<?php

namespace ITHilbert\LaravelKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiTaskRun extends Model
{
    use HasFactory;

    protected $connection = 'ai_sqlite';
    protected $table = 'ai_task_runs';

    protected $fillable = [
        'ai_task_id',
        'run_no',
        'job_type',
        'status',
        'prompt_hash',
        'git_branch',
        'git_commit_hash',
        'stdout_log',
        'stderr_log',
        'result_diff',
        'finished_at',
        'last_heartbeat_at',
    ];

    public function casts(): array
    {
        return [
            'finished_at' => 'datetime',
            'last_heartbeat_at' => 'datetime',
        ];
    }

    public function task()
    {
        return $this->belongsTo(AiTask::class, 'ai_task_id');
    }
}
