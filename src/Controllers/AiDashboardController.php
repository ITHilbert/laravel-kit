<?php

namespace ITHilbert\LaravelKit\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Bus;
use ITHilbert\LaravelKit\Models\AiTask;
use ITHilbert\LaravelKit\Jobs\Ai\RunCursorBuilderJob;
use ITHilbert\LaravelKit\Jobs\Ai\RunPhpUnitJob;
use ITHilbert\LaravelKit\Jobs\Ai\RunCriticReviewJob;

class AiDashboardController extends Controller
{
    public function index()
    {
        $tasks = AiTask::with(['runs' => function($q) {
            $q->orderBy('run_no', 'asc');
        }])->orderBy('created_at', 'desc')->get();
        
        $isPaused = Cache::get('ai_queue_paused', false);

        return view('laravelkit::ai.dashboard', compact('tasks', 'isPaused'));
    }

    public function togglePause()
    {
        $currentState = Cache::get('ai_queue_paused', false);
        Cache::put('ai_queue_paused', !$currentState);
        
        return redirect()->back();
    }

    public function destroy($id)
    {
        $task = AiTask::findOrFail($id);
        
        // Prevent deleting active running jobs strictly without killing processes
        if ($task->status === 'running') {
            return redirect()->back()->withErrors(['msg' => 'Running tasks cannot be deleted seamlessly over UI right now. Pause first.']);
        }
        
        $task->delete(); // Cascades AiTaskRuns
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'module' => 'nullable|string',
        ]);

        $task = AiTask::create([
            'title' => $request->title,
            'description' => $request->description,
            'module' => $request->module ?? 'Core',
            'depends_on_task_id' => $request->depends_on_task_id,
            'status' => 'pending',
        ]);

        $chain = Bus::chain([
            new RunCursorBuilderJob($task, 1),
            new RunPhpUnitJob($task, 1),
            new RunCriticReviewJob($task, 1),
        ]);

        if ($request->has('is_urgent')) {
            $chain->onQueue('ai_pipeline_high');
        } else {
            $chain->onQueue('ai_pipeline');
        }

        $chain->dispatch();

        return redirect()->back();
    }
}
