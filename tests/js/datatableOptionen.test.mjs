/**
 * Tests für kit-datatable-optionen (Dashboard #341), Aufgabe T1.
 *
 * Schnittstelle (vom Test festgelegt, der Coder baut danach):
 *   Datei:   vue/tailwind/datatable/optionen.mjs   (reines ES-Modul; darf ./columnDefs.mjs importieren)
 *   Export:  export function baueOptionen(eingabe, defaultLanguage)
 *     eingabe = { columns, hasActions, pageLength, language, datatableOptions, ajaxUrl, rows }
 *       columns          Spalten-Quelle für buildColumnDefs(columns, hasActions)
 *       hasActions       boolean
 *       pageLength       number
 *       language         Objekt|undefined|null  (Prop `language`)
 *       datatableOptions Objekt|undefined|null  (Prop `datatableOptions`)
 *       ajaxUrl          string|undefined (truthy → serverSide + ajax)
 *       rows             Array|undefined  (nur ohne ajaxUrl → data)
 *     defaultLanguage Objekt – Basis der Sprache
 *   Rückgabe: DataTables-Optionsobjekt (neues Objekt, die Eingaben bleiben unverändert):
 *     columns       = buildColumnDefs(columns, hasActions) – stets intern, nie aus datatableOptions
 *     pageLength, responsive:true, searching:true, orderCellsTop:true, language = {...defaultLanguage, ...language}
 *       – alle durch gleichnamige Schlüssel in datatableOptions überschreibbar (language ersetzt komplett)
 *     ajaxUrl → serverSide:true, ajax:ajaxUrl; sonst rows → data:rows – stets intern
 *   DataTable.vue importiert baueOptionen aus ./optionen.mjs und nutzt es in initDataTable().
 *
 * Läuft mit: node --test tests/js/datatableOptionen.test.mjs
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

import { baueOptionen } from '../../vue/tailwind/datatable/optionen.mjs';
import { buildColumnDefs } from '../../vue/tailwind/datatable/columnDefs.mjs';

const XSS = '<img src=x onerror=alert(1)>';
const DEFAULT_LANG = { search: 'Suche', emptyTable: 'Leer' };
const SPALTEN = [{ key: 'name', label: 'Name' }, { key: 'x', label: 'X', sortable: false }];

function baue(extra = {}) {
    return baueOptionen({
        columns: SPALTEN,
        hasActions: false,
        pageLength: 25,
        language: undefined,
        datatableOptions: undefined,
        ajaxUrl: undefined,
        rows: undefined,
        ...extra,
    }, DEFAULT_LANG);
}

/** Wie DataTables: fehlende render-Funktion → Rohwert. */
function zelle(def, data, type) {
    return typeof def.render === 'function' ? def.render(data, type, {}) : data;
}

/** Vergleichbar machen: Funktionen als Marker (render-Funktionen sind je Aufruf neue Instanzen). */
function form(defs) {
    return defs.map((d) => ({ ...d, render: typeof d.render }));
}

const FREMDE_COLUMNS = [{ data: 'name', render: (d) => d }];

// AK1
test('AK1: columns stammen aus buildColumnDefs, nicht aus datatableOptions.columns', () => {
    const o = baue({ datatableOptions: { columns: FREMDE_COLUMNS } });
    assert.notEqual(o.columns, FREMDE_COLUMNS);
    assert.deepEqual(form(o.columns), form(buildColumnDefs(SPALTEN, false)));
    assert.equal(o.columns.length, 2);
    assert.equal(o.columns[1].orderable, false);
});

// AK1
test('AK1: eine Zelle mit <img onerror> wird trotz datatableOptions.columns als Text gerendert', () => {
    const o = baue({ datatableOptions: { columns: FREMDE_COLUMNS } });
    const out = zelle(o.columns[0], XSS, 'display');
    assert.ok(out.includes('&lt;img src=x onerror=alert(1)&gt;'));
    assert.ok(!out.includes('<'));
});

// AK1
test('AK1: Gegenprobe – ohne datatableOptions.columns ist die Zelle ebenso escaped', () => {
    const o = baue();
    assert.ok(zelle(o.columns[0], XSS, 'display').includes('&lt;img'));
});

// AK1
test('AK1: die Aktionsspalte bleibt mit datatableOptions.columns erhalten (hasActions)', () => {
    const o = baue({ hasActions: true, datatableOptions: { columns: FREMDE_COLUMNS } });
    assert.equal(o.columns.length, 3);
    assert.equal(o.columns[2].data, null);
    assert.equal(o.columns[2].searchable, false);
});

// AK1
test('AK1: datatableOptions.columns als leeres Array oder null ersetzt die Spalten nicht', () => {
    for (const c of [[], null, undefined]) {
        const o = baue({ datatableOptions: { columns: c } });
        assert.equal(o.columns.length, 2, `columns=${JSON.stringify(c)}`);
        assert.equal(typeof o.columns[0].render, 'function');
    }
});

// AK1
test('AK1: columns bleiben intern, auch wenn weitere Optionen mitgegeben werden', () => {
    const o = baue({ datatableOptions: { pageLength: 5, columns: FREMDE_COLUMNS, order: [[0, 'asc']] } });
    assert.equal(o.pageLength, 5);
    assert.deepEqual(o.order, [[0, 'asc']]);
    assert.ok(zelle(o.columns[0], XSS, 'display').includes('&lt;img'));
});

// AK1
test('AK1: datatableOptions.columns und columnDefs-Schlüssel – Eingabe wird nicht verändert', () => {
    const opts = { columns: FREMDE_COLUMNS };
    baue({ datatableOptions: opts });
    assert.deepEqual(Object.keys(opts), ['columns']);
    assert.equal(opts.columns, FREMDE_COLUMNS);
});

// AK2
test('AK2: pageLength, order und dom aus datatableOptions gelten in den Optionen', () => {
    const o = baue({ datatableOptions: { pageLength: 7, order: [[1, 'desc']], dom: 'tp' } });
    assert.equal(o.pageLength, 7);
    assert.deepEqual(o.order, [[1, 'desc']]);
    assert.equal(o.dom, 'tp');
});

// AK2
test('AK2: datatableOptions überschreibt auch searching und orderCellsTop', () => {
    const o = baue({ datatableOptions: { searching: false, orderCellsTop: false } });
    assert.equal(o.searching, false);
    assert.equal(o.orderCellsTop, false);
});

// AK2
test('AK2: datatableOptions.responsive überschreibt die interne Vorgabe', () => {
    assert.equal(baue({ datatableOptions: { responsive: false } }).responsive, false);
});

// AK2
test('AK2: Gegenprobe – ohne datatableOptions gelten die internen Vorgaben', () => {
    const o = baue({ pageLength: 25 });
    assert.equal(o.pageLength, 25);
    assert.equal(o.searching, true);
    assert.equal(o.orderCellsTop, true);
    assert.equal(o.responsive, true);
});

// AK2
test('AK2: unbekannte weitere Schlüssel werden unverändert übernommen', () => {
    const fn = () => 1;
    const o = baue({ datatableOptions: { stateSave: true, drawCallback: fn, lengthMenu: [5, 10] } });
    assert.equal(o.stateSave, true);
    assert.equal(o.drawCallback, fn);
    assert.deepEqual(o.lengthMenu, [5, 10]);
});

// AK3
test('AK3: ajaxUrl setzt serverSide und ajax', () => {
    const o = baue({ ajaxUrl: '/api/liste' });
    assert.equal(o.serverSide, true);
    assert.equal(o.ajax, '/api/liste');
    assert.ok(!('data' in o));
});

// AK3
test('AK3: gleichnamige Schlüssel ajax/serverSide in datatableOptions haben bei ajaxUrl keine Wirkung', () => {
    const o = baue({
        ajaxUrl: '/api/liste',
        datatableOptions: { ajax: '/fremd', serverSide: false },
    });
    assert.equal(o.ajax, '/api/liste');
    assert.equal(o.serverSide, true);
});

// AK3
test('AK3: rows setzt data (ohne ajaxUrl)', () => {
    const rows = [{ name: 'a' }];
    const o = baue({ rows });
    assert.equal(o.data, rows);
    assert.ok(!('ajax' in o));
    assert.ok(!('serverSide' in o));
});

// AK3
test('AK3: ein data in datatableOptions hat bei rows keine Wirkung', () => {
    const rows = [{ name: 'a' }];
    const o = baue({ rows, datatableOptions: { data: [{ name: 'fremd' }] } });
    assert.equal(o.data, rows);
});

// AK3
test('AK3: mit ajaxUrl und rows gewinnt ajaxUrl (wie bisher: kein data)', () => {
    const o = baue({ ajaxUrl: '/api/liste', rows: [{ name: 'a' }] });
    assert.equal(o.ajax, '/api/liste');
    assert.ok(!('data' in o));
});

// AK3
test('AK3: leere ajaxUrl (\'\') zählt als nicht gesetzt und rows greift', () => {
    const rows = [];
    const o = baue({ ajaxUrl: '', rows });
    assert.ok(!('ajax' in o));
    assert.equal(o.data, rows);
});

// AK3
test('AK3: weder ajaxUrl noch rows → weder ajax noch data noch serverSide', () => {
    const o = baue();
    assert.ok(!('ajax' in o) && !('data' in o) && !('serverSide' in o));
});

// AK4
test('AK4: baueOptionen ist eine exportierte Funktion im Modul neben columnDefs.mjs', () => {
    assert.equal(typeof baueOptionen, 'function');
});

// AK4
test('AK4: DataTable.vue importiert baueOptionen aus ./optionen.mjs und ruft es auf', () => {
    const src = readFileSync(new URL('../../vue/tailwind/datatable/DataTable.vue', import.meta.url), 'utf8');
    assert.match(src, /import\s*\{[^}]*\bbaueOptionen\b[^}]*\}\s*from\s*['"]\.\/optionen\.mjs['"]/);
    assert.match(src, /\bbaueOptionen\s*\(/);
});

// AK4
test('AK4: DataTable.vue spreizt datatableOptions nicht mehr selbst in die Optionen', () => {
    const src = readFileSync(new URL('../../vue/tailwind/datatable/DataTable.vue', import.meta.url), 'utf8');
    assert.ok(!/\.\.\.\s*\(\s*props\.datatableOptions/.test(src));
});

// Randfall 1
test('Randfall 1: datatableOptions fehlt (undefined/null) → Optionen wie bisher', () => {
    for (const d of [undefined, null]) {
        const o = baue({ datatableOptions: d });
        assert.equal(o.pageLength, 25);
        assert.equal(o.responsive, true);
        assert.equal(o.searching, true);
        assert.equal(o.orderCellsTop, true);
        assert.deepEqual(o.language, DEFAULT_LANG);
        assert.equal(o.columns.length, 2);
    }
});

// Randfall 1
test('Randfall 1: datatableOptions leer ({}) → Optionen wie bisher', () => {
    assert.deepEqual(form(baue({ datatableOptions: {} }).columns), form(baue().columns));
    const o = baue({ datatableOptions: {} });
    assert.deepEqual(Object.keys(o).sort(), Object.keys(baue()).sort());
    assert.equal(o.pageLength, 25);
    assert.deepEqual(o.language, DEFAULT_LANG);
});

// Randfall 2
test('Randfall 2: die Prop language wird mit defaultLanguage zusammengeführt', () => {
    const o = baue({ language: { search: 'Finden', zeros: 'Nix' } });
    assert.deepEqual(o.language, { search: 'Finden', emptyTable: 'Leer', zeros: 'Nix' });
});

// Randfall 2
test('Randfall 2: ohne Prop language und ohne datatableOptions gilt defaultLanguage', () => {
    for (const l of [undefined, null, {}]) {
        assert.deepEqual(baue({ language: l }).language, DEFAULT_LANG);
    }
});

// Randfall 2
test('Randfall 2: ein language in datatableOptions überschreibt die zusammengeführte Sprache', () => {
    const o = baue({
        language: { search: 'Finden' },
        datatableOptions: { language: { search: 'Override' } },
    });
    assert.deepEqual(o.language, { search: 'Override' });
});

// Randfall 2
test('Randfall 2: defaultLanguage und Prop language werden nicht verändert', () => {
    const lang = { search: 'Finden' };
    baue({ language: lang });
    assert.deepEqual(lang, { search: 'Finden' });
    assert.deepEqual(DEFAULT_LANG, { search: 'Suche', emptyTable: 'Leer' });
});

const FREMD = { ajax: '/fremd', data: [{ name: 'fremd' }], serverSide: true };

// AK3
test('AK3: bei ajaxUrl steht kein data aus datatableOptions in den Optionen', () => {
    const o = baue({ ajaxUrl: '/api/liste', datatableOptions: { ...FREMD } });
    assert.equal(o.ajax, '/api/liste');
    assert.equal(o.serverSide, true);
    assert.ok(!('data' in o));
});

// AK3
test('AK3: bei rows steht kein ajax und kein serverSide aus datatableOptions in den Optionen', () => {
    const rows = [{ name: 'a' }];
    const o = baue({ rows, datatableOptions: { ...FREMD } });
    assert.equal(o.data, rows);
    assert.ok(!('ajax' in o));
    assert.ok(!('serverSide' in o));
});

// AK3
test('AK3: ohne ajaxUrl und rows wirkt keiner der Schlüssel ajax, data, serverSide aus datatableOptions', () => {
    for (const k of Object.keys(FREMD)) {
        const o = baue({ datatableOptions: { [k]: FREMD[k] } });
        assert.ok(!(k in o), `Schlüssel ${k}`);
    }
    const o = baue({ datatableOptions: { ...FREMD } });
    assert.ok(!('ajax' in o) && !('data' in o) && !('serverSide' in o));
});

// AK3
test('AK3: bei ajaxUrl und rows wirken die drei Schlüssel aus datatableOptions ebenfalls nicht', () => {
    const o = baue({ ajaxUrl: '/api/liste', rows: [{ name: 'a' }], datatableOptions: { ...FREMD, data: [1] } });
    assert.equal(o.ajax, '/api/liste');
    assert.ok(!('data' in o));
});

// AK3
test('AK3: das Entfernen der drei Schlüssel lässt andere Optionen aus datatableOptions unberührt', () => {
    const o = baue({ ajaxUrl: '/x', datatableOptions: { ...FREMD, pageLength: 3, order: [[0, 'asc']] } });
    assert.equal(o.pageLength, 3);
    assert.deepEqual(o.order, [[0, 'asc']]);
});
