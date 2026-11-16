// Tests für das Combobox-Skript (Spec combobox-ohne-jquery-ui, #1359: AK1–AK8, Randfälle).
// Export: initCombobox(root = document) aus js/combobox.js, Ersatz für das jQuery-UI-Widget ui.combobox.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { beforeEach, describe, test } from 'node:test';
import { FakeEvent, h, installiere } from './fake-dom.mjs';

const lade = () => import('../../js/combobox.js');
const quelle = (pfad) => readFileSync(new URL(`../../${pfad}`, import.meta.url), 'utf8');

let doc;
beforeEach(() => {
    doc = installiere();
});

const FRUECHTE = [
    ['Apfel', '1'],
    ['Birne', '2'],
    ['Ananas', '3'],
    ['Banane', '4'],
    ['', ''],
];

// optionen: [text, value, extraAttribute]; liefert das Select (im form, damit change bubbeln kann)
function selectMit(optionen, wurzel = doc.body) {
    const sel = h(
        'select',
        { class: 'selectcb', name: 'frucht' },
        ...optionen.map(([t, v, a = {}]) => h('option', { value: v, ...a }, t)),
    );
    const form = h('form', {}, sel);
    wurzel.appendChild(form);
    return sel;
}

async function aufbauen(optionen = FRUECHTE) {
    const { initCombobox } = await lade();
    const sel = selectMit(optionen);
    initCombobox(doc);
    const box = sel.nextElementSibling;
    const teile = {
        sel,
        box,
        eingabe: box?.querySelector('input[type=text].kit-combobox-input'),
        knopf: box?.querySelector('button[type=button].kit-combobox-button'),
        liste: box?.querySelector('ul.kit-combobox-list'),
        eintraege: () => box.querySelectorAll('ul.kit-combobox-list li'),
        texte: () => box.querySelectorAll('ul.kit-combobox-list li').map((li) => li.textContent),
        offen: () => !box.querySelector('ul.kit-combobox-list').hasAttribute('hidden'),
        tippe(text) {
            teile.eingabe.value = text;
            teile.eingabe.dispatchEvent(new FakeEvent('input'));
        },
        taste(key) {
            teile.eingabe.dispatchEvent(new FakeEvent('keydown', { key }));
        },
        aktiv: () => teile.eintraege().filter((li) => li.classList.contains('active')).map((li) => li.textContent),
        aenderungen: [],
    };
    sel.parentElement.addEventListener('change', () => teile.aenderungen.push(sel.value));
    return teile;
}

describe('AK1 Aufbau', () => {
    // AK1
    test('Select ausgeblendet, dahinter span.kit-combobox mit Eingabefeld (Wert B), Knopf und versteckter Liste', async () => {
        const { initCombobox } = await lade();
        const sel = selectMit([
            ['A', '1'],
            ['B', '2', { selected: 'selected' }],
        ]);
        initCombobox(document);

        assert.equal(sel.style.display, 'none');
        const box = sel.nextElementSibling;
        assert.ok(box && box.matches('span.kit-combobox'), 'span.kit-combobox direkt hinter dem Select');
        assert.equal(box.querySelector('input[type=text].kit-combobox-input')?.value, 'B');
        assert.ok(box.querySelector('button[type=button].kit-combobox-button'));
        assert.ok(box.querySelector('ul.kit-combobox-list').hasAttribute('hidden'));
        assert.equal(sel.parentElement.querySelectorAll('select.selectcb').length, 1, 'Select bleibt im DOM');
    });

    // AK1
    test('zweiter Aufruf erzeugt keinen zweiten Aufbau', async () => {
        const { initCombobox } = await lade();
        const sel = selectMit([['A', '1']]);
        initCombobox(document);
        assert.equal(doc.querySelectorAll('.kit-combobox').length, 1, 'Aufbau nach erstem Aufruf');
        initCombobox(document);
        assert.equal(doc.querySelectorAll('.kit-combobox').length, 1);
        assert.equal(doc.querySelectorAll('.kit-combobox-input').length, 1);
        assert.ok(sel.nextElementSibling.matches('span.kit-combobox'));
    });

    // AK1
    test('root selbst darf das select.selectcb sein', async () => {
        const { initCombobox } = await lade();
        const sel = selectMit([['A', '1']]);
        initCombobox(sel);
        assert.ok(sel.nextElementSibling?.matches('span.kit-combobox'));
    });

    // Randfall 1
    test('Select ohne ausgewählte Option: Eingabefeld leer, kein Fehler', async () => {
        const t = await aufbauen([
            ['A', '1'],
            ['B', '2'],
        ]);
        assert.equal(t.sel.selectedIndex, -1, 'Aufbau: nichts ausgewählt');
        assert.equal(t.eingabe.value, '');
    });

    // Randfall 3
    test('Wurzelelement ohne Treffer: tut nichts', async () => {
        const { initCombobox } = await lade();
        const leer = h('div', {}, h('select', { class: 'anderes' }, h('option', { value: '1' }, 'A')));
        doc.body.appendChild(leer);
        const davor = leer.innerHTML;
        assert.doesNotThrow(() => initCombobox(leer));
        assert.equal(leer.innerHTML, davor);
    });
});

describe('AK2 Suche', () => {
    // AK2
    test('„AN“ zeigt genau Ananas und Banane, Treffer in <strong>, Platzhalter und Nicht-Treffer fehlen', async () => {
        const t = await aufbauen();
        t.tippe('AN');

        assert.ok(t.offen(), 'Liste ist sichtbar');
        const eintraege = t.eintraege();
        assert.deepEqual(t.texte(), ['Ananas', 'Banane']);
        assert.equal(eintraege[0].innerHTML, '<strong>An</strong><strong>an</strong>as');
        assert.equal(eintraege[1].innerHTML, 'B<strong>an</strong><strong>an</strong>e');
    });

    // Randfall 2
    test('Option mit style="display: none" erscheint nicht in der Liste', async () => {
        const t = await aufbauen([
            ['Apfel', '1'],
            ['Anis', '2', { style: 'display: none' }],
            ['Ananas', '3'],
        ]);
        t.knopf.click();
        assert.deepEqual(t.texte(), ['Apfel', 'Ananas']);
        t.tippe('an');
        assert.deepEqual(t.texte(), ['Ananas']);
    });

    // Randfall 4
    test('kein Auto-Ausfüllen bei nur einem Treffer', async () => {
        const t = await aufbauen();
        t.tippe('Bir');
        assert.deepEqual(t.texte(), ['Birne']);
        assert.equal(t.sel.selectedIndex, -1, 'Select bleibt unverändert');
        assert.equal(t.eingabe.value, 'Bir');
    });
});

describe('AK3 Knopf', () => {
    // AK3
    test('Klick zeigt alle Optionen mit value (ohne Filter), zweiter Klick schließt', async () => {
        const t = await aufbauen();
        t.eingabe.value = 'AN'; // Text im Feld, aber kein Filter über den Knopf
        t.knopf.click();

        assert.ok(t.offen());
        assert.deepEqual(t.texte(), ['Apfel', 'Birne', 'Ananas', 'Banane']);

        t.knopf.click();
        assert.equal(t.offen(), false);
    });
});

describe('AK4 Auswahl per Klick', () => {
    for (const ereignis of ['mousedown', 'click']) {
        // AK4
        test(`Eintrag per ${ereignis}: Select-Wert, Eingabefeld, Liste zu, change bubbelt`, async () => {
            const t = await aufbauen();
            t.knopf.click();
            const birne = t.eintraege().find((li) => li.textContent === 'Birne');
            assert.ok(birne, 'Aufbau: Eintrag Birne in geöffneter Liste');
            assert.equal(t.aenderungen.length, 0);

            birne.dispatchEvent(new FakeEvent(ereignis));

            assert.equal(t.sel.value, '2');
            assert.equal(t.eingabe.value, 'Birne');
            assert.equal(t.offen(), false);
            assert.deepEqual(t.aenderungen, ['2'], 'change einmal, bubbelnd bis zum form');
        });
    }
});

describe('AK5 Fokusverlust', () => {
    // AK5
    test('blur: Text passt ohne Groß-/Kleinschreibung genau auf eine Option, diese ist ausgewählt', async () => {
        const t = await aufbauen();
        assert.equal(t.sel.selectedIndex, -1, 'Aufbau: nichts ausgewählt');
        t.eingabe.value = 'ananas';
        t.eingabe.dispatchEvent(new FakeEvent('blur', { bubbles: false }));
        assert.equal(t.sel.value, '3');
    });

    // AK5
    test('change: Text passt auf keine Option, Feld leer und selectedIndex -1', async () => {
        const t = await aufbauen([
            ['Apfel', '1', { selected: 'selected' }],
            ['Birne', '2'],
        ]);
        assert.equal(t.eingabe.value, 'Apfel', 'Aufbau: Apfel war ausgewählt');
        t.eingabe.value = 'Ana';
        t.eingabe.dispatchEvent(new FakeEvent('change'));
        assert.equal(t.eingabe.value, '');
        assert.equal(t.sel.selectedIndex, -1);
    });
});

describe('AK6 Tastatur', () => {
    // AK6
    test('Pfeil-runter öffnet und markiert den ersten Eintrag; weiter wandert, Pfeil-hoch zurück', async () => {
        const t = await aufbauen();
        assert.equal(t.offen(), false);

        t.taste('ArrowDown');
        assert.ok(t.offen());
        assert.deepEqual(t.aktiv(), ['Apfel']);

        t.taste('ArrowDown');
        assert.deepEqual(t.aktiv(), ['Birne']);

        t.taste('ArrowUp');
        assert.deepEqual(t.aktiv(), ['Apfel']);
    });

    // AK6
    test('Enter wählt den markierten Eintrag wie in AK4', async () => {
        const t = await aufbauen();
        t.taste('ArrowDown');
        t.taste('ArrowDown');
        t.taste('Enter');

        assert.equal(t.sel.value, '2');
        assert.equal(t.eingabe.value, 'Birne');
        assert.equal(t.offen(), false);
        assert.deepEqual(t.aenderungen, ['2']);
    });

    // AK6
    test('Escape schließt die Liste ohne Änderung', async () => {
        const t = await aufbauen([
            ['Apfel', '1', { selected: 'selected' }],
            ['Birne', '2'],
        ]);
        t.taste('ArrowDown');
        assert.ok(t.offen(), 'Aufbau: Liste offen');
        t.taste('ArrowDown');
        t.taste('Escape');

        assert.equal(t.offen(), false);
        assert.equal(t.sel.value, '1');
        assert.equal(t.eingabe.value, 'Apfel');
        assert.equal(t.aenderungen.length, 0);
    });
});

describe('AK7 Sonderzeichen', () => {
    const optionen = [
        ['<b>x</b>', '1'],
        ['A&B', '2'],
        ['foo(bar', '3'],
    ];

    // AK7
    test('HTML in Optionstexten wird als Text angezeigt, nur das Suchwort steht in <strong>', async () => {
        const t = await aufbauen(optionen);
        assert.doesNotThrow(() => t.tippe('x'));

        const li = t.eintraege();
        assert.equal(li.length, 1);
        assert.equal(li[0].textContent, '<b>x</b>');
        assert.equal(li[0].querySelector('b'), null, 'kein <b>-Element aus dem Optionstext');
        assert.deepEqual(li[0].querySelectorAll('strong').map((s) => s.textContent), ['x']);

        t.tippe('a&b');
        assert.deepEqual(t.texte(), ['A&B']);
        assert.deepEqual(t.eintraege()[0].querySelectorAll('strong').map((s) => s.textContent), ['A&B']);
    });

    // AK7
    test('RegExp-Zeichen im Suchtext werfen keinen Fehler', async () => {
        const t = await aufbauen(optionen);
        assert.doesNotThrow(() => t.tippe('('));
        assert.deepEqual(t.texte(), ['foo(bar']);
        assert.deepEqual(t.eintraege()[0].querySelectorAll('strong').map((s) => s.textContent), ['(']);
        assert.doesNotThrow(() => t.tippe('[a-'));
        assert.deepEqual(t.texte(), []);
    });
});

describe('AK8 Quelltext ohne jQuery', () => {
    const dateien = [
        'js/combobox.js',
        'vue/bootstrap4/input/search.vue',
        'vue/bootstrap5/input/search.vue',
        'vue/bootstrap5/forms/search.vue',
    ];
    const verboten = ['jQuery', '$(', '.autocomplete', '.combobox(', 'window.onload'];
    const findet = (text) => verboten.filter((v) => text.includes(v));

    // AK8
    test('Prüfung selbst greift: erkennt jeden verbotenen Ausdruck in einem Beispieltext', () => {
        assert.deepEqual(findet('jQuery $( x.autocomplete y.combobox( window.onload'), verboten);
        assert.deepEqual(findet('const a = 1;'), []);
    });

    for (const datei of dateien) {
        // AK8
        test(`${datei}: vorhanden, nicht leer, kein jQuery/onload`, () => {
            const text = quelle(datei);
            assert.ok(text.length > 100, 'Datei ist nicht leer');
            assert.deepEqual(findet(text), []);
        });
    }

    for (const datei of dateien.slice(1)) {
        // AK8
        test(`${datei}: importiert initCombobox und ruft es in mounted mit this.$el auf`, () => {
            const text = quelle(datei);
            assert.match(text, /import\s*\{[^}]*\binitCombobox\b[^}]*\}\s*from/);
            assert.match(text, /mounted\s*\([^)]*\)\s*\{[\s\S]*?initCombobox\(\s*this\.\$el\s*\)/);
        });
    }
});
