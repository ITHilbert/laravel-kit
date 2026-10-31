// Tests für das Tab-Skript (T2: AK5, Randfall 3).
//
// Vertrag (data-Attribute, vom Kit-Skript `js/tabs.js` ausgewertet):
//   tabs-header:  Hauptelement  data-kit-tabs="<gruppe>"
//   tab-header:   Hauptelement  data-kit-tab="<name>"      (innerhalb des tabs-header derselben Gruppe)
//   tabs-body:    Hauptelement  data-kit-tabs-body="<gruppe>"
//   tab-body:     Hauptelement  data-kit-tab-body="<name>" (innerhalb des tabs-body derselben Gruppe)
// Export: initTabs(root = document). Aktiv = Klasse `active` (tab-header und tab-body)
// plus Klasse `show` am tab-body und aria-selected="true" am tab-header; inaktiv = ohne diese.
// Namen dürfen in verschiedenen Gruppen gleich sein; die Gruppe entscheidet.
// Ohne Gruppenangabe erzeugen die Components data-kit-tabs="" bzw. data-kit-tabs-body="" (leer).
// Vertrag für leere Gruppen-ID (Randfall 3, präzisiert): Ein tabs-header gehört zum nächsten folgenden
// [data-kit-tabs-body] in Dokumentreihenfolge — auch wenn beide in verschiedenen Elternelementen stehen
// (z. B. Header in div.card-main-header, Body in div.card-body).
import assert from 'node:assert/strict';
import { beforeEach, describe, test } from 'node:test';
import { h, installiere } from './fake-dom.mjs';

const { initTabs } = await import('../../js/tabs.js');

let doc;

function gruppe(id, namen, aktiv) {
    const header = h(
        'ul',
        { 'data-kit-tabs': id },
        ...namen.map((n) =>
            h('li', {}, h('button', { 'data-kit-tab': n, class: n === aktiv ? 'tab-header active' : 'tab-header', 'aria-selected': String(n === aktiv) }, h('span', { class: 'inhalt' }, n))),
        ),
    );
    const body = h(
        'div',
        { 'data-kit-tabs-body': id },
        ...namen.map((n) => h('div', { 'data-kit-tab-body': n, class: n === aktiv ? 'tab-body active show' : 'tab-body' }, h('p', {}, `${id}-${n}`))),
    );
    return { header, body, knopf: (n) => header.querySelector(`[data-kit-tab="${n}"]`), panel: (n) => body.querySelector(`[data-kit-tab-body="${n}"]`) };
}

const aktiv = (el) => el.classList.contains('active');

beforeEach(() => {
    doc = installiere();
});

describe('Tabs ohne Vue', () => {
    // AK5
    test('Klick auf einen Reiter zeigt dessen Inhalt', () => {
        const g = gruppe('g1', ['a', 'b', 'c'], 'a');
        doc.body.append(g.header, g.body);
        initTabs(doc);

        assert.equal(aktiv(g.panel('b')), false, 'Gegenprobe: b ist vor dem Klick verborgen');
        g.knopf('b').click();

        assert.equal(aktiv(g.panel('b')), true);
        assert.equal(g.panel('b').classList.contains('show'), true);
        assert.equal(aktiv(g.knopf('b')), true);
        assert.equal(g.knopf('b').getAttribute('aria-selected'), 'true');
    });

    // AK5
    test('Klick auf einen Reiter blendet die anderen Inhalte aus', () => {
        const g = gruppe('g1', ['a', 'b', 'c'], 'a');
        doc.body.append(g.header, g.body);
        initTabs(doc);

        assert.equal(aktiv(g.panel('a')), true, 'Gegenprobe: a ist vor dem Klick sichtbar');
        g.knopf('c').click();

        assert.equal(aktiv(g.panel('c')), true);
        for (const n of ['a', 'b']) {
            assert.equal(aktiv(g.panel(n)), false, `Inhalt ${n} bleibt sichtbar`);
            assert.equal(g.panel(n).classList.contains('show'), false, `Inhalt ${n} hat noch show`);
            assert.equal(aktiv(g.knopf(n)), false, `Reiter ${n} bleibt markiert`);
            assert.equal(g.knopf(n).getAttribute('aria-selected'), 'false');
        }
    });

    // AK5
    test('Klick auf ein Kind des Reiters (z. B. Symbol, Text) schaltet ebenfalls um', () => {
        const g = gruppe('g1', ['a', 'b'], 'a');
        doc.body.append(g.header, g.body);
        initTabs(doc);

        g.knopf('b').querySelector('.inhalt').click();

        assert.equal(aktiv(g.panel('b')), true);
        assert.equal(aktiv(g.panel('a')), false);
    });

    // AK5
    test('wiederholtes Umschalten hin und zurück funktioniert', () => {
        const g = gruppe('g1', ['a', 'b'], 'a');
        doc.body.append(g.header, g.body);
        initTabs(doc);

        g.knopf('b').click();
        assert.equal(aktiv(g.panel('b')), true, 'Gegenprobe: erster Klick hat umgeschaltet');
        g.knopf('a').click();

        assert.equal(aktiv(g.panel('a')), true);
        assert.equal(aktiv(g.panel('b')), false);
        g.knopf('a').click();
        assert.equal(aktiv(g.panel('a')), true, 'Klick auf den aktiven Reiter lässt ihn aktiv');
    });

    // AK5
    test('das Skript braucht kein Vue und keine globale Funktion', () => {
        const g = gruppe('g1', ['a', 'b'], 'a');
        doc.body.append(g.header, g.body);
        initTabs(doc);
        g.knopf('b').click();

        assert.equal(aktiv(g.panel('b')), true);
        assert.equal(globalThis.Vue, undefined);
    });

    // AK5
    test('Klick außerhalb der Reiter ändert nichts', () => {
        const g = gruppe('g1', ['a', 'b'], 'a');
        const sonst = h('button', { id: 'x' });
        doc.body.append(g.header, g.body, sonst);
        initTabs(doc);

        g.knopf('b').click();
        assert.equal(aktiv(g.panel('b')), true, 'Gegenprobe: Umschalten funktioniert');
        sonst.click();

        assert.equal(aktiv(g.panel('b')), true);
        assert.equal(aktiv(g.panel('a')), false);
    });

    // AK5
    test('initTabs(root) bindet nur Tabs unter dem übergebenen root', () => {
        const innen = h('section');
        const g = gruppe('g1', ['a', 'b'], 'a');
        innen.append(g.header, g.body);
        const f = gruppe('g2', ['a', 'b'], 'a');
        doc.body.append(innen, f.header, f.body);
        initTabs(innen);

        g.knopf('b').click();
        assert.equal(aktiv(g.panel('b')), true, 'Gegenprobe: Tabs in root schalten');
        f.knopf('b').click();

        assert.equal(aktiv(f.panel('b')), false);
        assert.equal(aktiv(f.panel('a')), true);
    });
});

describe('Zwei Tab-Gruppen', () => {
    // Randfall 3
    test('Umschalten in Gruppe 1 lässt Gruppe 2 unverändert, auch bei gleichen Namen', () => {
        const g1 = gruppe('g1', ['a', 'b'], 'a');
        const g2 = gruppe('g2', ['a', 'b'], 'a');
        doc.body.append(g1.header, g1.body, g2.header, g2.body);
        initTabs(doc);

        g1.knopf('b').click();

        assert.equal(aktiv(g1.panel('b')), true, 'Gegenprobe: Gruppe 1 hat umgeschaltet');
        assert.equal(aktiv(g1.panel('a')), false);
        assert.equal(aktiv(g2.panel('a')), true);
        assert.equal(aktiv(g2.panel('b')), false);
        assert.equal(aktiv(g2.knopf('a')), true);
        assert.equal(aktiv(g2.knopf('b')), false);
    });

    // Randfall 3
    test('Umschalten in Gruppe 2 lässt Gruppe 1 unverändert', () => {
        const g1 = gruppe('g1', ['a', 'b'], 'b');
        const g2 = gruppe('g2', ['a', 'b'], 'a');
        doc.body.append(g1.header, g1.body, g2.header, g2.body);
        initTabs(doc);

        g2.knopf('b').click();

        assert.equal(aktiv(g2.panel('b')), true, 'Gegenprobe: Gruppe 2 hat umgeschaltet');
        assert.equal(aktiv(g1.panel('b')), true);
        assert.equal(aktiv(g1.panel('a')), false);
    });

    // Randfall 3
    test('beide Gruppen schalten nacheinander jeweils unabhängig', () => {
        const g1 = gruppe('g1', ['x', 'y', 'z'], 'x');
        const g2 = gruppe('g2', ['x', 'y', 'z'], 'x');
        doc.body.append(g1.header, g1.body, g2.header, g2.body);
        initTabs(doc);

        g1.knopf('y').click();
        g2.knopf('z').click();

        assert.equal(aktiv(g1.panel('y')), true);
        assert.equal(aktiv(g1.panel('z')), false);
        assert.equal(aktiv(g2.panel('z')), true);
        assert.equal(aktiv(g2.panel('y')), false);
        assert.equal(aktiv(g2.panel('x')), false);
        assert.equal(aktiv(g1.panel('x')), false);
    });
});

// Markup wie es tabs-header/tab-header/tabs-body/tab-body ohne Gruppenangabe erzeugen
// (Blade-Templates in src/Views/kit/tailwind; leere Gruppen-ID).
function standard(prefix, namen, aktiv) {
    const header = h(
        'ul',
        { class: 'tabs-header', role: 'tablist', 'data-kit-tabs': '' },
        ...namen.map((n) =>
            h(
                'li',
                { class: 'block', role: 'presentation' },
                h('button', {
                    type: 'button',
                    id: `${n}-tab`,
                    role: 'tab',
                    'aria-selected': String(n === aktiv),
                    'data-kit-tab': n,
                    class: n === aktiv ? 'tab-header active' : 'tab-header',
                }),
            ),
        ),
    );
    const body = h(
        'div',
        { class: 'tabs-body', 'data-kit-tabs-body': '' },
        ...namen.map((n) => h('div', { id: n, role: 'tabpanel', 'aria-labelledby': `${n}-tab`, 'data-kit-tab-body': n, class: n === aktiv ? 'tab-body active show' : 'tab-body' }, h('p', {}, `${prefix}-${n}`))),
    );
    return { header, body, knopf: (n) => header.querySelector(`[data-kit-tab="${n}"]`), panel: (n) => body.querySelector(`[data-kit-tab-body="${n}"]`) };
}

describe('Zwei Tab-Gruppen im Standard-Markup (leere Gruppen-ID)', () => {
    const layouts = {
        'als Geschwister im selben Elternelement': (doc, g1, g2) => doc.body.append(g1.header, g1.body, g2.header, g2.body),
        'je Gruppe in eigenem Container': (doc, g1, g2) => {
            doc.body.append(h('section', {}, g1.header, g1.body), h('section', {}, g2.header, g2.body));
        },
    };

    for (const [name, baue] of Object.entries(layouts)) {
        // Randfall 3
        test(`Klick in Gruppe 1 schaltet nur Gruppe 1 (${name})`, () => {
            const g1 = standard('g1', ['a', 'b'], 'a');
            const g2 = standard('g2', ['c', 'd'], 'c');
            baue(doc, g1, g2);
            initTabs(doc);

            g1.knopf('b').click();

            assert.equal(aktiv(g1.panel('b')), true, 'Gegenprobe: Gruppe 1 hat umgeschaltet');
            assert.equal(aktiv(g1.panel('a')), false);
            assert.equal(aktiv(g1.knopf('b')), true);
            assert.equal(aktiv(g2.panel('c')), true, 'Gruppe 2 darf nicht mitschalten');
            assert.equal(aktiv(g2.panel('d')), false);
            assert.equal(g2.panel('c').classList.contains('show'), true);
            assert.equal(aktiv(g2.knopf('c')), true);
            assert.equal(g2.knopf('c').getAttribute('aria-selected'), 'true');
        });

        // Randfall 3
        test(`Klick in Gruppe 2 schaltet nur Gruppe 2 (${name})`, () => {
            const g1 = standard('g1', ['a', 'b'], 'b');
            const g2 = standard('g2', ['c', 'd'], 'c');
            baue(doc, g1, g2);
            initTabs(doc);

            g2.knopf('d').click();

            assert.equal(aktiv(g2.panel('d')), true, 'Gegenprobe: Gruppe 2 hat umgeschaltet');
            assert.equal(aktiv(g2.panel('c')), false);
            assert.equal(aktiv(g1.panel('b')), true, 'Gruppe 1 darf nicht mitschalten');
            assert.equal(aktiv(g1.panel('a')), false);
            assert.equal(g1.knopf('b').getAttribute('aria-selected'), 'true');
        });

        // Randfall 3
        test(`gleiche Namen in beiden Gruppen schalten unabhängig (${name})`, () => {
            const g1 = standard('g1', ['a', 'b'], 'a');
            const g2 = standard('g2', ['a', 'b'], 'a');
            baue(doc, g1, g2);
            initTabs(doc);

            g1.knopf('b').click();
            assert.equal(aktiv(g1.panel('b')), true, 'Gegenprobe');
            g2.knopf('a').click();

            assert.equal(aktiv(g1.panel('b')), true);
            assert.equal(aktiv(g1.panel('a')), false);
            assert.equal(aktiv(g2.panel('a')), true);
            assert.equal(aktiv(g2.panel('b')), false);
        });
    }
});

// Randfall 3 (präzisiert nach Nachprüfung 2): Header und Body in verschiedenen Elternelementen.
const kopf = (header) => h('div', { class: 'card-main-header' }, header);
const rumpf = (body) => h('div', { class: 'card-body' }, body);

describe('Leere Gruppen-ID: Header und Body in verschiedenen Elternelementen', () => {
    const layouts = {
        'card-main-header und card-body, eine Karte je Gruppe': (doc, gs) => {
            for (const g of gs) doc.body.append(h('div', { class: 'card' }, kopf(g.header), rumpf(g.body)));
        },
        'alle Köpfe und Rümpfe flach hintereinander im selben Container': (doc, gs) => {
            doc.body.append(h('main', {}, ...gs.flatMap((g) => [kopf(g.header), rumpf(g.body)])));
        },
        'Header tief verschachtelt, Body in anderem Zweig': (doc, gs) => {
            for (const g of gs) {
                doc.body.append(h('section', {}, h('div', {}, h('div', { class: 'card-main-header' }, h('nav', {}, g.header))), h('article', {}, h('div', { class: 'card-body' }, g.body))));
            }
        },
    };

    for (const [name, baue] of Object.entries(layouts)) {
        // Randfall 3
        test(`eine Gruppe: Klick auf den Reiter schaltet den Inhalt (${name})`, () => {
            const g = standard('g1', ['a', 'b'], 'a');
            baue(doc, [g]);
            initTabs(doc);

            assert.equal(aktiv(g.panel('a')), true, 'Gegenprobe: a ist vor dem Klick sichtbar');
            g.knopf('b').click();

            assert.equal(aktiv(g.knopf('b')), true, 'Reiter schaltet');
            assert.equal(aktiv(g.panel('b')), true, 'Inhalt schaltet nicht mit');
            assert.equal(g.panel('b').classList.contains('show'), true);
            assert.equal(aktiv(g.panel('a')), false);
            assert.equal(g.panel('a').classList.contains('show'), false);
        });

        // Randfall 3
        test(`zwei Gruppen: Klick in Gruppe 1 schaltet nur deren Inhalt (${name})`, () => {
            const g1 = standard('g1', ['a', 'b'], 'a');
            const g2 = standard('g2', ['a', 'b'], 'a');
            baue(doc, [g1, g2]);
            initTabs(doc);

            g1.knopf('b').click();

            assert.equal(aktiv(g1.panel('b')), true, 'Gegenprobe: Gruppe 1 hat umgeschaltet');
            assert.equal(aktiv(g1.panel('a')), false);
            assert.equal(aktiv(g2.panel('a')), true, 'Gruppe 2 darf nicht mitschalten');
            assert.equal(aktiv(g2.panel('b')), false);
            assert.equal(g2.knopf('a').getAttribute('aria-selected'), 'true');
        });

        // Randfall 3
        test(`zwei Gruppen nacheinander: Gruppe 2 schaltet nur ihren eigenen Inhalt (${name})`, () => {
            const g1 = standard('g1', ['a', 'b'], 'a');
            const g2 = standard('g2', ['a', 'b'], 'a');
            baue(doc, [g1, g2]);
            initTabs(doc);

            g1.knopf('b').click();
            assert.equal(aktiv(g1.panel('b')), true, 'Gegenprobe: Gruppe 1 hat umgeschaltet');
            g2.knopf('b').click();

            assert.equal(aktiv(g2.panel('b')), true);
            assert.equal(aktiv(g2.panel('a')), false);
            assert.equal(aktiv(g1.panel('b')), true, 'Gruppe 1 bleibt, wie sie war');
            assert.equal(aktiv(g1.panel('a')), false);
        });
    }
});
