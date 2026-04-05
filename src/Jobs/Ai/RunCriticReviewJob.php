<?php

namespace ITHilbert\LaravelKit\Jobs\Ai;

class RunCriticReviewJob extends AbstractAiJob
{
    protected function getJobType(): string
    {
        return 'critic';
    }

    public function handle()
    {
        $run = $this->getRunLog();
        $run->update(['status' => 'processing']);

        try {
            // TODO: Execute Git Diff validation
            
            $run->update([
                'status' => 'success',
                'stdout_log' => 'Critic review dummy passed.',
                'finished_at' => now(),
            ]);

            // Finaler Abschluss des AI Tasks
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
