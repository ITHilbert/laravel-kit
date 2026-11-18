// Tests für js/tabs.js mit dem Markup des Blade-Themes `kern` (Spec blade-kern, AK10, Randfall 3).
// Bindung wie bei den alten Themes über data-kit-tabs / data-kit-tab / data-kit-tabs-body / data-kit-tab-body
// (die Namen prüft KernTabsComboboxTest am gerenderten Markup).
// Zustand im Kern: Reiter und Panel tragen `is-active`, ein inaktives Panel trägt `hidden`.
import assert from 'node:assert/strict';
import { beforeEach, describe, test } from 'node:test';
import { h, installiere } from './fake-dom.mjs';

const { initTabs } = await import('../../js/tabs.js');

let doc;
beforeEach(() => {
    doc = installiere();
});

function kern(id, namen, aktiv) {
    const header = h(
        'div',
        { class: 'kit-tabs__list', role: 'tablist', 'data-kit-tabs': id },
        ...namen.map((n) =>
            h('button', { type: 'button', role: 'tab', 'aria-controls': n, 'aria-selected': String(n === aktiv), 'data-kit-tab': n, class: n === aktiv ? 'kit-tabs__tab is-active' : 'kit-tabs__tab' }, n),
        ),
    );
    const body = h(
        'div',
        { class: 'kit-tabs__body', 'data-kit-tabs-body': id },
        ...namen.map((n) => {
            const p = h('div', { id: n, role: 'tabpanel', 'data-kit-tab-body': n, class: n === aktiv ? 'kit-tabs__panel is-active' : 'kit-tabs__panel' }, `inhalt-${n}`);
            if (n !== aktiv) p.setAttribute('hidden', '');
            return p;
        }),
    );
    return { header, body, knopf: (n) => header.querySelector(`[data-kit-tab="${n}"]`), panel: (n) => body.querySelector(`[data-kit-tab-body="${n}"]`) };
}

const aktiv = (el) => el.classList.contains('is-active');
const versteckt = (el) => el.hasAttribute('hidden');

describe('Tabs im Theme kern', () => {
    for (const id of ['g1', '']) {
        // AK10
        test(`Klick auf Reiter b schaltet Reiter und Panel um (Gruppe "${id}")`, () => {
            const g = kern(id, ['a', 'b'], 'a');
            doc.body.append(g.header, g.body);
            initTabs(doc);

            assert.equal(aktiv(g.panel('a')), true, 'Gegenprobe: a ist vor dem Klick aktiv');
            assert.equal(versteckt(g.panel('b')), true, 'Gegenprobe: b ist vor dem Klick versteckt');
            g.knopf('b').click();

            assert.equal(aktiv(g.knopf('b')), true);
            assert.equal(aktiv(g.panel('b')), true);
            assert.equal(versteckt(g.panel('b')), false);
            assert.equal(g.knopf('b').getAttribute('aria-selected'), 'true');
            assert.equal(aktiv(g.knopf('a')), false);
            assert.equal(aktiv(g.panel('a')), false);
            assert.equal(versteckt(g.panel('a')), true);
            assert.equal(g.knopf('a').getAttribute('aria-selected'), 'false');
        });
    }

    // AK10
    test('Zurückschalten zeigt a wieder und versteckt b', () => {
        const g = kern('g1', ['a', 'b'], 'a');
        doc.body.append(g.header, g.body);
        initTabs(doc);

        g.knopf('b').click();
        assert.equal(versteckt(g.panel('b')), false, 'Gegenprobe: erster Klick hat umgeschaltet');
        g.knopf('a').click();

        assert.equal(versteckt(g.panel('a')), false);
        assert.equal(versteckt(g.panel('b')), true);
        assert.equal(aktiv(g.knopf('a')), true);
    });

    // AK10
    test('die alten Themes bekommen weder is-active noch hidden', () => {
        const header = h('ul', { 'data-kit-tabs': 'x' }, h('li', {}, h('button', { 'data-kit-tab': 'a', class: 'tab-header active' })), h('li', {}, h('button', { 'data-kit-tab': 'b', class: 'tab-header' })));
        const body = h('div', { 'data-kit-tabs-body': 'x' }, h('div', { 'data-kit-tab-body': 'a', class: 'tab-body active show' }), h('div', { 'data-kit-tab-body': 'b', class: 'tab-body' }));
        doc.body.append(header, body);
        initTabs(doc);

        header.querySelector('[data-kit-tab="b"]').click();

        const b = body.querySelector('[data-kit-tab-body="b"]');
        assert.equal(b.classList.contains('active'), true, 'Gegenprobe: altes Verhalten greift');
        assert.equal(b.classList.contains('is-active'), false);
        assert.equal(body.querySelector('[data-kit-tab-body="a"]').hasAttribute('hidden'), false);
        assert.equal(header.querySelector('[data-kit-tab="b"]').classList.contains('is-active'), false);
    });
});

describe('Tabs im Theme kern ohne aktiven Reiter', () => {
    // Randfall 3
    test('alle Panels hidden, initTabs wirft nicht, Klick zeigt das gewählte Panel', () => {
        const g = kern('g1', ['a', 'b'], null);
        doc.body.append(g.header, g.body);

        assert.doesNotThrow(() => initTabs(doc));
        assert.equal(versteckt(g.panel('a')) && versteckt(g.panel('b')), true, 'Gegenprobe: Ausgangszustand alle hidden');
        g.knopf('a').click();

        assert.equal(versteckt(g.panel('a')), false);
        assert.equal(aktiv(g.panel('a')), true);
        assert.equal(versteckt(g.panel('b')), true);
    });
});
