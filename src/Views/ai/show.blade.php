<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task #{{ $task->id }} - AI Pipeline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</head>

<body class="bg-gray-100 min-h-screen text-gray-800 font-sans p-8">

    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center bg-white p-6 rounded shadow mb-6">
            <div>
                <a href="{{ route('ai.dashboard') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 mb-3 transition-colors">
                    <span aria-hidden="true">←</span> Zurück zum Dashboard
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Task #{{ $task->id }}: {{ $task->title }}</h1>
                <p class="text-sm text-gray-500">Erstellt am {{ $task->created_at->format('d.m.Y H:i:s') }}</p>
            </div>
            <div>
                <span class="font-mono px-3 py-1 rounded text-lg
                    @if($task->status === 'completed') bg-green-100 text-green-800
                    @elseif($task->status === 'failed') bg-red-100 text-red-800
                    @elseif($task->status === 'running') bg-blue-100 text-blue-800
                    @elseif($task->status === 'needs_info') bg-orange-100 text-orange-800
                    @else bg-gray-200 text-gray-800 @endif
                ">
                    {{ strtoupper($task->status) }}
                </span>
            </div>
        </header>

        @if($task->rueckfrage)
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded shadow mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4 w-full">
                    <h3 class="text-lg font-bold text-red-800">Rückfrage vom Gemini Worker</h3>
                    <div class="mt-2 text-red-700 whitespace-pre-wrap">{{ $task->rueckfrage }}</div>
                    
                    <form action="{{ route('ai.respond', $task->id) }}" method="POST" class="mt-6">
                        @csrf
                        <div class="mb-4">
                            <label for="answer" class="block text-sm font-bold text-red-800 mb-1">Deine Antwort:</label>
                            <textarea id="answer" name="answer" rows="4" required
                                class="w-full bg-white border border-red-200 rounded p-3 text-sm focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition-colors">
                            Antwort senden & Task fortsetzen
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 space-y-6">
                <!-- Task Payload / Instruktionen -->
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Instruktionen & Context Parameter</h2>
                    
                    <div class="mb-4">
                        <strong class="block text-gray-700 text-sm mb-1">Eingegebenes Modul:</strong>
                        <div class="bg-gray-50 border rounded p-2 font-mono text-sm">{{ $task->module }}</div>
                    </div>

                    <div class="mb-4">
                        <strong class="block text-gray-700 text-sm mb-1">Depends On (Abhängigkeit):</strong>
                        <div class="bg-gray-50 border rounded p-2 font-mono text-sm">{{ $task->depends_on_task_id ?? 'Ohne' }}</div>
                    </div>

                    <div class="mb-4">
                        <strong class="block text-gray-700 text-sm mb-1">Prompt / Beschreibung:</strong>
                        <div class="bg-gray-50 border rounded p-4 text-sm whitespace-pre-wrap">{{ $task->description }}</div>
                    </div>


                </div>

                <!-- Run Historie -->
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Ausführungs-Historie (Runs)</h2>
                    
                    @forelse($task->runs as $run)
                        <div class="mb-6 border rounded overflow-hidden">
                            <div class="bg-gray-50 p-3 border-b flex justify-between items-center sm:flex-row flex-col">
                                <div class="font-bold">
                                    Run #{{ $run->run_no }} <span class="text-gray-500 font-normal text-sm">({{ $run->job_type }})</span>
                                </div>
                                <div class="flex items-center gap-4 text-sm mt-2 sm:mt-0">
                                    @if($run->git_commit_hash)
                                        <span class="text-gray-600" title="Git Commit vor dem Run">
                                            Git: <code class="bg-gray-200 px-1 rounded">{{ substr($run->git_commit_hash, 0, 7) }}</code>
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded text-xs font-bold
                                        @if($run->status === 'success') bg-green-100 text-green-800
                                        @elseif($run->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-200 text-gray-800 @endif
                                    ">
                                        {{ strtoupper($run->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-white text-sm">
                                <p class="text-gray-500 mb-2 border-b pb-2">
                                    Gestartet: {{ $run->created_at ? $run->created_at->format('H:i:s') : '-' }} | 
                                    Beendet: {{ $run->finished_at ? $run->finished_at->format('H:i:s') : '-' }}
                                </p>
                                
                                @if($run->stderr_log)
                                    <div class="mt-3">
                                        <h4 class="font-bold text-red-700 text-xs uppercase mb-1">Errors / Output:</h4>
                                        <pre><code class="language-bash rounded bg-red-50 p-2 block">{{ $run->stderr_log }}</code></pre>
                                    </div>
                                @endif
                                
                                @if($run->stdout_log)
                                    <div class="mt-3">
                                        <h4 class="font-bold text-gray-600 text-xs uppercase mb-1">Standard Output:</h4>
                                        <pre><code class="language-bash rounded bg-gray-50 border p-2 block max-h-96 overflow-y-auto">{{ $run->stdout_log }}</code></pre>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Noch keine Runs aufgezeichnet.</p>
                    @endforelse
                </div>
            </div>

            <div class="space-y-6">
                <!-- Aktionen -->
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Aktionen</h2>
                    <form action="{{ route('ai.destroy', $task->id) }}" method="POST" class="w-full">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow transition-colors"
                            onclick="return confirm('Möchtest du diesen Task und alle zugehörigen Logs WIRKLICH LÖSCHEN?')">
                            🗑 Task Löschen
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
