<?php

namespace ITHilbert\LaravelKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiTask extends Model
{
    use HasFactory;

    /**
     * Die Datenbankverbindung für das Model.
     *
     * @var string
     */
    protected $connection = 'ai_sqlite';

    /**
     * Die Tabelle, die dem Model zugeordnet ist.
     *
     * @var string
     */
    protected $table = 'ai_tasks';

    /**
     * Die Attribute, die massenzuweisbar sind.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'module',
        'prompt',
        'description',
        'status',
        'is_urgent',
        'agent_feedback',
        'git_branch',
        'git_commit_hash',
        'depends_on_task_id',
        'tags',
        'rueckfrage',
    ];

    /**
     * Die Attribute, die konvertiert werden sollen.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_urgent' => 'boolean',
            'tags' => 'array',
        ];
    }

    /**
     * Gibt die mit dem Task verknüpften Durchläufe zurück.
     */
    public function runs(): HasMany
    {
        return $this->hasMany(AiTaskRun::class, 'ai_task_id');
    }

    /**
     * Gibt den Task zurück, von dem dieser Task abhängig ist.
     */
    public function dependency(): BelongsTo
    {
        return $this->belongsTo(AiTask::class, 'depends_on_task_id');
    }
}
