/**
 * Tests für kit-checkbox-checked (Dashboard #432), Aufgabe T1.
 *
 * Schnittstelle (vom Test festgelegt, der Coder baut danach):
 *   Datei:   vue/shared/checkboxChecked.mjs   (reines ES-Modul, keine Abhängigkeiten, kein Vue)
 *   Export:  export function istAngehakt(checked, value)
 *     checked  so, wie Vue die Prop übergibt:
 *                undefined   Attribut fehlt (nicht gesetzt)
 *                ''          `checked=""` und `<checkbox checked>` (leer gesetzt)
 *                '1' | 'checked' | 'true' | '0' | 'false' | …   Attribut als String
 *                true | false | 1 | 0   gebunden mit `:checked="…"`
 *     value    Prop `value` (String, Zahl, Boolean oder undefined)
 *   Rückgabe: strikt boolean
 *     checked gesetzt (nicht undefined): true bei true, 1 und den Strings '1', 'checked', 'true'
 *       (Groß-/Kleinschreibung egal); alles andere (false, 0, '', '0', 'false', …) → false,
 *       auch wenn value 1 ist.
 *     checked nicht gesetzt (undefined): wie bisher value == 1 || value === true.
 *   Die Funktion ist rein (kein Zwischenspeicher): jeder Aufruf rechnet neu.
 *
 *   Beide Komponenten importieren sie aus diesem Modul:
 *     vue/tailwind/forms/checkbox.vue   (Vue 3, script setup): `import { istAngehakt } from '../../shared/checkboxChecked.mjs'`;
 *         `checked` als Prop mit Typ `any` (kein Boolean im Typ – sonst wird "fehlt" zu false und
 *         `checked=""` zu true); `:checked` bindet eine computed, die istAngehakt(props.checked, props.value)
 *         aufruft (oder direkt :checked="istAngehakt(checked, value)").
 *     vue/bootstrap5/forms/checkbox.vue (Vue 2.7, Options API): gleiche Importzeile; Prop `checked`
 *         ohne Typ Boolean und ohne default; `:checked="…"` bindet eine computed oder Methode, die
 *         istAngehakt(this.checked, this.value) aufruft; kein rohes `checked` am input, kein data-Wert.
 *     `:value="value"` bleibt am input; inheritAttrs bleibt false.
 *
 * Läuft mit: node --test tests/js/checkboxChecked.test.mjs
 */
import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const HIER = dirname(fileURLToPath(import.meta.url));
const MODUL = resolve(HIER, '../../vue/shared/checkboxChecked.mjs');
const TAILWIND = resolve(HIER, '../../vue/tailwind/forms/checkbox.vue');
const BOOTSTRAP = resolve(HIER, '../../vue/bootstrap5/forms/checkbox.vue');

// Dynamischer Import: fehlt das Modul, scheitert nur jeder Modul-Test (nicht die Quelltext-Tests).
async function regel() {
    const m = await import('../../vue/shared/checkboxChecked.mjs');
    assert.equal(typeof m.istAngehakt, 'function', 'Export istAngehakt fehlt');
    return m.istAngehakt;
}

const quelle = (pfad) => readFileSync(pfad, 'utf8');
const ohneLeer = (s) => s.replace(/\s+/g, '');

// ---------- AK1: checked hakt an (value 11, erst recht bei value 1) ----------
const AK1 = [
    ['AK1: checked="1" hakt an (value 11)', '1'],
    ['AK1: checked="checked" hakt an (value 11)', 'checked'],
    ['AK1: checked="true" hakt an (value 11)', 'true'],
    ['AK1: :checked="true" hakt an (value 11)', true],
    ['AK1: :checked="1" hakt an (value 11)', 1],
];
for (const [titel, wert] of AK1) {
    // AK1
    test(titel, async () => {
        const f = await regel();
        assert.equal(f(wert, '11'), true);
        // Gegenprobe: derselbe Aufbau mit value 11 und ohne checked ist nicht angehakt
        assert.equal(f(undefined, '11'), false);
    });
}

// ---------- AK2: checked hakt nicht an, auch bei value 1 ----------
const AK2 = [
    ['AK2: checked="" hakt nicht an auch bei value 1', ''],
    ['AK2: checked="0" hakt nicht an auch bei value 1', '0'],
    ['AK2: checked="false" hakt nicht an auch bei value 1', 'false'],
    ['AK2: :checked="false" hakt nicht an auch bei value 1', false],
    ['AK2: :checked="0" hakt nicht an auch bei value 1', 0],
];
for (const [titel, wert] of AK2) {
    // AK2
    test(titel, async () => {
        const f = await regel();
        assert.equal(f(wert, '1'), false);
        assert.equal(f(wert, 1), false);
        // Gegenprobe: ohne checked ist value 1 angehakt (AK3) – der Aufbau greift
        assert.equal(f(undefined, '1'), true);
    });
}

// ---------- AK3: ohne checked wie bisher ----------
// AK3
test('AK3: ohne checked ist value 1 angehakt (als String oder Zahl oder true)', async () => {
    const f = await regel();
    assert.equal(f(undefined, '1'), true);
    assert.equal(f(undefined, 1), true);
    assert.equal(f(undefined, true), true);
});

// AK3
test('AK3: ohne checked ist value 0 nicht angehakt', async () => {
    const f = await regel();
    assert.equal(f(undefined, '0'), false);
    assert.equal(f(undefined, 0), false);
    assert.equal(f(undefined, false), false);
    assert.equal(f(undefined, '1'), true); // Gegenprobe
});

// AK3
test('AK3: ohne checked ist leerer value nicht angehakt', async () => {
    const f = await regel();
    assert.equal(f(undefined, ''), false);
    assert.equal(f(undefined, '1'), true); // Gegenprobe
});

// AK3
test('AK3: ohne checked und ohne value ist die Checkbox nicht angehakt', async () => {
    const f = await regel();
    assert.equal(f(undefined, undefined), false);
    assert.equal(f(undefined, null), false);
    assert.equal(f(undefined, '1'), true); // Gegenprobe
});

// AK3
test('AK3: nicht gesetzt und leer gesetzt unterscheiden sich bei value 1', async () => {
    const f = await regel();
    assert.equal(f(undefined, '1'), true);
    assert.equal(f('', '1'), false);
});

// ---------- Randfall 1: checked ohne Wert = checked="" ----------
// Randfall 1
test('Randfall 1: checkbox mit checked ohne Wert (Vue liefert leer) hakt nicht an auch nicht bei value 1', async () => {
    const f = await regel();
    assert.equal(f('', '1'), false);
    assert.equal(f('', '11'), false);
    assert.equal(f('1', '11'), true); // Gegenprobe: wer anhaken will, schreibt checked="1"
    assert.equal(f(true, '11'), true); // … oder :checked="true"
});

// ---------- Randfall 2: Groß-/Kleinschreibung ----------
for (const [titel, wert] of [
    ['Randfall 2: checked="TRUE" hakt an', 'TRUE'],
    ['Randfall 2: checked="Checked" hakt an', 'Checked'],
    ['Randfall 2: checked="True" hakt an', 'True'],
    ['Randfall 2: checked="CHECKED" hakt an', 'CHECKED'],
]) {
    // Randfall 2
    test(titel, async () => {
        const f = await regel();
        assert.equal(f(wert, '11'), true);
        assert.equal(f(undefined, '11'), false); // Gegenprobe
    });
}

// Randfall 2
test('Randfall 2: checked="FALSE" und checked="False" haken nicht an (auch bei value 1)', async () => {
    const f = await regel();
    assert.equal(f('FALSE', '1'), false);
    assert.equal(f('False', '1'), false);
    assert.equal(f('true', '1'), true); // Gegenprobe
});

// ---------- Randfall 3: reaktiv ----------
// Randfall 3
test('Randfall 3: die Regel rechnet bei jedem Aufruf neu (keine Zwischenspeicherung)', async () => {
    const f = await regel();
    assert.equal(f('0', '11'), false);
    assert.equal(f('1', '11'), true);
    assert.equal(f('0', '11'), false);
    assert.equal(f(undefined, '0'), false);
    assert.equal(f(undefined, '1'), true);
    assert.equal(f(undefined, '0'), false);
});

// Randfall 3
test('Randfall 3: Tailwind rechnet den Zustand reaktiv aus den Props (computed bzw. Aufruf im Template)', () => {
    const src = quelle(TAILWIND);
    const bindung = src.match(/:checked="\s*([^"]+?)\s*"/);
    assert.ok(bindung, ':checked ist am input gebunden');
    const ausdruck = ohneLeer(bindung[1]);
    if (/^istAngehakt\(/.test(ausdruck)) {
        assert.equal(ausdruck, 'istAngehakt(checked,value)');
        return;
    }
    assert.match(ausdruck, /^\w+$/, 'gebunden an einen Namen (computed)');
    const def = ohneLeer(src).match(new RegExp(`const${ausdruck}=computed\\((.*?)\\);?</script>|const${ausdruck}=computed\\((.*)`));
    assert.ok(def, `computed ${ausdruck} ist definiert`);
    assert.ok((def[1] ?? def[2]).includes('istAngehakt(props.checked,props.value)'),
        `computed ${ausdruck} ruft istAngehakt(props.checked, props.value) auf`);
});

// Randfall 3
test('Randfall 3: Bootstrap5 rechnet den Zustand reaktiv aus den Props (computed oder Methode und kein data-Wert)', () => {
    const src = quelle(BOOTSTRAP);
    const bindung = src.match(/:checked="\s*([^"]+?)\s*"/);
    assert.ok(bindung, ':checked ist am input gebunden');
    const name = bindung[1].match(/^(\w+)(\(\))?$/)?.[1];
    assert.ok(name, `:checked bindet eine computed oder Methode (war: ${bindung[1]})`);
    const skript = ohneLeer(src.slice(src.indexOf('<script')));
    const def = skript.match(new RegExp(`${name}(?:\\(\\)\\{|:function\\(\\)\\{)(.*?)\\}`));
    assert.ok(def, `${name} ist in computed/methods definiert`);
    assert.ok(def[1].includes('istAngehakt(this.checked,this.value)'),
        `${name} ruft istAngehakt(this.checked, this.value) auf`);
    assert.ok(!/\bdata\s*[(:]/.test(src.slice(src.indexOf('<script'))), 'kein data-Wert, der einmalig kopiert wird');
});

// ---------- AK4: value bleibt der gesendete Wert, checked nicht roh am input ----------
const WERT = [
    ['AK4: Tailwind bindet weiter value am input', TAILWIND],
    ['AK4: Bootstrap5 bindet weiter value am input', BOOTSTRAP],
];
for (const [titel, pfad] of WERT) {
    // AK4
    test(titel, () => {
        const inputs = quelle(pfad).match(/<input[^>]*>/gs) ?? [];
        assert.ok(inputs.length > 0, 'input vorhanden');
        for (const tag of inputs) assert.match(tag, /:value="value"/);
    });
}

const PROP = [
    ['AK4: Tailwind deklariert checked als Prop und nicht roh über attrs', TAILWIND],
    ['AK4: Bootstrap5 deklariert checked als Prop und nicht roh über attrs', BOOTSTRAP],
];
for (const [titel, pfad] of PROP) {
    // AK4
    test(titel, () => {
        const src = quelle(pfad);
        if (pfad === TAILWIND) {
            assert.match(src, /defineProps<\{[^}]*\bchecked\??\s*:/s);
        } else {
            assert.match(src, /props:\s*\{[^]*?['"]?checked['"]?\s*:/);
        }
        assert.match(src, /inheritAttrs:\s*false/); // v-bind attrs bleibt für andere Attribute
    });
}

const ROH = [
    ['AK4: Tailwind setzt kein rohes checked-Attribut am input', TAILWIND],
    ['AK4: Bootstrap5 setzt kein rohes checked-Attribut am input', BOOTSTRAP],
];
for (const [titel, pfad] of ROH) {
    // AK4
    test(titel, () => {
        const inputs = quelle(pfad).match(/<input[^>]*>/gs) ?? [];
        assert.ok(inputs.length > 0, 'input vorhanden');
        for (const tag of inputs) {
            assert.ok(!/\schecked(?=[\s>=/])/.test(tag), `rohes checked in: ${tag}`);
        }
        assert.ok(inputs.some((t) => /\s:checked="/.test(t)), 'ein input bindet :checked');
    });
}

const TYP = [
    ['AK4: Tailwind deklariert checked ohne Typ Boolean und ohne default', TAILWIND],
    ['AK4: Bootstrap5 deklariert checked ohne Typ Boolean und ohne default', BOOTSTRAP],
];
for (const [titel, pfad] of TYP) {
    // AK4
    test(titel, () => {
        const src = quelle(pfad);
        const dekl = pfad === TAILWIND
            ? src.match(/\bchecked\??\s*:\s*([^;\n,}]*)/)
            : src.match(/['"]?checked['"]?\s*:\s*(\{[^}]*\}|[^,\n]*)/);
        assert.ok(dekl, 'checked ist deklariert');
        assert.ok(!/boolean/i.test(dekl[1]), `Typ enthält Boolean: ${dekl[1]}`);
        assert.ok(!/default/.test(dekl[1]), `default gesetzt: ${dekl[1]}`);
    });
}

// ---------- AK5: beide Themes gleich ----------
// AK5
test('AK5: beide Komponenten importieren istAngehakt aus demselben Modul', () => {
    const orte = [TAILWIND, BOOTSTRAP].map((pfad) => {
        const m = quelle(pfad).match(/import\s*\{[^}]*\bistAngehakt\b[^}]*\}\s*from\s*['"]([^'"]+)['"]/);
        assert.ok(m, `${pfad} importiert istAngehakt`);
        assert.ok(/\.mjs$/.test(m[1]));
        return resolve(dirname(pfad), m[1]);
    });
    assert.equal(orte[0], MODUL);
    assert.equal(orte[1], MODUL);
});

// AK5
test('AK5: beide rufen istAngehakt mit checked zuerst und value danach auf', () => {
    assert.ok(ohneLeer(quelle(TAILWIND)).includes('istAngehakt(props.checked,props.value)')
        || ohneLeer(quelle(TAILWIND)).includes('istAngehakt(checked,value)'));
    assert.ok(ohneLeer(quelle(BOOTSTRAP)).includes('istAngehakt(this.checked,this.value)'));
});

// AK5
test('AK5: keine der Komponenten enthält die alte eigene Regel (value == 1 / this.value == true) mehr', () => {
    assert.ok(!/value\s*==\s*1/.test(quelle(TAILWIND)));
    assert.ok(!/value\s*==\s*1/.test(quelle(BOOTSTRAP)));
    assert.ok(!/value\s*===?\s*true/.test(quelle(TAILWIND)));
    assert.ok(!/value\s*==\s*true/.test(quelle(BOOTSTRAP)));
});

// AK5
test('AK5: für dieselbe Eingabe entscheidet die gemeinsame Regel gleich (Tabelle beider Themes)', async () => {
    const f = await regel();
    const tabelle = [
        [['1', '11'], true], [['', '1'], false], [[undefined, '1'], true], [[undefined, '0'], false],
        [[true, '11'], true], [[false, '1'], false], [['TRUE', '1'], true], [[0, '1'], false],
    ];
    for (const [[c, v], erwartet] of tabelle) assert.equal(f(c, v), erwartet, `checked=${JSON.stringify(c)} value=${JSON.stringify(v)}`);
});
