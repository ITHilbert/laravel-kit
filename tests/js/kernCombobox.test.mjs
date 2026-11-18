// Tests für js/combobox.js mit dem Markup des Blade-Themes `kern` (Spec blade-kern, AK12).
// Markup wie `<x-kit::combobox>` im Theme kern: div.kit-combobox > label + select.kit-combobox__select[data-kit-combobox].
// Das Skript ergänzt im div: input.kit-combobox__input, button.kit-combobox__trigger, ul.kit-combobox__list > li.kit-combobox__item.
import assert from 'node:assert/strict';
import { beforeEach, describe, test } from 'node:test';
import { FakeEvent, h, installiere } from './fake-dom.mjs';

const { initCombobox } = await import('../../js/combobox.js');

let doc;
beforeEach(() => {
    doc = installiere();
});

function kernCombobox() {
    const select = h(
        'select',
        { class: 'kit-combobox__select', name: 'land', id: 'land', 'data-kit-combobox': '' },
        h('option', { value: 'de' }, 'Deutschland'),
        h('option', { value: 'at', selected: 'selected' }, 'Österreich'),
        h('option', { value: 'ch' }, 'Schweiz'),
    );
    const wurzel = h('div', { class: 'kit-combobox' }, h('label', { class: 'kit-combobox__label', for: 'land' }, 'Land'), select);
    const form = h('form', {}, wurzel);
    doc.body.appendChild(form);
    const aenderungen = [];
    form.addEventListener('change', () => aenderungen.push(select.value));
    initCombobox(doc);
    return {
        select,
        wurzel,
        aenderungen,
        eingabe: wurzel.querySelector('.kit-combobox__input'),
        knopf: wurzel.querySelector('.kit-combobox__trigger'),
        liste: wurzel.querySelector('.kit-combobox__list'),
        eintraege: () => wurzel.querySelectorAll('.kit-combobox__item'),
    };
}

describe('Combobox im Theme kern', () => {
    // AK12
    test('das Skript ergänzt Eingabefeld, Knopf und versteckte Liste im div.kit-combobox', () => {
        const t = kernCombobox();

        assert.ok(t.eingabe, 'kit-combobox__input fehlt');
        assert.equal(t.eingabe.getAttribute('role'), 'combobox');
        assert.equal(t.eingabe.value, 'Österreich');
        assert.ok(t.knopf, 'kit-combobox__trigger fehlt');
        assert.ok(t.liste, 'kit-combobox__list fehlt');
        assert.equal(t.liste.getAttribute('role'), 'listbox');
        assert.equal(t.liste.hasAttribute('hidden'), true);
        assert.equal(t.wurzel.classList.contains('is-open'), false);
        assert.equal(t.wurzel.querySelectorAll('.kit-combobox__input').length, 1);
    });

    // AK12
    test('geöffnet: is-open an der Wurzel, Einträge als kit-combobox__item, Markierung is-active', () => {
        const t = kernCombobox();

        t.knopf.click();
        assert.equal(t.wurzel.classList.contains('is-open'), true);
        assert.equal(t.liste.hasAttribute('hidden'), false);
        assert.deepEqual(t.eintraege().map((li) => li.textContent), ['Deutschland', 'Österreich', 'Schweiz']);

        t.eingabe.dispatchEvent(new FakeEvent('keydown', { key: 'ArrowDown' }));
        assert.deepEqual(t.eintraege().filter((li) => li.classList.contains('is-active')).map((li) => li.textContent), ['Deutschland']);
        assert.equal(t.eintraege().some((li) => li.classList.contains('active')), false, 'altes Klassenpaar nicht im Kern');

        t.knopf.click();
        assert.equal(t.wurzel.classList.contains('is-open'), false);
        assert.equal(t.liste.hasAttribute('hidden'), true);
    });

    // AK12
    test('Auswahl setzt den Wert des select, schließt die Liste und löst change aus', () => {
        const t = kernCombobox();
        t.knopf.click();
        const schweiz = t.eintraege().find((li) => li.textContent === 'Schweiz');
        assert.ok(schweiz, 'Aufbau: Eintrag Schweiz da');

        schweiz.dispatchEvent(new FakeEvent('mousedown'));

        assert.equal(t.select.value, 'ch');
        assert.equal(t.eingabe.value, 'Schweiz');
        assert.equal(t.wurzel.classList.contains('is-open'), false);
        assert.deepEqual(t.aenderungen, ['ch']);
    });

    // AK12
    test('für select.selectcb bleibt der alte Aufbau (span.kit-combobox hinter dem Select)', () => {
        const sel = h('select', { class: 'selectcb' }, h('option', { value: '1' }, 'A'));
        doc.body.appendChild(h('form', {}, sel));

        initCombobox(doc);

        assert.ok(sel.nextElementSibling?.matches('span.kit-combobox'));
        assert.ok(sel.nextElementSibling.querySelector('.kit-combobox-input'));
        assert.equal(sel.nextElementSibling.querySelector('.kit-combobox__input'), null);
    });
});
