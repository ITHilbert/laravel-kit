/**
 * Tests für datatable-aktionen (Dashboard #1794), Aufgabe TW.
 *
 * Schnittstelle (Spec, Abschnitt „Schnittstelle“):
 *   vue/tailwind/datatable/columnDefs.mjs  buildColumnDefs(columns, hasActions, aktionsSpalte?)
 *   vue/tailwind/datatable/optionen.mjs    baueOptionen(eingabe, …) mit eingabe.aktionsSpalte
 *   vue/tailwind/datatable/zeilenLink.mjs  zeilenUrl(vorlage, daten) → string|null
 *                                          klickIstZeilenKlick(ziel) → boolean
 *
 * Vertrag klickIstZeilenKlick: ruft ziel.closest('a, button, input, select, textarea, label') auf
 * und liefert false, wenn das ein Element ergibt, sonst true.
 *
 * AK9 (Doku) und Randfall 3 (CHANGELOG) sind nicht automatisch prüfbar.
 *
 * Läuft mit: node --test tests/js/datatableAktionen.test.mjs
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

import { buildColumnDefs } from '../../vue/tailwind/datatable/columnDefs.mjs';
import { baueOptionen } from '../../vue/tailwind/datatable/optionen.mjs';
import { zeilenUrl, klickIstZeilenKlick } from '../../vue/tailwind/datatable/zeilenLink.mjs';

const SELEKTOR = 'a, button, input, select, textarea, label';
const XSS = '<a href="/x" onclick="alert(1)">Bearbeiten</a>';
const SPALTEN = [{ key: 'name', label: 'Name' }, { key: 'x', label: 'X', sortable: false }];

/** Wie DataTables: fehlende render-Funktion → Rohwert. */
function zelle(def, data, type, row = {}) {
    return typeof def.render === 'function' ? def.render(data, type, row) : data;
}

function form(defs) {
    return defs.map((d) => ({ ...d, render: typeof d.render }));
}

// AK1
test('AK1: letzte Definition ist die Server-Aktionsspalte, Zellwert unverändert als HTML', () => {
    const defs = buildColumnDefs(SPALTEN, false, 'action');
    assert.equal(defs.length, 3);
    assert.equal(defs[0].data, 'name');
    const letzte = defs[defs.length - 1];
    assert.equal(letzte.data, 'action');
    assert.equal(letzte.orderable, false);
    assert.equal(letzte.searchable, false);
    for (const t of ['display', 'sort', 'type', 'filter']) {
        assert.equal(zelle(letzte, XSS, t), XSS, `Typ ${t}`);
    }
    // Gegenprobe: normale Spalte escaped dasselbe HTML bei display
    assert.ok(!zelle(defs[0], XSS, 'display').includes('<'));
});

// AK2
test('AK2: ohne aktionsSpalte bleibt das Ergebnis wie bisher', () => {
    for (const hasActions of [false, true]) {
        const bisher = buildColumnDefs(SPALTEN, hasActions);
        for (const leer of [undefined, null, '']) {
            const neu = buildColumnDefs(SPALTEN, hasActions, leer);
            assert.deepEqual(form(neu), form(bisher));
        }
        assert.equal(bisher.length, hasActions ? 3 : 2);
    }
    assert.equal(buildColumnDefs(SPALTEN, true)[2].data, null);
});

// AK3
test('AK3: aktionsSpalte und hasActions ersetzen die Standard-Aktionsspalte', () => {
    const defs = buildColumnDefs(SPALTEN, true, 'action');
    assert.equal(defs.length, 3);
    assert.equal(defs.filter((d) => d.data === 'action').length, 1);
    assert.equal(defs.filter((d) => d.data === null).length, 0);
    // Gegenprobe: ohne aktionsSpalte gibt es die Standardspalte (data null)
    assert.equal(buildColumnDefs(SPALTEN, true).filter((d) => d.data === null).length, 1);
});

// AK4
test('AK4: baueOptionen enthält die Server-Aktionsspalte, datatableOptions.columns überschreibt nicht', () => {
    const eingabe = {
        columns: SPALTEN, hasActions: false, pageLength: 25, language: undefined,
        datatableOptions: undefined, ajaxUrl: '/x', rows: undefined, aktionsSpalte: 'action',
    };
    const o = baueOptionen(eingabe, {});
    const letzte = o.columns[o.columns.length - 1];
    assert.equal(o.columns.length, 3);
    assert.equal(letzte.data, 'action');
    assert.equal(letzte.orderable, false);
    assert.equal(letzte.searchable, false);

    const fremd = [{ data: 'fremd' }];
    const o2 = baueOptionen({ ...eingabe, datatableOptions: { columns: fremd } }, {});
    assert.notEqual(o2.columns, fremd);
    assert.deepEqual(form(o2.columns), form(o.columns));
});

// AK5
test('AK5: zeilenUrl ersetzt jedes :id und kodiert die ID', () => {
    assert.equal(zeilenUrl('/users/:id/edit', { id: 7 }), '/users/7/edit');
    assert.equal(zeilenUrl('/a/:id/b/:id', { id: 'x1' }), '/a/x1/b/x1');
    assert.equal(zeilenUrl('/users/:id', { id: 'a/b' }), '/users/a%2Fb');
    assert.equal(zeilenUrl('/users/:id', { id: 0 }), '/users/0');
});

// AK6
test('AK6: ohne id oder ohne Vorlage ist das Ergebnis null', () => {
    assert.equal(zeilenUrl('/users/:id', { id: 7 }), '/users/7'); // Gegenprobe: Aufbau greift
    for (const daten of [{}, { id: null }, { id: undefined }, { id: '' }, null, undefined]) {
        assert.equal(zeilenUrl('/users/:id', daten), null, JSON.stringify(daten));
    }
    for (const vorlage of ['', null, undefined]) {
        assert.equal(zeilenUrl(vorlage, { id: 7 }), null);
    }
});

// AK7
/** Fake-Element: closest(selector) läuft die Eltern entlang und vergleicht Tag-Namen. */
function el(tag, parent = null, aufrufe = []) {
    const self = {
        tag,
        parent,
        closest(selector) {
            aufrufe.push(selector);
            const tags = selector.split(',').map((s) => s.trim());
            for (let n = self; n; n = n.parent) {
                if (tags.includes(n.tag)) return n;
            }
            return null;
        },
    };
    return self;
}

test('AK7: interaktive Ziele und deren Kinder sind kein Zeilenklick, andere Ziele schon', () => {
    const aufrufe = [];
    const td = el('td', null, aufrufe);
    // Gegenprobe: Aufbau greift, Zellen-Ziele zählen
    assert.equal(klickIstZeilenKlick(td), true);
    assert.equal(klickIstZeilenKlick(el('span', td, aufrufe)), true);
    assert.equal(aufrufe[0], SELEKTOR);

    for (const tag of ['a', 'button', 'input', 'select', 'textarea', 'label']) {
        const direkt = el(tag, td);
        const imKind = el('span', el('i', direkt));
        assert.equal(klickIstZeilenKlick(direkt), false, `${tag} direkt`);
        assert.equal(klickIstZeilenKlick(imKind), false, `${tag} Kind`);
    }
});

// AK8
const SFC = readFileSync(new URL('../../vue/tailwind/datatable/DataTable.vue', import.meta.url), 'utf8');

const PRUEFUNGEN_AK8 = {
    'Prop aktionsSpalte': (s) => /aktionsSpalte\s*\??\s*:/.test(s),
    'Prop zeilenLink': (s) => /zeilenLink\s*\??\s*:/.test(s),
    'Import aus ./zeilenLink.mjs': (s) => /import\s*\{[^}]*\bzeilenUrl\b[^}]*\bklickIstZeilenKlick\b[^}]*\}\s*from\s*['"]\.\/zeilenLink\.mjs['"]|import\s*\{[^}]*\bklickIstZeilenKlick\b[^}]*\bzeilenUrl\b[^}]*\}\s*from\s*['"]\.\/zeilenLink\.mjs['"]/.test(s),
    'zeilenUrl genutzt': (s) => /zeilenUrl\s*\(/.test(s),
    'klickIstZeilenKlick genutzt': (s) => /klickIstZeilenKlick\s*\(/.test(s),
    'Klick-Handler auf tbody': (s) => /tbody[\s\S]{0,300}click|click[\s\S]{0,300}tbody/.test(s),
    'ID über dtInstance.row(tr).data()': (s) => /dtInstance[\s\S]{0,40}\.row\(\s*\w+\s*\)\s*\.data\(\)/.test(s),
    'Kopfzelle mit actionsLabel für aktionsSpalte': (s) => /v-if="[^"]*aktionsSpalte[^"]*"[^>]*>[^<]*\{\{\s*actionsLabel\s*\}\}|aktionsSpalte[\s\S]{0,400}\{\{\s*actionsLabel\s*\}\}/.test(s),
    'hasActions falsch bei aktionsSpalte': (s) => /hasActions[\s\S]{0,200}aktionsSpalte|aktionsSpalte[\s\S]{0,200}hasActions/.test(s),
};

// AK8
test('AK8: DataTable.vue deklariert und nutzt aktionsSpalte und zeilenLink laut Spec', () => {
    for (const [name, pruefe] of Object.entries(PRUEFUNGEN_AK8)) {
        assert.ok(pruefe(SFC), `fehlt im Quelltext: ${name}`);
    }
    // Gegenprobe: ein Quelltext ohne die neuen Teile fällt bei jeder Prüfung durch
    const ohne = SFC.replace(/aktionsSpalte|zeilenLink|zeilenUrl|klickIstZeilenKlick/g, 'x');
    for (const [name, pruefe] of Object.entries(PRUEFUNGEN_AK8)) {
        if (name === 'ID über dtInstance.row(tr).data()' || name === 'Klick-Handler auf tbody') continue;
        assert.ok(!pruefe(ohne), `Prüfung zu lasch: ${name}`);
    }
});

// AK8
test('AK8: eine leere Filterzelle gehört zur Server-Aktionsspalte', () => {
    // mindestens zwei Kopf-/Filterzellen sind an aktionsSpalte gebunden (Kopfzeile und Filterzeile)
    const treffer = SFC.match(/<th[^>]*aktionsSpalte[^>]*>/g) ?? [];
    assert.ok(treffer.length >= 2, `th-Zellen mit aktionsSpalte: ${treffer.length}`);
    assert.ok(treffer.some((t) => /px-2 py-2/.test(t)), 'Filterzelle (px-2 py-2) fehlt');
});

// Randfall 1
test('Randfall 1: cursor-pointer hängt an der Prop zeilenLink', () => {
    const stellen = [...SFC.matchAll(/cursor-pointer/g)].map((m) => m.index);
    assert.ok(stellen.length > 0, 'cursor-pointer fehlt');
    assert.ok(
        stellen.some((i) => SFC.slice(Math.max(0, i - 300), i + 300).includes('zeilenLink')),
        'cursor-pointer nicht an zeilenLink gebunden',
    );
    // Gegenprobe der Prüfung: ohne zeilenLink im Umfeld wäre sie nicht erfüllt
    const ohne = SFC.replace(/zeilenLink/g, 'x');
    assert.ok(!stellen.some((i) => ohne.slice(Math.max(0, i - 300), i + 300).includes('zeilenLink')));
});

// Randfall 2
test('Randfall 2: Klick-Handler liegt auf dem tbody, nicht auf der Tabelle oder dem Kopf', () => {
    assert.ok(/tbody[\s\S]{0,300}click|click[\s\S]{0,300}tbody/.test(SFC), 'kein Handler am tbody');
    assert.ok(!/(tableRef\.value|thead)[^\n]{0,60}addEventListener\(\s*['"]click/.test(SFC), 'Handler an Tabelle oder Kopf');
    // Gegenprobe: ein Handler direkt an der Tabelle würde erkannt
    assert.ok(/(tableRef\.value|thead)[^\n]{0,60}addEventListener\(\s*['"]click/.test("tableRef.value.addEventListener('click', f)"));
});
