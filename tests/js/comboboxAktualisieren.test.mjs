// Tests für aktualisiereCombobox und die search.vue (Spec combobox-ohne-jquery-ui, #1366: AK11, AK12).
// Fehlerbild: Neu-Rendern der Vue-Komponente setzte select.value zurück, das Eingabefeld zeigte die Nutzerauswahl.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { beforeEach, describe, test } from 'node:test';
import { h, installiere } from './fake-dom.mjs';

const lade = () => import('../../js/combobox.js');
const quelle = (pfad) => readFileSync(new URL(`../../${pfad}`, import.meta.url), 'utf8');

let doc;
beforeEach(() => {
    doc = installiere();
});

const option = (text, value, extra = {}) => h('option', { value, ...extra }, text);

async function aufbauen() {
    const modul = await lade();
    const sel = h('select', { class: 'selectcb' }, option('Apfel', '1', { selected: 'selected' }), option('Birne', '2'), option('Kiwi', '3'));
    doc.body.appendChild(h('form', {}, sel));
    modul.initCombobox(doc);
    const box = sel.nextElementSibling;
    return {
        modul,
        sel,
        eingabe: box.querySelector('input.kit-combobox-input'),
        offnen: () => {
            box.querySelector('button.kit-combobox-button').click();
            return box.querySelectorAll('ul.kit-combobox-list li').map((li) => li.textContent);
        },
    };
}

describe('AK11 aktualisiereCombobox', () => {
    // AK11
    test('Eingabefeld folgt selectedIndex und value von außen, leer bei keiner Auswahl', async () => {
        const t = await aufbauen();
        assert.equal(t.eingabe.value, 'Apfel', 'Aufbau: Apfel gewählt');
        assert.equal(typeof t.modul.aktualisiereCombobox, 'function', 'Export aktualisiereCombobox');

        t.sel.selectedIndex = 1;
        t.modul.aktualisiereCombobox(t.sel);
        assert.equal(t.eingabe.value, 'Birne');

        t.sel.value = '3';
        t.modul.aktualisiereCombobox(t.sel);
        assert.equal(t.eingabe.value, 'Kiwi');

        t.sel.selectedIndex = -1;
        t.modul.aktualisiereCombobox(t.sel);
        assert.equal(t.eingabe.value, '');
    });

    // AK11
    test('ausgetauschte Optionen erscheinen beim nächsten Öffnen, Eingabefeld zeigt die neu gewählte', async () => {
        const t = await aufbauen();
        assert.deepEqual(t.offnen(), ['Apfel', 'Birne', 'Kiwi'], 'Aufbau: alte Optionen');
        t.offnen(); // wieder schließen

        t.sel.replaceChildren(option('Dattel', '7'), option('Feige', '8', { selected: 'selected' }));
        t.modul.aktualisiereCombobox(t.sel);

        assert.equal(t.eingabe.value, 'Feige');
        assert.deepEqual(t.offnen(), ['Dattel', 'Feige']);
    });

    // AK11
    test('Select ohne Combobox: Aufruf wirft nicht und erzeugt nichts', async () => {
        const { aktualisiereCombobox } = await lade();
        const sel = h('select', { class: 'selectcb' }, option('A', '1', { selected: 'selected' }));
        doc.body.appendChild(h('form', {}, sel));
        const davor = doc.innerHTML;

        assert.equal(typeof aktualisiereCombobox, 'function', 'Export aktualisiereCombobox');
        assert.doesNotThrow(() => aktualisiereCombobox(sel));
        assert.equal(doc.innerHTML, davor);
        assert.equal(doc.querySelectorAll('.kit-combobox').length, 0);
        assert.equal(sel.nextElementSibling, null);
    });
});

describe('AK12 search.vue Quelltext', () => {
    const dateien = ['vue/bootstrap4/input/search.vue', 'vue/bootstrap5/input/search.vue', 'vue/bootstrap5/forms/search.vue'];
    const muster = {
        'gewaehlt in data': (t) => /data\s*:\s*function\s*\(\)\s*\{[\s\S]*?gewaehlt\s*:/.test(t) || /data\s*\(\)\s*\{[\s\S]*?gewaehlt\s*:/.test(t),
        'Select nicht mehr per :value="value" gebunden': (t) => !/<select[^>]*:value\s*=\s*"value"/.test(t),
        'Select an gewaehlt gebunden': (t) => /<select[^>]*:value\s*=\s*"gewaehlt"/.test(t) || /<select[^>]*v-model\s*=\s*"gewaehlt"/.test(t),
        'change übernimmt Wert und emittiert input': (t) => /\$emit\(\s*'input'/.test(t) && /@change|v-on:change/.test(t),
        'watch auf value': (t) => /watch\s*:\s*\{[\s\S]*?\bvalue\b\s*[(:]/.test(t),
        'watch auf options': (t) => /watch\s*:\s*\{[\s\S]*?\boptions\b\s*[(:{]/.test(t),
        'aktualisiereCombobox(this.$el) nach $nextTick': (t) => /\$nextTick[\s\S]*?aktualisiereCombobox\(\s*this\.\$el\s*\)/.test(t),
        'Import aktualisiereCombobox': (t) => /import\s*\{[^}]*\baktualisiereCombobox\b[^}]*\}\s*from/.test(t),
    };
    const fehlend = (t) => Object.entries(muster).filter(([, f]) => !f(t)).map(([n]) => n);

    // AK12
    test('Prüfung selbst greift: guter Beispieltext besteht, alter Stand fällt in jedem Punkt durch', () => {
        const gut = `<select :value="gewaehlt" @change="gewaehlt = $event.target.value; $emit('input', gewaehlt)"></select>
import { initCombobox, aktualisiereCombobox } from 'x';
export default { data () { return { gewaehlt: '' }; }, watch: { value () { this.$nextTick(() => aktualisiereCombobox(this.$el)); }, options () {} } }`;
        assert.deepEqual(fehlend(gut), []);
        assert.equal(fehlend('<select :options="options" :value="value">').length, Object.keys(muster).length);
    });

    for (const datei of dateien) {
        // AK12
        test(`${datei}: gewaehlt, change/emit, watch auf value und options, aktualisiereCombobox nach $nextTick`, () => {
            assert.deepEqual(fehlend(quelle(datei)), []);
        });
    }

    // AK12
    test('die drei Dateien sind byte-gleich', () => {
        const [a, b, c] = dateien.map(quelle);
        assert.ok(a.length > 100, 'Datei ist nicht leer');
        assert.equal(b, a);
        assert.equal(c, a);
    });
});
