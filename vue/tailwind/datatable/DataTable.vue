<template>
    <div class="overflow-x-auto w-full">
        <table ref="tableRef" class="w-full text-sm text-left text-gray-700 border-collapse">
            <thead>
                <!-- Zeile 1: Spalten-Überschriften -->
                <tr class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3 border-b border-gray-200 font-semibold whitespace-nowrap"
                    >
                        {{ col.label }}
                    </th>
                    <th
                        v-if="hasActions"
                        class="px-4 py-3 border-b border-gray-200 font-semibold whitespace-nowrap text-right"
                    >
                        {{ actionsLabel }}
                    </th>
                </tr>
                <!-- Zeile 2: Filter-Zeile -->
                <tr class="bg-white">
                    <th
                        v-for="col in columns"
                        :key="'filter-' + col.key"
                        class="px-2 py-2 border-b border-gray-200"
                    >
                        <input
                            v-if="col.filterable !== false"
                            type="text"
                            :placeholder="'Filtern...'"
                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 bg-white"
                            @input="onFilterInput($event, col.key)"
                        />
                    </th>
                    <th v-if="hasActions" class="px-2 py-2 border-b border-gray-200"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Wird von DataTables.net befüllt (client-side: rows-Prop; server-side: ajaxUrl) -->
            </tbody>
        </table>

        <!-- Leere-Tabelle-Hinweis (wird von DataTables überschrieben) -->
        <div v-if="!dtInitialized" class="py-8 text-center text-gray-400 text-sm">
            Daten werden geladen...
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue';

// ─── Typen ────────────────────────────────────────────────────────────────────

export interface ColumnDefinition {
    /** Eindeutiger Schlüssel, entspricht dem Datenfeldnamen (z.B. "name", "created_at") */
    key: string;
    /** Anzeigetitel in der Kopfzeile */
    label: string;
    /**
     * Ob diese Spalte einen Filter-Input bekommt.
     * Default: true. Auf false setzen für berechnete / Icon-Spalten.
     */
    filterable?: boolean;
    /**
     * Ob DataTables diese Spalte sortierbar macht.
     * Default: true.
     */
    sortable?: boolean;
    /**
     * Optionale Render-Funktion (DataTables-Spalten-Render-API).
     * Signatur: (data, type, row) => string
     * Nur für client-side Modus nutzbar.
     */
    render?: (data: unknown, type: string, row: Record<string, unknown>) => string;
}

export type ActionType = 'show' | 'edit' | 'delete';

export interface DataTableProps {
    /** Spalten-Definitionen */
    columns: ColumnDefinition[];
    /** Server-side: URL zum Yajra-DataTables-Endpoint */
    ajaxUrl?: string;
    /** Client-side: Array von Datenobjekten */
    rows?: Record<string, unknown>[];
    /**
     * Aktionen die in der letzten Spalte angezeigt werden (Default-Buttons).
     * Wird ignoriert, wenn der #actions-Slot belegt ist.
     */
    actions?: ActionType[];
    /** Beschriftung der Aktions-Kopfzelle. Default: "Aktionen" */
    actionsLabel?: string;
    /**
     * Anzahl der Zeilen pro Seite. Default: 25.
     */
    pageLength?: number;
    /**
     * Sprache für DataTables (Standard: Deutsch).
     * Vollständiges DataTables language-Objekt kann übergeben werden.
     */
    language?: Record<string, unknown>;
    /**
     * Zusätzliche DataTables-Optionen. Werden mit den internen Optionen gemergt.
     * Vorsicht: columns/ajax/data werden intern gesetzt und nicht überschrieben.
     */
    datatableOptions?: Record<string, unknown>;
}

// ─── Props & Emits ────────────────────────────────────────────────────────────

const props = withDefaults(defineProps<DataTableProps>(), {
    actions: () => ['show', 'edit', 'delete'],
    actionsLabel: 'Aktionen',
    pageLength: 25,
});

/**
 * Events die die Komponente emittiert.
 * Das Konsumprojekt hängt seine Routen/Handler an diese Events.
 *
 * @emit show   - Zeigen-Button geklickt  → row-Objekt
 * @emit edit   - Bearbeiten-Button geklickt → row-Objekt
 * @emit delete - Löschen-Button geklickt → row-Objekt
 */
const emit = defineEmits<{
    (e: 'show', row: Record<string, unknown>): void;
    (e: 'edit', row: Record<string, unknown>): void;
    (e: 'delete', row: Record<string, unknown>): void;
}>();

// ─── Slots ────────────────────────────────────────────────────────────────────

/**
 * #actions-Slot (scoped): überschreibt die Default-Buttons vollständig.
 * Slot-Props: { row }
 *
 * Beispiel:
 *   <data-table ...>
 *     <template #actions="{ row }">
 *       <a :href="`/rechnungen/${row.id}`" class="...">Öffnen</a>
 *     </template>
 *   </data-table>
 */
const slots = defineSlots<{
    actions?(props: { row: Record<string, unknown> }): unknown;
}>();

// ─── Template-Refs & State ────────────────────────────────────────────────────

const tableRef = ref<HTMLTableElement | null>(null);
const dtInitialized = ref(false);

// Speichert die DataTables-Instanz – Typ 'any', da DataTables keine offizielle TS-Deklaration mitbringt
// und die Peer-Dependency nicht hart verdrahtet werden soll.
// eslint-disable-next-line @typescript-eslint/no-explicit-any
let dtInstance: any = null;

// ─── Computed ─────────────────────────────────────────────────────────────────

const hasActions = computed<boolean>(() => {
    return (props.actions && props.actions.length > 0) || !!slots.actions;
});

const hasShowAction = computed(() => props.actions?.includes('show'));
const hasEditAction = computed(() => props.actions?.includes('edit'));
const hasDeleteAction = computed(() => props.actions?.includes('delete'));

// ─── Standardsprache (Deutsch) ────────────────────────────────────────────────

const defaultLanguage: Record<string, unknown> = {
    sEmptyTable:     'Keine Daten vorhanden',
    sInfo:           '_START_ bis _END_ von _TOTAL_ Einträgen',
    sInfoEmpty:      '0 bis 0 von 0 Einträgen',
    sInfoFiltered:   '(gefiltert von _MAX_ Einträgen)',
    sLengthMenu:     '_MENU_ Einträge anzeigen',
    sLoadingRecords: 'Wird geladen...',
    sProcessing:     'Bitte warten...',
    sSearch:         'Suche:',
    sZeroRecords:    'Keine Einträge vorhanden',
    oPaginate: {
        sFirst:    'Erste',
        sLast:     'Letzte',
        sNext:     'Nächste',
        sPrevious: 'Zurück',
    },
    oAria: {
        sSortAscending:  ': aktivieren, um Spalte aufsteigend zu sortieren',
        sSortDescending: ': aktivieren, um Spalte absteigend zu sortieren',
    },
};

// ─── Filter-Logik ─────────────────────────────────────────────────────────────

/**
 * Mappt Spalten-Key → DataTables column-Index, um gezielt die korrekte Spalte zu filtern.
 */
const columnIndexMap = computed<Record<string, number>>(() => {
    const map: Record<string, number> = {};
    props.columns.forEach((col, idx) => {
        map[col.key] = idx;
    });
    return map;
});

function onFilterInput(event: Event, key: string): void {
    if (!dtInstance) {
        return;
    }
    const value = (event.target as HTMLInputElement).value;
    const colIndex = columnIndexMap.value[key];
    if (colIndex !== undefined) {
        dtInstance.column(colIndex).search(value).draw();
    }
}

// ─── DataTables Initialisierung ───────────────────────────────────────────────

/**
 * Baut die DataTables column-Definitionen aus den Column-Props.
 */
function buildColumnDefs(): unknown[] {
    const defs: unknown[] = props.columns.map((col) => ({
        data:      col.key,
        orderable: col.sortable !== false,
        render:    col.render ?? undefined,
    }));

    if (hasActions.value) {
        defs.push({
            data:      null,
            orderable: false,
            searchable: false,
            // Render-Funktion baut den Container für die Aktions-Buttons.
            // Der eigentliche Inhalt wird nach dem Draw via delegiertem Event-Listener gesetzt.
            render:    (_data: unknown, _type: string, row: Record<string, unknown>) => {
                const rowJson = JSON.stringify(row).replace(/'/g, '&#39;');
                return `<div class="flex items-center justify-end gap-1" data-actions-cell data-row='${rowJson}'></div>`;
            },
        });
    }

    return defs;
}

/**
 * Prüft ob jQuery und DataTable global verfügbar sind (Peer-Dependency-Annahme:
 * das Konsumprojekt stellt window.$ und window.DataTable via datatable.js bereit).
 */
function resolveDataTable(): unknown {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const w = window as any;
    if (typeof w.DataTable === 'function') {
        return w.DataTable;
    }
    if (typeof w.$ === 'function' && typeof w.$.fn?.DataTable === 'function') {
        return w.$.fn.DataTable;
    }
    return null;
}

/**
 * Rendert Aktions-Buttons in allen Zellen nach einem DataTables draw.
 * Nutzt delegierte Event-Listener (einmalig registriert, nicht je Zeile).
 */
function renderActionCells(): void {
    if (!tableRef.value) {
        return;
    }
    const cells = tableRef.value.querySelectorAll<HTMLDivElement>('[data-actions-cell]');
    cells.forEach((cell) => {
        if (cell.dataset.rendered === '1') {
            return;
        }
        cell.dataset.rendered = '1';

        let row: Record<string, unknown> = {};
        try {
            row = JSON.parse(cell.dataset.row ?? '{}');
        } catch {
            row = {};
        }

        // Slot hat Vorrang — wird nach Mount via Vue teleportiert (nicht möglich in DataTables render-Callback).
        // Daher: wenn kein Slot → Default-Buttons als HTML; wenn Slot → Konsumprojekt muss selbst rendern.
        // Hinweis: Scoped Slots können in DataTables-render-Callbacks nicht direkt genutzt werden.
        // Der #actions-Slot ist für den Fall gedacht, dass der Konsument kein DataTables einsetzt
        // oder die Aktionszelle komplett mit eigenem Vue-Markup übernehmen will.
        // Default-Fallback: Buttons als einfache HTML-Anchor/Buttons mit CSS-Klassen.
        if (hasShowAction.value) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-show';
            btn.title = 'Anzeigen';
            btn.innerHTML = '<i class="fas fa-file-contract"></i>';
            btn.addEventListener('click', () => emit('show', row));
            cell.appendChild(btn);
        }

        if (hasEditAction.value) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-edit';
            btn.title = 'Bearbeiten';
            btn.innerHTML = '<i class="fas fa-pencil-alt"></i>';
            btn.addEventListener('click', () => emit('edit', row));
            cell.appendChild(btn);
        }

        if (hasDeleteAction.value) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-delete';
            btn.title = 'Löschen';
            btn.innerHTML = '<i class="fas fa-minus-circle"></i>';
            btn.addEventListener('click', () => emit('delete', row));
            cell.appendChild(btn);
        }
    });
}

function initDataTable(): void {
    if (!tableRef.value) {
        return;
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const DT: any = resolveDataTable();
    if (!DT) {
        console.error(
            '[DataTable] DataTables.net ist nicht global verfügbar. '
            + 'Stelle sicher, dass datatable.js importiert wurde (window.DataTable oder window.$.fn.DataTable).',
        );
        return;
    }

    const language = { ...defaultLanguage, ...(props.language ?? {}) };

    const options: Record<string, unknown> = {
        columns:       buildColumnDefs(),
        pageLength:    props.pageLength,
        language,
        responsive:    true,
        // Filter-Eingaben laufen über unsere eigene Filter-Zeile — die DataTables-Suche oben ausblenden.
        searching:     true,
        // Zweite Kopfzeile (Filter) soll NICHT als Sortier-Header interpretiert werden.
        orderCellsTop: true,
        ...(props.datatableOptions ?? {}),
    };

    if (props.ajaxUrl) {
        // Server-side / Yajra-Modus
        options.serverSide = true;
        options.ajax       = props.ajaxUrl;
    } else if (props.rows) {
        // Client-side Modus
        options.data = props.rows;
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const w = window as any;
    if (typeof w.$ === 'function') {
        // jQuery-Wrapper (datatables.net-dt bevorzugt diesen Pfad)
        dtInstance = w.$(tableRef.value).DataTable(options);
    } else {
        // Direkter DataTable-Aufruf (datatables.net ohne jQuery)
        dtInstance = new DT(tableRef.value, options);
    }

    // Nach jedem draw → Aktions-Buttons rendern
    dtInstance.on('draw', () => {
        renderActionCells();
    });

    // Ersten draw abwarten
    renderActionCells();
    dtInitialized.value = true;
}

// ─── Lifecycle ────────────────────────────────────────────────────────────────

onMounted(() => {
    initDataTable();
});

onBeforeUnmount(() => {
    if (dtInstance) {
        dtInstance.destroy(true);
        dtInstance = null;
    }
});

// Wenn rows per Prop aktualisiert werden (client-side Reaktivität)
watch(
    () => props.rows,
    (newRows) => {
        if (dtInstance && newRows) {
            dtInstance.clear();
            dtInstance.rows.add(newRows);
            dtInstance.draw();
        }
    },
    { deep: true },
);

// ─── Öffentliche API (expose) ─────────────────────────────────────────────────

defineExpose({
    /**
     * Gibt die rohe DataTables-Instanz zurück, falls das Konsumprojekt
     * erweiterte DataTables-API-Calls benötigt (z.B. dtInstance.ajax.reload()).
     */
    getDtInstance: () => dtInstance,
    /** Erzwingt einen DataTables-Redraw. */
    redraw: () => dtInstance?.draw(),
    /** Leert die Tabelle und lädt neu (server-side). */
    reload: (resetPaging = true) => dtInstance?.ajax?.reload(null, resetPaging),
});
</script>
