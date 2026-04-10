<?php

namespace ITHilbert\LaravelKit\Commands;

use Illuminate\Console\Command;

/**
 * Liest und filtert die CHANGELOG_AI.md des aktuellen Projekts.
 *
 * Kanonisches Format eines Eintrags:
 *   ## [#TASK-XXX] Titel — YYYY-MM-DD
 *   **Modul:** ModulName
 *   **TaskID:** #TASK-XXX   (optional)
 *
 *   - Beschreibung ...
 */
class ChangelogShowCommand extends Command
{
    protected $signature = 'changelog:show
                            {--last=10 : Zeige die letzten N Einträge}
                            {--since= : Nur Einträge ab diesem Datum (YYYY-MM-DD)}
                            {--module= : Nur Einträge für dieses Modul}
                            {--task= : Nur den Eintrag mit dieser TaskID (z.B. #TASK-057)}';

    protected $description = 'Gibt gefilterte Einträge aus der CHANGELOG_AI.md aus';

    public function handle(): int
    {
        $changelogPath = base_path('.project/CHANGELOG_AI.md');

        if (! file_exists($changelogPath)) {
            $this->error("CHANGELOG_AI.md nicht gefunden unter: {$changelogPath}");

            return Command::FAILURE;
        }

        $content  = file_get_contents($changelogPath);
        $entries  = $this->parseEntries($content);

        $entries = $this->applyFilters($entries);

        if (empty($entries)) {
            $this->warn('Keine Einträge gefunden, die den Filterkriterien entsprechen.');

            return Command::SUCCESS;
        }

        foreach ($entries as $entry) {
            $this->renderEntry($entry);
        }

        $this->line('');
        $this->line("<fg=gray>── {$this->appliedFiltersLabel()} | " . count($entries) . ' Eintrag/Einträge angezeigt ──</>');

        return Command::SUCCESS;
    }

    /**
     * Zerlegt den Markdown-Inhalt in strukturierte Einträge.
     *
     * @return array<int, array{title: string, date: string, module: string, task_id: string, body: string, raw_header: string}>
     */
    private function parseEntries(string $content): array
    {
        // Trenne an jeder ## Überschrift
        $blocks = preg_split('/^(?=## )/m', $content);
        $entries = [];

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block) || ! str_starts_with($block, '##')) {
                continue;
            }

            $lines  = explode("\n", $block);
            $header = trim(ltrim(array_shift($lines), '#'));

            $date    = $this->extractDate($header);
            $taskId  = $this->extractTaskId($header);
            $title   = $this->extractTitle($header);
            $module  = $this->extractModule($lines);
            $body    = implode("\n", $lines);

            $entries[] = [
                'title'      => $title,
                'date'       => $date,
                'module'     => $module,
                'task_id'    => $taskId,
                'body'       => trim($body),
                'raw_header' => $header,
            ];
        }

        // Neueste zuerst (nach Datum, dann nach Position)
        usort($entries, fn ($a, $b) => strcmp($b['date'], $a['date']));

        return $entries;
    }

    /** @param array<int, array<string, string>> $entries */
    private function applyFilters(array $entries): array
    {
        $last   = (int) $this->option('last');
        $since  = $this->option('since');
        $module = $this->option('module');
        $task   = $this->option('task');

        if ($since) {
            $entries = array_values(array_filter(
                $entries,
                fn ($e) => $e['date'] >= $since
            ));
        }

        if ($module) {
            $entries = array_values(array_filter(
                $entries,
                fn ($e) => str_contains(strtolower($e['module']), strtolower($module))
            ));
        }

        if ($task) {
            $entries = array_values(array_filter(
                $entries,
                fn ($e) => strtolower($e['task_id']) === strtolower($task)
            ));

            return $entries; // --task gibt immer nur 1 Eintrag
        }

        if ($last > 0) {
            $entries = array_slice($entries, 0, $last);
        }

        return $entries;
    }

    /** @param array<string, string> $entry */
    private function renderEntry(array $entry): void
    {
        $taskLabel = $entry['task_id'] ? " <fg=cyan>[{$entry['task_id']}]</>" : '';
        $date      = $entry['date'] ? " <fg=gray>({$entry['date']})</>" : '';
        $module    = $entry['module'] ? " <fg=yellow>Modul: {$entry['module']}</>" : '';

        $this->line('');
        $this->line("<fg=white;options=bold>▶ {$entry['title']}</>{$taskLabel}{$date}");

        if ($module) {
            $this->line("  {$module}");
        }

        if (! empty($entry['body'])) {
            $bodyLines = array_filter(
                explode("\n", $entry['body']),
                function (string $l): bool {
                    $trimmed = trim($l);

                    // Metadaten-Zeilen und HR-Trennlinien überspringen
                    return $trimmed !== ''
                        && ! str_starts_with($trimmed, '**Modul:**')
                        && ! str_starts_with($trimmed, '**TaskID:**')
                        && $trimmed !== '---';
                }
            );

            // Kontext sparen: max. 10 Zeilen
            foreach (array_slice(array_values($bodyLines), 0, 10) as $line) {
                $this->line("  <fg=gray>{$line}</>");
            }
        }
    }

    private function extractDate(string $header): string
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $header, $m)) {
            return $m[1];
        }

        return '';
    }

    private function extractTaskId(string $header): string
    {
        if (preg_match('/#TASK-\d+/i', $header, $m)) {
            return $m[0];
        }

        return '';
    }

    private function extractTitle(string $header): string
    {
        // Entferne TaskID-Klammer z.B. [#TASK-057]
        $title = preg_replace('/\[#?TASK-\d+\]\s*/i', '', $header);
        // Entferne Datum
        $title = preg_replace('/—?\s*\d{4}-\d{2}-\d{2}\s*/', '', $title ?? '');

        return trim($title ?? $header, ' []—-');
    }

    /** @param array<int, string> $lines */
    private function extractModule(array $lines): string
    {
        foreach ($lines as $line) {
            if (preg_match('/\*{0,2}Modul:\*{0,2}\s*(.+)/i', $line, $m)) {
                return trim($m[1]);
            }
        }

        return '';
    }

    private function appliedFiltersLabel(): string
    {
        $parts = [];

        if ($this->option('since')) {
            $parts[] = 'seit ' . $this->option('since');
        }
        if ($this->option('module')) {
            $parts[] = 'Modul=' . $this->option('module');
        }
        if ($this->option('task')) {
            $parts[] = 'task=' . $this->option('task');
        }
        if (! $this->option('task')) {
            $parts[] = 'last=' . $this->option('last');
        }

        return implode(', ', $parts);
    }
}
