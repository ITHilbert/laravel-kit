/**
 * Tests für kit-datatable-text (Dashboard #261), Aufgabe T1.
 *
 * Schnittstelle (vom Test festgelegt, der Coder baut danach):
 *   Datei:   vue/tailwind/datatable/columnDefs.mjs   (reines ES-Modul, keine Abhängigkeiten)
 *   Export:  export function buildColumnDefs(columns, hasActions)
 *     columns    Array<{ key, label?, sortable?, html?, render? }>
 *     hasActions boolean – hängt die Aktionsspalte als letzte Definition an
 *   Rückgabe: Array von DataTables-column-Definitionen:
 *     { data: col.key, orderable: col.sortable !== false, render: <function|undefined> }
 *     Die Aktionsspalte genau wie bisher in buildColumnDefs() der DataTable.vue.
 *   render-Vertrag: render(data, type, row). Ohne html/render: type 'display' liefert
 *     den escapten Text, jeder andere Typ den unveränderten Rohwert.
 *     Mit html:true darf render fehlen (undefined) oder den Rohwert liefern.
 *     Eine eigene render-Funktion wird unverändert (selbe Referenz) durchgereicht.
 *
 * Läuft mit: node --test tests/js/datatableSpalten.test.mjs
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';

import { buildColumnDefs } from '../../vue/tailwind/datatable/columnDefs.mjs';

const XSS = '<img src=x onerror=alert(1)>';

/** Wie DataTables: fehlende render-Funktion → Rohwert (data). */
function zelle(def, data, type, row = {}) {
    return typeof def.render === 'function' ? def.render(data, type, row) : data;
}

function spalte(extra = {}) {
    return buildColumnDefs([{ key: 'name', label: 'Name', ...extra }], false)[0];
}

/** Dekodiert die fünf erlaubten Entities (Gegenprobe: Escaping verlustfrei). */
function dekodiere(s) {
    return s
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"')
        .replace(/&(#39|#x27|apos);/g, "'")
        .replace(/&amp;/g, '&');
}

// AK1
test('AK1: display liefert escapten Text statt HTML', () => {
    const out = zelle(spalte(), XSS, 'display');
    assert.ok(out.includes('&lt;img src=x onerror=alert(1)&gt;'));
    assert.ok(!out.includes('<'));
});

// AK1
test('AK1: Spalte ohne html/render hat eine render-Funktion (sonst setzt DataTables Rohwert als HTML ein)', () => {
    assert.equal(typeof spalte().render, 'function');
});

// AK1
test('AK1: html:false verhält sich wie fehlendes html', () => {
    assert.ok(zelle(spalte({ html: false }), XSS, 'display').includes('&lt;img'));
});

// AK1
test('AK1: data und orderable der Spalte bleiben erhalten', () => {
    const defs = buildColumnDefs([
        { key: 'a', label: 'A' },
        { key: 'b', label: 'B', sortable: false },
    ], false);
    assert.equal(defs.length, 2);
    assert.equal(defs[0].data, 'a');
    assert.equal(defs[0].orderable, true);
    assert.equal(defs[1].data, 'b');
    assert.equal(defs[1].orderable, false);
});

// AK2
test('AK2: html:true liefert für display den Rohwert', () => {
    assert.equal(zelle(spalte({ html: true }), XSS, 'display'), XSS);
});

// AK2
test('AK2: html:true liefert auch für sort/type/filter den Rohwert', () => {
    for (const t of ['sort', 'type', 'filter']) {
        assert.equal(zelle(spalte({ html: true }), XSS, t), XSS);
    }
});

// AK3
test('AK3: eigene render-Funktion wird unverändert durchgereicht', () => {
    const eigene = (d) => `<b>${d}</b>`;
    assert.equal(spalte({ render: eigene }).render, eigene);
});

// AK3
test('AK3: Ergebnis der eigenen render wird unescaped eingesetzt', () => {
    const def = spalte({ render: (d) => `<b>${d}</b>` });
    assert.equal(zelle(def, 'x', 'display'), '<b>x</b>');
});

// AK3
test('AK3: eigene render erhält data, type und row', () => {
    const aufrufe = [];
    const def = spalte({ render: (...a) => { aufrufe.push(a); return 'ok'; } });
    const row = { id: 1 };
    zelle(def, 'v', 'sort', row);
    assert.deepEqual(aufrufe, [['v', 'sort', row]]);
});

// AK3
test('AK3: eigene render gewinnt gegenüber html:true', () => {
    const eigene = () => 'eigen';
    assert.equal(spalte({ html: true, render: eigene }).render, eigene);
});

// AK3
test('AK3: eigene render gewinnt auch bei html:false', () => {
    const eigene = () => 'eigen';
    assert.equal(spalte({ html: false, render: eigene }).render, eigene);
});

// AK4
test('AK4: sort, type und filter liefern den Rohwert', () => {
    for (const t of ['sort', 'type', 'filter']) {
        assert.equal(zelle(spalte(), XSS, t), XSS, `Typ ${t}`);
    }
});

// AK4
test('AK4: Rohwert bleibt für sort/type/filter auch mit Sonderzeichen, Zahl und null unverändert', () => {
    for (const t of ['sort', 'type', 'filter']) {
        assert.equal(zelle(spalte(), `a & "b" 'c'`, t), `a & "b" 'c'`);
        assert.equal(zelle(spalte(), 5, t), 5);
        assert.equal(zelle(spalte(), null, t), null);
    }
});

// AK5
test('AK5: ohne Aktionen keine zusätzliche Spalte', () => {
    assert.equal(buildColumnDefs([{ key: 'a' }, { key: 'b' }], false).length, 2);
});

// AK5
test('AK5: mit Aktionen hängt genau eine Spalte am Ende an', () => {
    const defs = buildColumnDefs([{ key: 'a' }, { key: 'b' }], true);
    assert.equal(defs.length, 3);
    assert.equal(defs[0].data, 'a');
    assert.equal(defs[1].data, 'b');
});

// AK5
test('AK5: Aktionsspalte hat data null, nicht sortier- und nicht suchbar', () => {
    const a = buildColumnDefs([{ key: 'a' }], true)[1];
    assert.equal(a.data, null);
    assert.equal(a.orderable, false);
    assert.equal(a.searchable, false);
    assert.equal(typeof a.render, 'function');
});

// AK5
test('AK5: Aktionsspalte rendert denselben Container wie bisher', () => {
    const a = buildColumnDefs([{ key: 'a' }], true)[1];
    const row = { id: 7, name: 'Anna' };
    assert.equal(
        a.render(null, 'display', row),
        `<div class="flex items-center justify-end gap-1" data-actions-cell data-row='${JSON.stringify(row)}'></div>`,
    );
});

// AK5
test('AK5: Apostroph in der Zeile wird im data-row-Attribut als &#39; kodiert', () => {
    const a = buildColumnDefs([{ key: 'a' }], true)[1];
    const out = a.render(null, 'display', { name: "O'Brien" });
    assert.ok(out.includes('O&#39;Brien'));
    assert.ok(!out.includes("O'Brien"));
});

// Randfall 1
test('Randfall 1: null und undefined ergeben bei display eine leere Zelle', () => {
    assert.equal(zelle(spalte(), null, 'display'), '');
    assert.equal(zelle(spalte(), undefined, 'display'), '');
});

// Randfall 2
test('Randfall 2: Zahlen und Booleans erscheinen bei display als Text', () => {
    assert.equal(zelle(spalte(), 5, 'display'), '5');
    assert.equal(zelle(spalte(), 0, 'display'), '0');
    assert.equal(zelle(spalte(), 1.5, 'display'), '1.5');
    assert.equal(zelle(spalte(), true, 'display'), 'true');
    assert.equal(zelle(spalte(), false, 'display'), 'false');
});

// Randfall 3
test('Randfall 3: & < > " \' werden einzeln escaped', () => {
    assert.equal(zelle(spalte(), '&', 'display'), '&amp;');
    assert.equal(zelle(spalte(), '<', 'display'), '&lt;');
    assert.equal(zelle(spalte(), '>', 'display'), '&gt;');
    const q = zelle(spalte(), '"', 'display');
    assert.ok(!q.includes('"') && dekodiere(q) === '"');
    const a = zelle(spalte(), "'", 'display');
    assert.ok(!a.includes("'") && dekodiere(a) === "'");
});

// Randfall 3
test('Randfall 3: Invariante über Angriffsvarianten – kein <, >, ", \' im Ergebnis, verlustfrei dekodierbar', () => {
    const angriffe = [
        XSS,
        '<script>alert(1)</script>',
        '"><svg onload=alert(1)>',
        "' onmouseover='alert(1)",
        '<style>*{}</style>',
        '<!-- x -->',
        '&lt;already&gt;',
        '&amp;&amp;',
        '<<a>>',
        'a & b < c > d " e \' f',
    ];
    for (const praefix of ['', 'text ', '<p>', '<!-- c -->']) {
        for (const a of angriffe) {
            const eingabe = praefix + a;
            const out = zelle(spalte(), eingabe, 'display');
            assert.ok(!/[<>"']/.test(out), `Rohzeichen in: ${out}`);
            assert.equal(dekodiere(out), eingabe);
        }
    }
});

// Randfall 3
test('Randfall 3: bereits vorhandene Entities werden nicht übernommen (&amp;lt; statt &lt;)', () => {
    assert.equal(zelle(spalte(), '&lt;', 'display'), '&amp;lt;');
});
