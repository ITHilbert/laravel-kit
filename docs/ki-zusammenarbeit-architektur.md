# Diskussions-Protokoll: KI-Zusammenarbeit & Task-Queue Architektur

### 1. Status & Kontext
- Erarbeitung eines Multi-Agenten-Prozesses zwischen VS Code (AntiGravity) und einem Background-Worker über eine Task-Datenbank.
- **Aktueller Status:** `[Beschlossen]` (Konzept steht, Kostenfrage für den Worker geklärt).

### 2. Die Ausgangslage (Das Problem / Ziel)
Das Arbeiten mit zwei KIs in unterschiedlichen Fenstern bremst den Entwickler stark aus. Ziel ist ein lokaler "Multi-Threading"-Ansatz: VS Code (AntiGravity) wird zur reinen Planungs- und Architektur-Zentrale. Ein Hintergrundprozess erledigt die stumpfe Programmierung asynchron.

### 3. Evaluierte Ansätze (Varianten)
- **Variante A: "Markdown Handshake"** (Verworfen)
- **Variante B: Lokale Task-Datenbank (SQLite) [Lösung]**
  - **Pro:** Sauberes Queuing in einer `ai_tasks` Tabelle.

### 4. Offene Fragen & Neu evaluierte Ansätze
1. **API-Kosten vs. Headless-Worker (Der "Cursor CLI" Durchbruch)**
   - *Verworfen:* Eigener API-Daemon mit Pay-per-Token oder Cursor "Dauer-Loop" in der UI.
   - *Beschlossen (Variante F):* Wir nutzen einen lokalen Filesystem/DB Watcher (`artisan ai:watch`). Sobald AntiGravity via SQLite einen Task ableitet, wirft dieser Watcher den `cursor-agent` CLI Agenten headless an!

### 5. Beschlüsse (Die Hard Facts & Architektur)
1. **GitHub Copilot Kündigung:**
   - **Beschluss:** Copilot kann **gecancelt** werden, da es bei diesem Architektur-Level obsolet ist.
2. **Der finale Worker-Ansatz (Laravel Native Queues & isolierte DB!):**
   - **Beschluss:** Wir nutzen die **native Laravel Job Queue** (database). 
   - **Isolierte Entwickler-Datenbank:** Um die Production-Datenbank extrem sauber zu halten, werden alle AI-Tabellen (`ai_tasks`, `ai_task_runs`, Jobs) in einer reinen lokalen Dev-Datenbank abgelegt (`storage/devtools_ai.sqlite`). Das Agent-System weicht von der regulären MySQL/MariaDB ab und nutzt nativ eine eigens definierte Connection (`ai_sqlite`), damit niemals Agent-Schmutz in die Business-Daten gelangt.
   - Wir konfigurieren saubere Laravel Jobs (z.B. `RunCursorAgentJob`).
   - *Ohne Überschneidungen:* Wir nutzen die Middleware `WithoutOverlapping`, damit nicht zwei Worker versehentlich an denselben Projekt-Dateien arbeiten.
   - *Timeouts & Retries:* Jeder Job-Typ erhält spezifische Limits (z.B. `timeout` und `backoff` Staffelungen: `[1, 5, 10]`).
3. **Das isolierte Git-Sicherheitsnetz (Branches/Worktrees):**
   - **Beschluss:** Wir müllen den Master Branch nicht mit unfertigen KI-Gehversuchen voll. Der Worker checkt für jeden AI-Task automatisch in einen isolierten Zweig (z.B. `ai/task-42`) aus. Erst nach erfolgreicher Beendigung und Freigabe durch den Review-Agent wird das Ergebnis zurück auf den Arbeits-Branch gemerged!
4. **Strukturierte Prompt-Templates & Globale Regeln:**
   - **Beschluss:** Prompt-Befehle werden sauber über Blade-Templates gerendert. Globale Projekt-Architekturregeln packen wir aber in feste Config-Dateien (z.B. `.agents/rules/` oder `.cursorrules`), damit der Task-Context nicht künstlich aufgebläht wird.
5. **Die `Bus::chain()` Pipeline & "Actor-Critic" Review:**
   - **Beschluss:** Wir orchestrieren den TDD-Workflow und das Actor-Critic Modell absolut stabil über Laravels native Batching-Mechanismen:
     `Bus::chain([ new RunCursorBuilderJob(), new RunPhpUnitJob(), new RunCriticReviewJob() ])->dispatch();`
   - *Der "Critic":* Das letzte Kettenglied (`RunCriticReviewJob`) triggert ein günstiges Modell (Gemini Flash / lokales LLM) zur strengen Begutachtung des Git-Diffs gegen unsere `architektur.md`. Bei "FAIL" reißt die Job-Chain ab und iteriert einen Korrektur-Job.
6. **Observability & Auditierung (Das Run-Log):**
   - **Beschluss:** Um zu verhindern, dass die Queue zu einer "Blackbox" verkommt, implementieren wir eine Sub-Tabelle `ai_task_runs`. Anstatt Fehler nur flüchtig im Laravel Job überschreiben zu lassen, dokumentiert diese Tabelle jeden einzelnen Sub-Schritt.
   - *Detail-Logging:* Wir speichern pro Durchlauf: `task_id`, `job_type` (z.B. Test, Builder, Critic), `status`, den verwendeten `prompt_hash`, das `result_log` (die Begründung bei Fehlern) und optional den `git_diff`. Dies ermöglicht uns eine präzise Kosten- und Fehler-Analyse, wenn Aufgaben immer wieder festhängen.
7. **Queue-Radar & Stale State Prevention (MCP Tool):**
   - **Beschluss:** Damit der Planer-Agent nicht auf Basis von veralteten Code-Ständen plant (während der Worker noch 5 offene Tasks abarbeitet), wird eine zwingende Sicherheits-Schranke eingeführt.
   - Es wird ein MCP-Tool (`AiTaskStatusTool`) gebaut, das die aktuelle "Queue-Tiefe" (Anzahl der offenen `pending`/`processing` Tasks) abfragt. Bei der Erstellung von neuen AI-Tasks warnt die Rückgabe des Tools aktiv: *"Achtung, 4 Tasks offen. Lass Cursor erst abarbeiten!"*
8. **Datenbank-Schema (Die Felder):**
   - **Tabelle `ai_tasks` (Der Haupt-Task):**
     `id`, `title`, `description` (Der grobe Plan), `status` (pending, running, completed, failed, blocked), `depends_on_task_id`, `module` (z.B. Invoice, Frontend, oder 'Core' für fachlich unabhängige/globale Tasks), `tags` (JSON, z.B. ['view', 'bugfix']), `created_at`, `updated_at`.
   - **Tabelle `ai_task_runs` (Das Run-Log):**
     `id`, `ai_task_id`, `run_no`, `job_type` (builder, test, critic), `status` (queued, processing, success, failed), `prompt_hash` (Referenz zum Prompt), `git_branch`, `stdout_log` (Ausgabe), `stderr_log` (Fehler), `result_diff` (Was wurde geändert?), `created_at`, `finished_at`.
9. **Die Micro-Task Pflicht (Modulgrenzen):**
   - **Beschluss:** Ein AI-Task darf **niemals** moduliergreifend agieren (Bounded Context). Aufgaben, die Code in Modul A und Modul B erfordern (z.B. Event in A, Listener in B), müssen zwingend in zwei separate Tasks zerschnitten werden, welche über `depends_on_task_id` miteinander verknüpft sind. Dies verhindert Prompt-Bloating, minimiert Halluzinationen beim Cursor-Worker und garantiert saubere Git-Deltas für den Critic-Agenten.
10. **Git Traceability & Branch-Metadaten:**
    - **Beschluss:** Das `AiTaskRun` Log wird um die Felder `git_branch` und `git_commit_hash` erweitert. Damit schaffen wir eine native, extrem performante Traceability zwischen Queue-Einträgen und physischem Code.
    - Dies erlaubt dem Critic-Agenten ein schnelles Review des exakten Git-Diffs, und Entwicklern einen gefahrlosen Rollback im Fall von halluziniertem Cursor-Code, da der exakte Branch sofort identifizierbar ist.
