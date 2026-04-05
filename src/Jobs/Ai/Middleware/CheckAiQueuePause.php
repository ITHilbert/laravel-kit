<?php

namespace ITHilbert\LaravelKit\Jobs\Ai\Middleware;

use Illuminate\Support\Facades\Cache;

class CheckAiQueuePause
{
    public function handle($job, $next)
    {
        // Wenn über UI auf Pause gedrückt wurde: Task blockieren und verzögern
        if (Cache::get('ai_queue_paused', false)) {
            $job->release(30); // in 30 Sekunden erneut prüfen
            return;
        }

        return $next($job);
    }
}
