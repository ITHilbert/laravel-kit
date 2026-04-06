<?php

namespace ITHilbert\LaravelKit\Jobs\Ai;

class RunPhpUnitJob extends AbstractAiJob
{
    protected function getJobType(): string
    {
        return 'test';
    }

    public function handle()
    {
        $run = $this->getRunLog();
        $run->update(['status' => 'processing']);

        try {
            // TODO: Execute PHPUnit
            $run->update([
                'status' => 'success',
                'stdout_log' => 'PHPUnit dummy success (100% green).',
                'stderr_log' => null,
                'finished_at' => now(),
            ]);

            $this->aiTask->update(['status' => 'completed']);

        } catch (\Exception $e) {
            $run->update([
                'status' => 'failed',
                'stderr_log' => $e->getMessage(),
                'finished_at' => now(),
            ]);
            $this->aiTask->update(['status' => 'failed']);
            $this->fail($e);
        }
    }
}
