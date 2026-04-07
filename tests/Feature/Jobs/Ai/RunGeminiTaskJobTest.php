<?php

namespace ITHilbert\LaravelKit\Tests\Feature\Jobs\Ai;

use Tests\TestCase;
use ITHilbert\LaravelKit\Models\AiTask;
use ITHilbert\LaravelKit\Models\AiTaskRun;
use ITHilbert\LaravelKit\Jobs\Ai\RunGeminiTaskJob;
use Illuminate\Support\Facades\Process;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;

class RunGeminiTaskJobTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_completes_the_task_on_success_without_feedback()
    {
        // Mocking the facade
        Process::fake([
            '*' => Process::result(
                output: 'Gemini Response: Das ist eine erfolgreiche Antwort.',
                errorOutput: '',
                exitCode: 0
            )
        ]);

        $task = AiTask::create([
            'title' => 'Test Task',
            'description' => 'Mache X und Y',
            'module' => 'Core',
            'status' => 'pending'
        ]);

        $job = new RunGeminiTaskJob($task, 1);
        $job->handle();

        $task->refresh();
        $this->assertEquals('completed', $task->status);
        $this->assertNull($task->rueckfrage);

        $run = $task->runs()->first();
        $this->assertEquals('success', $run->status);
        $this->assertStringContainsString('Das ist eine erfolgreiche Antwort.', $run->stdout_log);
    }

    #[Test]
    public function it_sets_task_to_needs_info_when_feedback_required_is_matched()
    {
        // Mocking the facade with a process output that contains the feedback string
        $cliOutput = "Einige Logs\n\n# FEEDBACK_REQUIRED Wir brauchen mehr Infos über Modul X.\n# Agent Feedback\nLief alles gut.";
        
        Process::fake([
            '*' => Process::result(
                output: $cliOutput,
                errorOutput: '',
                exitCode: 0
            )
        ]);

        $task = AiTask::create([
            'title' => 'Test Task Feedback',
            'description' => 'Mache X und Y',
            'module' => 'Core',
            'status' => 'pending'
        ]);

        $job = new RunGeminiTaskJob($task, 1);
        $job->handle();

        // Überprüft Ob der Task in "needs_info" gelaufen ist
        $task->refresh();
        $this->assertEquals('needs_info', $task->status);
        $this->assertEquals('Wir brauchen mehr Infos über Modul X.', $task->rueckfrage);

        $run = $task->runs()->first();
        $this->assertEquals('needs_info', $run->status);
        $this->assertStringContainsString('Wir brauchen mehr Infos', $run->stdout_log);
    }
}
