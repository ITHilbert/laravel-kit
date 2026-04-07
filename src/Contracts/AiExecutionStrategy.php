<?php

namespace ITHilbert\LaravelKit\Contracts;

use Closure;

interface AiExecutionStrategy
{
    /**
     * Executes the AI task.
     *
     * @param \ITHilbert\LaravelKit\Models\AiTask $aiTask
     * @param \ITHilbert\LaravelKit\Models\AiTaskRun $run
     * @param Closure(\Exception $e): void $failClosure
     * @return void
     */
    public function execute($aiTask, $run, Closure $failClosure);
}
