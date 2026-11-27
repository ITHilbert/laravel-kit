// Fokusführung des Blade-Lösch-Dialogs (Spec loesch-dialog-nacharbeit, #2435: AK1–AK4).
// Das Fake-DOM führt document.activeElement: focus() wirkt nur im Dokument und ohne `hidden`-Vorfahr.
import assert from 'node:assert/strict';
import { beforeEach, describe, test } from 'node:test';
import { FakeEvent, h, installiere } from './fake-dom.mjs';

let doc;

const laden = () => import('../../js/loesch-dialog.js');

// Markup wie src/Views/kit/kern/dialog-delete.blade.php
function baue() {
    const formular = h('form', { id: 'formDelete', method: 'POST' });
    formular.submit = () => {};
    const abbrechen = h('button', { type: 'button', 'data-variant': 'cancel', 'data-kit-delete-abbrechen': '' }, 'Abbrechen');
    const bestaetigen = h('button', { type: 'button', 'data-variant': 'delete', 'data-kit-delete-bestaetigen': '' }, 'Löschen');
    const overlay = h('div', { class: 'kit-dialog__overlay', 'data-kit-delete-abbrechen': '' });
    const wurzel = h(
        'div',
        { class: 'kit-dialog', 'data-kit-delete-dialog': '', 'data-route': '/kunden/0', hidden: '' },
        overlay,
        h('div', { class: 'kit-dialog__content', role: 'dialog' }, h('h2', {}, 'Titel'), abbrechen, bestaetigen),
    );
    const opener = h('button', { 'data-kit-delete': '5' });
    const draussen = h('button', {}, 'Draußen');
    doc.body.append(opener, draussen, wurzel, formular);
    return { wurzel, abbrechen, bestaetigen, overlay, opener, draussen };
}

const taste = (ziel, init = {}) => {
    const ev = new FakeEvent('keydown', { key: 'Tab', ...init });
    ziel.dispatchEvent(ev);
    return ev;
};

beforeEach(() => {
    doc = installiere();
});

describe('Lösch-Dialog Fokus', () => {
    // AK1
    test('Öffnen setzt den Fokus auf „Abbrechen“', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        initLoeschDialog(doc);

        d.opener.focus();
        assert.equal(doc.activeElement, d.opener, 'Gegenprobe: das Fake-DOM führt den Fokus, Start auf dem Auslöser');
        d.opener.click();
        assert.equal(d.wurzel.hidden, false);
        assert.equal(doc.activeElement, d.abbrechen);
    });

    // AK2
    for (const weg of ['abbrechen', 'overlay', 'escape', 'loeschen']) {
        test(`${weg} gibt den Fokus an den Auslöser zurück`, async () => {
            const { initLoeschDialog } = await laden();
            const d = baue();
            initLoeschDialog(doc);

            d.opener.click();
            assert.equal(d.wurzel.hidden, false, 'Gegenprobe: Dialog war offen');
            assert.notEqual(doc.activeElement, d.opener, 'Gegenprobe: Fokus war nicht beim Auslöser');
            if (weg === 'abbrechen') d.abbrechen.click();
            else if (weg === 'overlay') d.overlay.click();
            else if (weg === 'loeschen') d.bestaetigen.click();
            else doc.dispatchEvent(new FakeEvent('keydown', { key: 'Escape' }));

            assert.equal(d.wurzel.hidden, true);
            assert.equal(doc.activeElement, d.opener);
        });
    }

    // AK2 (Randfall: Auslöser nicht mehr in der Seite)
    test('fehlt der Auslöser beim Schließen, gibt es keinen Fehler und er bekommt keinen Fokus', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        initLoeschDialog(doc);

        d.opener.click();
        assert.equal(d.wurzel.hidden, false, 'Gegenprobe: Dialog war offen');
        d.opener.remove();
        assert.doesNotThrow(() => d.abbrechen.click());
        assert.equal(d.wurzel.hidden, true);
        assert.notEqual(doc.activeElement, d.opener);
    });

    // AK3
    test('Tab auf „Löschen“ springt auf „Abbrechen“, Umschalt+Tab auf „Abbrechen“ auf „Löschen“', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        initLoeschDialog(doc);
        d.opener.click();

        d.bestaetigen.focus();
        const vor = taste(d.bestaetigen);
        assert.equal(vor.defaultPrevented, true);
        assert.equal(doc.activeElement, d.abbrechen);

        const zurueck = taste(d.abbrechen, { shiftKey: true });
        assert.equal(zurueck.defaultPrevented, true);
        assert.equal(doc.activeElement, d.bestaetigen);
        assert.equal(d.wurzel.contains(doc.activeElement), true);
    });

    // AK4
    test('bei geschlossenem Dialog greift die Falle nicht', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        initLoeschDialog(doc);

        d.draussen.focus();
        const zu = taste(d.draussen);
        assert.equal(zu.defaultPrevented, false);
        assert.equal(doc.activeElement, d.draussen);

        // Gegenprobe: offen greift sie, nach dem Schließen wieder nicht
        d.opener.click();
        d.bestaetigen.focus();
        assert.equal(taste(d.bestaetigen).defaultPrevented, true, 'Gegenprobe: offen greift die Falle');
        d.abbrechen.click();
        d.draussen.focus();
        const wieder = taste(d.draussen);
        assert.equal(wieder.defaultPrevented, false);
        assert.equal(doc.activeElement, d.draussen);
    });
});
