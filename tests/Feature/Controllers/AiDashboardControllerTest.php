<?php

namespace ITHilbert\LaravelKit\Tests\Feature\Controllers;

use Tests\TestCase;
use ITHilbert\LaravelKit\Models\AiTask;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;

class AiDashboardControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_can_display_the_dashboard()
    {
        $response = $this->get('/devtools/ai');

        $response->assertStatus(200);
        $response->assertViewIs('laravelkit::ai.dashboard');
    }

    #[Test]
    public function it_can_store_a_new_task_and_dispatch_chain()
    {
        Bus::fake();

        $response = $this->post('/devtools/ai', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'module' => 'Core'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('ai_tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'module' => 'Core',
            'status' => 'pending'
        ], 'ai_sqlite');

        Bus::assertChained([
            \ITHilbert\LaravelKit\Jobs\Ai\RunGeminiTaskJob::class,
            \ITHilbert\LaravelKit\Jobs\Ai\RunPhpUnitJob::class,
        ]);
    }

    #[Test]
    public function it_can_display_the_show_page()
    {
        $task = AiTask::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'module' => 'Core',
            'status' => 'pending'
        ]);

        $response = $this->get('/devtools/ai/' . $task->id);

        $response->assertStatus(200);
        $response->assertViewIs('laravelkit::ai.show');
        $response->assertSee('Task #' . $task->id);
    }
}
