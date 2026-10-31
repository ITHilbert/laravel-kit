import { buildColumnDefs } from './columnDefs.mjs';

/**
 * Baut die DataTables-Optionen. Reihenfolge: interne Vorgaben, dann datatableOptions,
 * dann columns (stets intern). ajax/data/serverSide aus datatableOptions werden verworfen
 * und nur über ajaxUrl bzw. rows gesetzt.
 */
export function baueOptionen(eingabe, defaultLanguage) {
    const { columns, hasActions, pageLength, language, datatableOptions, ajaxUrl, rows } = eingabe;
    const { ajax, data, serverSide, ...benutzer } = datatableOptions ?? {};

    const options = {
        pageLength,
        language: { ...defaultLanguage, ...(language ?? {}) },
        responsive: true,
        searching: true,
        orderCellsTop: true,
        ...benutzer,
        columns: buildColumnDefs(columns, hasActions),
    };

    if (ajaxUrl) {
        options.serverSide = true;
        options.ajax = ajaxUrl;
    } else if (rows) {
        options.data = rows;
    }

    return options;
}
