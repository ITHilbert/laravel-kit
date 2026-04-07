# AI Pipeline Installation & Konzept

Dieses Dokument beschreibt die Einrichtung und das Setup der "AI Task Pipeline" in Projekten, die `laravel-kit` nutzen. Das gesamte Setup-Wissen ist in das Konsolen-Kommando `install:ai-task` sowie in einen globalen Agent-Workflow ausgelagert, um manuelle Fehler zu vermeiden.

## 1. Das Backend-Kommando (`install:ai-task`)
Das Kommando `php artisan install:ai-task` kapselt die Infrastruktur-Voraussetzungen. Wenn man es ausführt, passiert im Hintergrund folgendes:

1. **Configs publishen:** Führt `vendor:publish` aus. Die Dateien `ai.php` und `laravelkit.php` werden in den `config/` Ordner der Host-Website kopiert.
2. **Datenbank Migrations:** Ruft `php artisan migrate` auf, um die Tabellen (`ai_tasks`, `ai_task_runs` etc.) anzulegen.
3. **NPM Abhängigkeiten:** Führt `npm install -D @gemini-cli/gemini` aus, um den Headless-Worker bereitzustellen.
4. **Environment konfigurieren:** Prüft die `.env`-Datei und ergänzt (falls fehlend) `AI_EXECUTION_MODE=daemon`.

## 2. Der Agent-Workflow (`/setup-ai-task`)
Da APIs und spezielle Keys (wie `GEMINI_API_KEY`) nicht vom Composer-Kommando blind überschrieben werden sollten, übernimmt der **AI-Agent** die menschliche Interaktion.

In dem zentralen `.agents`-Repository liegt unter `.agents/workflows/setup-ai-task.md` der zugehörige Initialisierungs-Workflow.

**Ablauf für dich als Entwickler in einem NEUEN Projekt:**
1. Führe `composer require ithilbert/laravel-kit` im neuen Projekt aus.
2. Kopiere dir das `.agents` Repo ins Root (z.B. via add-agents Workflow).
3. Wechsle in den Chat (IDE) und tippe `/setup-ai-task`.
4. Der Agent führt für dich dann alle Configs / Installationen via `php artisan install:ai-task` aus.
5. Der Agent überprüft deine Keys, weist dich auf Fehlkonfigurationen hin, und testet den Worker sofort.

Dadurch bleibt der PHP-Code schlank (Command) und die Business-Intelligenz wird exklusiv von der KI verwaltet.
