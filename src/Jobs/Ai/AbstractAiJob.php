<?php

namespace ITHilbert\LaravelKit\Jobs\Ai;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ITHilbert\LaravelKit\Models\AiTask;
use ITHilbert\LaravelKit\Models\AiTaskRun;

abstract class AbstractAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $aiTask;
    public $runNo;

    public function __construct(AiTask $aiTask, int $runNo)
    {
        $this->aiTask = $aiTask;
        $this->runNo = $runNo;
        // Jede AI Execution passiert auf dieser isolierten Queue
        $this->onQueue('ai_pipeline');
    }

    protected function getRunLog(): AiTaskRun
    {
        return AiTaskRun::firstOrCreate([
            'ai_task_id' => $this->aiTask->id,
            'run_no' => $this->runNo,
            'job_type' => $this->getJobType(),
        ]);
    }

    public function middleware()
    {
        return [new \ITHilbert\LaravelKit\Jobs\Ai\Middleware\CheckAiQueuePause];
    }

    abstract protected function getJobType(): string;
}
