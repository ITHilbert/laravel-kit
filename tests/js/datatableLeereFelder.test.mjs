/**
 * Tests für kit-datatable-leere-felder (Dashboard #342), Aufgabe T1.
 * Prüft vue/tailwind/datatable/columnDefs.mjs (buildColumnDefs).
 *
 * Läuft mit: node --test tests/js/datatableLeereFelder.test.mjs
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

import { buildColumnDefs } from '../../vue/tailwind/datatable/columnDefs.mjs';

const XSS = '<img src=x onerror=alert(1)>';

function spalte(extra = {}) {
    return buildColumnDefs([{ key: 'name', label: 'Name', ...extra }], false)[0];
}

/**
 * Bildet _fnGetCellData (DataTables 1.13) nach: render bekommt den Rohwert
 * (undefined/null), defaultContent wird nie an render übergeben. Erst wenn das
 * Ergebnis undefined (ohne defaultContent: tn/4) bzw. null / die ganze Zeile ist,
 * setzt DataTables defaultContent ein; null bei display ergibt ''.
 */
function zelleAusZeile(def, row, type = 'display') {
    const dc = def.defaultContent === undefined ? null : def.defaultContent;
    let cell = def.data === null ? row : row[def.data];
    if (typeof def.render === 'function' && type) {
        cell = def.render(cell, type, row);
    }
    if (cell === undefined) {
        assert.notEqual(dc, null, 'tn/4: Requested unknown parameter (kein defaultContent)');
        return dc;
    }
    if ((cell === row || cell === null) && dc !== null) {
        cell = dc;
    }
    if (cell === null && type === 'display') {
        return '';
    }
    return cell;
}

// AK1
test('AK1: jede gebaute Spalte hat defaultContent leer', () => {
    const defs = buildColumnDefs([
        { key: 'a', label: 'A' },
        { key: 'b', label: 'B', sortable: false },
        { key: 'c', label: 'C', html: true },
        { key: 'd', label: 'D', render: (d) => `<b>${d}</b>` },
    ], false);
    assert.equal(defs.length, 4);
    for (const def of defs) {
        assert.strictEqual(def.defaultContent, '');
    }
});

// AK1
test('AK1: fehlendes Feld in der Zeile löst keinen tn/4-Fehler aus (Spalte ohne Zusätze)', () => {
    assert.doesNotThrow(() => zelleAusZeile(spalte(), { id: 1 }));
});

// AK1
test('AK1: fehlendes Feld löst auch bei html:true und eigener render keinen Fehler aus', () => {
    assert.doesNotThrow(() => zelleAusZeile(spalte({ html: true }), { id: 1 }));
    assert.doesNotThrow(() => zelleAusZeile(spalte({ render: () => 'x' }), { id: 1 }));
});

// AK2
test('AK2: fehlendes Feld rendert eine leere Zelle', () => {
    assert.equal(zelleAusZeile(spalte(), { id: 1 }), '');
});

// AK2
test('AK2: undefined rendert eine leere Zelle', () => {
    assert.equal(zelleAusZeile(spalte(), { name: undefined }), '');
});

// AK2
test('AK2: null rendert eine leere Zelle', () => {
    assert.equal(zelleAusZeile(spalte(), { name: null }), '');
});

// AK2
test('AK2: fehlendes Feld bleibt bei sort/type/filter leer, nicht der Text "undefined"', () => {
    for (const t of ['sort', 'type', 'filter']) {
        const out = zelleAusZeile(spalte(), { id: 1 }, t);
        assert.ok(out === '' || out === null || out === undefined, `Typ ${t}`);
        assert.notEqual(String(out), 'undefined');
    }
});

// AK3
test('AK3: vorhandener Wert wird weiter escaped angezeigt', () => {
    assert.equal(zelleAusZeile(spalte(), { name: XSS }), '&lt;img src=x onerror=alert(1)&gt;');
    assert.equal(zelleAusZeile(spalte(), { name: 5 }), '5');
    assert.equal(zelleAusZeile(spalte(), { name: 0 }), '0');
});

// AK3
test('AK3: vorhandener Wert bleibt bei sort/type/filter der Rohwert', () => {
    for (const t of ['sort', 'type', 'filter']) {
        assert.equal(zelleAusZeile(spalte(), { name: XSS }, t), XSS);
    }
});

// AK3
test('AK3: html:true liefert den Rohwert unverändert', () => {
    assert.equal(zelleAusZeile(spalte({ html: true }), { name: XSS }), XSS);
});

// AK3
test('AK3: eigene render-Funktion wird unverändert (selbe Referenz) durchgereicht und ihr Ergebnis unescaped eingesetzt', () => {
    const eigene = (d) => `<b>${d}</b>`;
    const def = spalte({ render: eigene });
    assert.equal(def.render, eigene);
    assert.equal(zelleAusZeile(def, { name: 'x' }), '<b>x</b>');
});

// AK3
test('AK3: eigenes defaultContent der Spalte bleibt erhalten', () => {
    assert.strictEqual(spalte({ defaultContent: '-' }).defaultContent, '-');
    assert.equal(zelleAusZeile(spalte({ defaultContent: '-' }), { id: 1 }), '-');
});

// AK3
test('AK3: Textspalte mit eigenem defaultContent zeigt ihn bei fehlendem Feld und bei null, vorhandener Wert bleibt (escaped)', () => {
    const def = spalte({ defaultContent: '-' });
    assert.equal(zelleAusZeile(def, { id: 1 }), '-');
    assert.equal(zelleAusZeile(def, { name: null }), '-');
    assert.equal(zelleAusZeile(def, { name: undefined }), '-');
    assert.equal(zelleAusZeile(def, { name: XSS }), '&lt;img src=x onerror=alert(1)&gt;');
    assert.equal(zelleAusZeile(def, { name: 0 }), '0');
});

// AK3
test('AK3: html:true-Spalte mit eigenem defaultContent zeigt ihn bei fehlendem Feld und bei null, vorhandener Wert bleibt', () => {
    const def = spalte({ html: true, defaultContent: '<i>-</i>' });
    assert.equal(zelleAusZeile(def, { id: 1 }), '<i>-</i>');
    assert.equal(zelleAusZeile(def, { name: null }), '<i>-</i>');
    assert.equal(zelleAusZeile(def, { name: '<b>x</b>' }), '<b>x</b>');
});

// AK3
test('AK3: Interface ColumnDefinition in DataTable.vue hat ein optionales Feld defaultContent', () => {
    const quelle = readFileSync(new URL('../../vue/tailwind/datatable/DataTable.vue', import.meta.url), 'utf8');
    const iface = quelle.match(/export interface ColumnDefinition \{[\s\S]*?\n\}/);
    assert.ok(iface, 'Interface ColumnDefinition nicht gefunden');
    assert.match(iface[0], /\bdefaultContent\?\s*:\s*string/);
});

// AK3
test('AK3: eigenes defaultContent bleibt auch mit html:true oder eigener render und neben anderen Spalten', () => {
    const defs = buildColumnDefs([
        { key: 'a', defaultContent: 'n/a', html: true },
        { key: 'b', defaultContent: '<i>-</i>', render: () => 'x' },
        { key: 'c' },
    ], false);
    assert.strictEqual(defs[0].defaultContent, 'n/a');
    assert.strictEqual(defs[1].defaultContent, '<i>-</i>');
    assert.strictEqual(defs[2].defaultContent, '');
});

// AK3
test('AK3: data und orderable bleiben unverändert', () => {
    const defs = buildColumnDefs([{ key: 'a' }, { key: 'b', sortable: false }], false);
    assert.equal(defs[0].data, 'a');
    assert.equal(defs[0].orderable, true);
    assert.equal(defs[1].data, 'b');
    assert.equal(defs[1].orderable, false);
});

// Randfall 1
test('Randfall 1: Aktionsspalte hat defaultContent leer', () => {
    const defs = buildColumnDefs([{ key: 'a' }], true);
    assert.equal(defs.length, 2);
    assert.strictEqual(defs[1].defaultContent, '');
});

// Randfall 1
test('Randfall 1: Aktionsspalte behält data null, orderable/searchable false und ihren Container', () => {
    const a = buildColumnDefs([{ key: 'a' }], true)[1];
    assert.equal(a.data, null);
    assert.equal(a.orderable, false);
    assert.equal(a.searchable, false);
    const row = { id: 7 };
    assert.equal(
        a.render(null, 'display', row),
        `<div class="flex items-center justify-end gap-1" data-actions-cell data-row='${JSON.stringify(row)}'></div>`,
    );
});

// Randfall 1
test('Randfall 1: ohne Aktionen gibt es keine Aktionsspalte, alle Spalten haben defaultContent', () => {
    const defs = buildColumnDefs([{ key: 'a' }], false);
    assert.equal(defs.length, 1);
    assert.strictEqual(defs[0].defaultContent, '');
});
