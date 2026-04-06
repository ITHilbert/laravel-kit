<?php

namespace ITHilbert\LaravelKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AiTask extends Model
{
    use HasFactory;

    protected $connection = 'ai_sqlite';
    protected $table = 'ai_tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'depends_on_task_id',
        'module',
        'tags',
        'rueckfrage',
    ];

    public function casts(): array
    {
        return [
            'tags' => 'array',
        ];
    }

    public function runs()
    {
        return $this->hasMany(AiTaskRun::class, 'ai_task_id');
    }

    public function dependency()
    {
        return $this->belongsTo(AiTask::class, 'depends_on_task_id');
    }
}
