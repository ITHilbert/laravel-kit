/**
 * Baut die DataTables column-Definitionen für die DataTable-Komponente.
 * Reines ES-Modul ohne Abhängigkeiten (per `node --test` prüfbar).
 */

const ESCAPES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

/** Escaped & < > " ' ; null/undefined ergeben eine leere Zelle. */
function escapeText(value) {
    if (value === null || value === undefined) {
        return '';
    }
    return String(value).replace(/[&<>"']/g, (c) => ESCAPES[c]);
}

/** Standard-Render: Anzeige als Text, alle anderen Typen (sort/type/filter) Rohwert. */
function textRender(data, type) {
    return type === 'display' ? escapeText(data) : data;
}

function renderFor(col) {
    if (col.render) {
        return col.render;
    }
    return col.html === true ? undefined : textRender;
}

/**
 * @param {Array<{key: string, sortable?: boolean, html?: boolean, render?: Function, defaultContent?: string}>} columns
 * @param {boolean} hasActions hängt die Aktionsspalte als letzte Definition an
 * @returns {unknown[]}
 */
export function buildColumnDefs(columns, hasActions) {
    const defs = columns.map((col) => ({
        data: col.key,
        orderable: col.sortable !== false,
        render: renderFor(col),
        defaultContent: col.defaultContent ?? '',
    }));

    if (hasActions) {
        defs.push({
            data: null,
            orderable: false,
            searchable: false,
            defaultContent: '',
            // Container für die Aktions-Buttons; Inhalt wird nach dem Draw gesetzt.
            render: (_data, _type, row) => {
                const rowJson = JSON.stringify(row).replace(/'/g, '&#39;');
                return `<div class="flex items-center justify-end gap-1" data-actions-cell data-row='${rowJson}'></div>`;
            },
        });
    }

    return defs;
}
