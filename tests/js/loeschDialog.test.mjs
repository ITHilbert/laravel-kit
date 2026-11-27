// Tests für den Blade-Lösch-Dialog (Spec loesch-dialog-blade, #2298: AK6–AK9).
//
// Vertrag (js/loesch-dialog.js): initLoeschDialog(root = document) bindet jedes [data-kit-delete-dialog] unter root
// einmal; offen/zu steht am Attribut `hidden` der Wurzel. Escape wird als keydown (key 'Escape') auf `document` gesendet.
import assert from 'node:assert/strict';
import { beforeEach, describe, test } from 'node:test';
import { FakeEvent, h, installiere } from './fake-dom.mjs';

let doc;

// Import je Test, damit ein fehlendes Modul den einzelnen Test rot macht (und nicht den ganzen Lauf).
const laden = () => import('../../js/loesch-dialog.js');

const knopf = (id) => h('button', { 'data-kit-delete': id });

function baue(route = '/kunden/0') {
    const gesendet = [];
    const formular = h('form', { id: 'formDelete', method: 'POST' });
    formular.submit = () => gesendet.push(formular.getAttribute('action'));
    const abbrechen = h('button', { type: 'button', 'data-variant': 'cancel', 'data-kit-delete-abbrechen': '' }, 'Abbrechen');
    const bestaetigen = h('button', { type: 'button', 'data-variant': 'delete', 'data-kit-delete-bestaetigen': '' }, 'Löschen');
    const overlay = h('div', { class: 'kit-dialog__overlay', 'data-kit-delete-abbrechen': '' });
    const wurzel = h(
        'div',
        { class: 'kit-dialog', 'data-kit-delete-dialog': '', 'data-route': route, hidden: '' },
        overlay,
        h('div', { class: 'kit-dialog__content', role: 'dialog' }, h('h2', {}, 'Titel'), abbrechen, bestaetigen),
    );
    doc.body.append(wurzel, formular);
    return { wurzel, formular, abbrechen, bestaetigen, overlay, gesendet };
}

const escape = () => doc.dispatchEvent(new FakeEvent('keydown', { key: 'Escape' }));

beforeEach(() => {
    doc = installiere();
});

describe('Blade-Lösch-Dialog', () => {
    // AK6
    test('Klick auf data-kit-delete öffnet den Dialog (hidden fällt weg)', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        const b = knopf('5');
        doc.body.append(b);
        initLoeschDialog(doc);

        assert.equal(d.wurzel.hidden, true, 'Gegenprobe: vor dem Klick zu');
        b.click();
        assert.equal(d.wurzel.hidden, false);
        assert.equal(d.wurzel.hasAttribute('hidden'), false);
    });

    // AK7
    test('Löschen setzt die action mit der ID, sendet einmal und schließt', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        const b = knopf('5');
        doc.body.append(b);
        initLoeschDialog(doc);

        b.click();
        assert.deepEqual(d.gesendet, [], 'Gegenprobe: vor „Löschen“ nichts gesendet');
        d.bestaetigen.click();

        assert.equal(d.formular.getAttribute('action'), '/kunden/5');
        assert.deepEqual(d.gesendet, ['/kunden/5']);
        assert.equal(d.wurzel.hidden, true);
    });

    // AK7 (Randfall: Query bleibt)
    test('die Query der Route bleibt erhalten', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue('/kunden/0?x=1');
        const b = knopf('5');
        doc.body.append(b);
        initLoeschDialog(doc);

        b.click();
        d.bestaetigen.click();
        assert.deepEqual(d.gesendet, ['/kunden/5?x=1']);
    });

    // AK8
    for (const weg of ['abbrechen', 'overlay', 'escape']) {
        test(`${weg} schließt ohne zu senden, danach gilt die neue ID`, async () => {
            const { initLoeschDialog } = await laden();
            const d = baue();
            const b5 = knopf('5');
            const b7 = knopf('7');
            doc.body.append(b5, b7);
            initLoeschDialog(doc);

            b5.click();
            assert.equal(d.wurzel.hidden, false, 'Gegenprobe: Dialog war offen');
            if (weg === 'abbrechen') d.abbrechen.click();
            else if (weg === 'overlay') d.overlay.click();
            else escape();

            assert.equal(d.wurzel.hidden, true);
            assert.deepEqual(d.gesendet, []);

            b7.click();
            assert.equal(d.wurzel.hidden, false);
            d.bestaetigen.click();
            assert.deepEqual(d.gesendet, ['/kunden/7']);
        });
    }

    // AK9
    test('zweimal initialisiert (gleiches root und Standard-root) sendet genau einmal', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        const b = knopf('5');
        doc.body.append(b);
        initLoeschDialog(doc);
        initLoeschDialog(doc);
        initLoeschDialog();

        b.click();
        d.bestaetigen.click();
        assert.deepEqual(d.gesendet, ['/kunden/5']);
    });

    // AK9
    test('ohne Dialog auf der Seite gibt es keinen Fehler', async () => {
        const { initLoeschDialog } = await laden();
        const b = knopf('5');
        doc.body.append(b);

        assert.doesNotThrow(() => initLoeschDialog(doc));
        assert.doesNotThrow(() => b.click());
        // Gegenprobe: kommt der Dialog später dazu, funktioniert die Init
        const d = baue();
        initLoeschDialog(doc);
        b.click();
        assert.equal(d.wurzel.hidden, false);
    });

    // AK7
    test('eine ID mit Sonderzeichen wird kodiert in die action übernommen', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue('/kunden/0?x=1');
        const normal = knopf('5');
        const b = knopf('a/b?c');
        doc.body.append(normal, b);
        initLoeschDialog(doc);

        normal.click();
        d.bestaetigen.click();
        assert.deepEqual(d.gesendet, ['/kunden/5?x=1'], 'Gegenprobe: Normalfall sendet');

        b.click();
        d.bestaetigen.click();
        assert.deepEqual(d.gesendet[1], '/kunden/a%2Fb%3Fc?x=1');
        assert.equal(d.gesendet.length, 2);
    });

    // AK8
    test('ohne #formDelete auf der Seite gibt „Löschen“ keinen Fehler und schließt', async () => {
        const { initLoeschDialog } = await laden();
        const d = baue();
        d.formular.remove();
        const b = knopf('5');
        doc.body.append(b);
        initLoeschDialog(doc);

        b.click();
        assert.equal(d.wurzel.hidden, false, 'Gegenprobe: Dialog war offen');
        assert.doesNotThrow(() => d.bestaetigen.click());
        assert.equal(d.wurzel.hidden, true);
        assert.deepEqual(d.gesendet, []);
        assert.equal(d.formular.getAttribute('action'), null);
    });
});
