<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Pipeline Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800 font-sans p-8">

    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center bg-white p-6 rounded shadow mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">🚀 AI Pipeline Radar</h1>
                <p class="text-sm text-gray-500">Autonomous Cursor Task Management</p>
            </div>
            <div>
                <form action="{{ route('ai.pause') }}" method="POST">
                    @csrf
                    @if($isPaused)
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow">
                            ▶ Queue Fortsetzen
                        </button>
                    @else
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow">
                            ⏸ Queue Pausieren
                        </button>
                    @endif
                </form>
            </div>
        </header>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Linke Spalte: Neuer Task Formular -->
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-4">Neuen Task einschieben</h2>
                <form action="{{ route('ai.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Titel</label>
                        <input type="text" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Modul</label>
                        <input type="text" name="module" value="Core" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Abhängigkeit (Task ID)</label>
                        <input type="number" name="depends_on_task_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Optional">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Instruktionen</label>
                        <textarea name="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required></textarea>
                    </div>
                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="is_urgent" id="is_urgent" value="1" class="mr-2">
                        <label for="is_urgent" class="font-bold text-red-600">High Priority (Überholen)</label>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow">
                        Task Einreihen
                    </button>
                </form>
            </div>

            <!-- Rechte Spalte: Aktive & Vergangene Tasks -->
            <div class="md:col-span-2 space-y-6">
                <!-- Aktive Queue -->
                <div class="bg-white p-6 rounded shadow">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">Active Queue</h2>
                    @forelse($tasks->whereIn('status', ['pending', 'running']) as $task)
                        <div class="mb-4 p-4 border rounded hover:bg-gray-50 flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg">#{{ $task->id }}: {{ $task->title }}</h3>
                                <div class="text-sm text-gray-600 mb-2">Module: {{ $task->module }} | Status: <span class="font-mono bg-blue-100 text-blue-800 px-2 rounded">{{ $task->status }}</span></div>
                                <div class="text-sm">{{ \Illuminate\Support\Str::limit($task->description, 100) }}</div>
                            </div>
                            <form action="{{ route('ai.destroy', $task->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold" onclick="return confirm('WIRKLICH LÖSCHEN?')">X</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Keine anstehenden Tasks.</p>
                    @endforelse
                </div>

                <!-- Historie / Abgeschlossen -->
                <div class="bg-white p-6 rounded shadow opacity-80">
                    <h2 class="text-xl font-bold mb-4 border-b pb-2">History & Logs</h2>
                    @forelse($tasks->whereIn('status', ['completed', 'failed']) as $task)
                        <div class="mb-4 p-4 border rounded hover:bg-gray-50">
                            <h3 class="font-bold">#{{ $task->id }}: {{ $task->title }}</h3>
                            <div class="text-sm mb-2">
                                Status: <span class="font-mono px-2 rounded {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $task->status }}</span>
                            </div>
                            
                            @foreach($task->runs as $run)
                                <div class="text-xs bg-gray-100 p-2 mt-2 rounded border-l-4 {{ $run->status === 'success' ? 'border-green-500' : ($run->status === 'failed' ? 'border-red-500' : 'border-gray-400') }}">
                                    <strong>Run {{ $run->run_no }} ({{ $run->job_type }}):</strong> {{ $run->status }}
                                    @if($run->git_commit_hash) | Git: {{ $run->git_commit_hash }} @endif
                                    @if($run->stderr_log)
                                        <div class="mt-1 text-red-600 font-mono overflow-x-auto whitespace-pre">{{ $run->stderr_log }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-gray-500 italic">Noch keine beendeten Tasks.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
