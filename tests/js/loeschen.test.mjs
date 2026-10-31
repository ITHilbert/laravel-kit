// Tests für Lösch-Button und Dialog-Insel (T2: AK6, Randfall 4).
//
// Vertrag (js/loeschen.js):
//   button-delete: Hauptelement  data-kit-delete="<id des Datensatzes>"
//   initLoeschen(root = document): ein Klick auf (ein Kind von) [data-kit-delete] löst auf `document`
//     das CustomEvent LOESCHEN_EREIGNIS ('kit:delete') aus, detail = { id: '<id als String>' }.
//   onLoeschen(callback, ziel = document): ruft callback({ id }) bei jedem Ereignis; gibt eine
//     Funktion zum Abmelden zurück. Die Vue-Komponente dialog-delete nutzt das, um sich zu öffnen.
//   loeschRoute(route, id): ersetzt das letzte Pfadsegment der Dialog-Prop `route` durch die ID
//     (route endet auf einen Platzhalter oder auf '/'), ID wird URL-kodiert, Query/Fragment bleiben.
//   Es gibt keine globale Funktion OpenDeleteForm und keine globale Variable deleteID.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { beforeEach, describe, test } from 'node:test';
import { h, installiere } from './fake-dom.mjs';

const { initLoeschen, onLoeschen, loeschRoute, LOESCHEN_EREIGNIS } = await import('../../js/loeschen.js');

let doc;

const knopf = (id) => h('button', { 'data-kit-delete': id, class: 'btn btn-delete' }, h('svg', { class: 'symbol' }));

function mitDialog() {
    const geoeffnet = [];
    const ab = onLoeschen((e) => geoeffnet.push(e.id), doc);
    return { geoeffnet, ab };
}

beforeEach(() => {
    doc = installiere();
    delete globalThis.OpenDeleteForm;
    delete globalThis.deleteID;
});

describe('Lösch-Dialog als Insel', () => {
    // AK6
    test('das Ereignis heißt kit:delete', () => {
        assert.equal(LOESCHEN_EREIGNIS, 'kit:delete');
    });

    // AK6
    test('Klick auf button-delete öffnet den Dialog mit der ID des Datensatzes', () => {
        const b = knopf('42');
        doc.body.append(b);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();

        assert.deepEqual(geoeffnet, [], 'Gegenprobe: vor dem Klick ist nichts geöffnet');
        b.click();

        assert.deepEqual(geoeffnet, ['42']);
    });

    // AK6
    test('Klick auf ein Kind des Buttons (Symbol) öffnet den Dialog ebenfalls', () => {
        const b = knopf('5');
        doc.body.append(b);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();

        b.querySelector('.symbol').click();

        assert.deepEqual(geoeffnet, ['5']);
    });

    // AK6
    test('das Ereignis ist ein CustomEvent auf document mit detail.id', () => {
        const b = knopf('8');
        doc.body.append(b);
        initLoeschen(doc);
        let ev = null;
        doc.addEventListener(LOESCHEN_EREIGNIS, (e) => (ev = e));

        b.click();

        assert.ok(ev, 'Ereignis kam nicht an');
        assert.deepEqual(ev.detail, { id: '8' });
    });

    // AK6
    test('es gibt keine globale Funktion OpenDeleteForm und keine globale Variable deleteID', () => {
        const b = knopf('3');
        doc.body.append(b);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();
        b.click();

        assert.deepEqual(geoeffnet, ['3'], 'Gegenprobe: der Dialog ging auf');
        assert.equal(typeof globalThis.OpenDeleteForm, 'undefined');
        assert.equal(typeof globalThis.deleteID, 'undefined');
    });

    // AK6
    test('Klick auf ein anderes Element öffnet keinen Dialog', () => {
        const b = knopf('1');
        const anderes = h('button', { class: 'btn' });
        doc.body.append(b, anderes);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();

        b.click();
        assert.deepEqual(geoeffnet, ['1'], 'Gegenprobe: der Lösch-Button öffnet');
        anderes.click();

        assert.deepEqual(geoeffnet, ['1']);
    });

    // AK6
    test('nach dem Abmelden öffnet der Klick den Dialog nicht mehr', () => {
        const b = knopf('1');
        doc.body.append(b);
        initLoeschen(doc);
        const { geoeffnet, ab } = mitDialog();
        b.click();
        assert.deepEqual(geoeffnet, ['1'], 'Gegenprobe');

        ab();
        b.click();

        assert.deepEqual(geoeffnet, ['1']);
    });

    // AK6
    test('die Lösch-Route des Dialogs gehört zur ID des Datensatzes', () => {
        assert.equal(loeschRoute('/admin/users/0', '42'), '/admin/users/42');
        assert.equal(loeschRoute('/admin/users/', '42'), '/admin/users/42');
        assert.equal(loeschRoute('https://example.test/admin/users/0', '7'), 'https://example.test/admin/users/7');
    });

    // AK6
    test('loeschRoute kodiert die ID und lässt Query und Fragment stehen', () => {
        assert.equal(loeschRoute('/a/b/0', 'x/y'), '/a/b/x%2Fy');
        assert.equal(loeschRoute('/a/b/0?seite=2', '9'), '/a/b/9?seite=2');
    });

    // AK6
    test('Klick und Route zusammen: Dialog zeigt die Lösch-Route genau dieser ID', () => {
        const b = knopf('42');
        doc.body.append(b);
        initLoeschen(doc);
        const routen = [];
        onLoeschen((e) => routen.push(loeschRoute('/admin/users/0', e.id)), doc);

        b.click();

        assert.deepEqual(routen, ['/admin/users/42']);
    });

    // AK6
    test('dialog-delete nutzt weder OpenDeleteForm noch ein festes Element-ID-Schema je Datensatz', () => {
        const quelle = readFileSync(new URL('../../vue/tailwind/dialog/delete.vue', import.meta.url), 'utf8');

        assert.match(quelle, /kit:delete|loeschen/, 'Gegenprobe: die Komponente hängt am Kit-Ereignis');
        assert.doesNotMatch(quelle, /OpenDeleteForm/);
    });
});

// Randfall 9 — nur Quelltext: die SFC lässt sich unter Node nicht ausführen.
describe('dialog-delete: bisheriger Öffnungsweg (Randfall 9, nur Quelltext)', () => {
    const quelle = readFileSync(new URL('../../vue/tailwind/dialog/delete.vue', import.meta.url), 'utf8');
    const template = quelle.slice(quelle.indexOf('<template>'), quelle.indexOf('</template>\n\n<script') + 11);
    const skript = quelle.slice(quelle.indexOf('<script'));

    // Randfall 9
    test('das Overlay behält id="myModalDelete"', () => {
        assert.match(template, /<div[^>]*\sid="myModalDelete"/, 'das Overlay-Element trägt id="myModalDelete"');
        assert.match(template, /class="dialog-overlay"/, 'id gehört zum Overlay');
        assert.match(template.slice(0, template.indexOf('class="dialog-overlay"') + 30), /id="myModalDelete"/);
    });

    // Randfall 9
    test('das Attribut route bleibt Prop des Dialogs', () => {
        assert.match(skript, /route\??:\s*string/);
        assert.match(skript, /route:\s*''/);
    });

    // Randfall 9
    test('Bestätigen nutzt die globale deleteID, wenn der Dialog nicht über das Kit-Ereignis geöffnet wurde', () => {
        const confirmFn = skript.slice(skript.indexOf('function confirm'), skript.indexOf('function handleKeydown'));
        assert.match(confirmFn, /deleteID/, 'confirm liest die globale deleteID');
        assert.match(confirmFn, /loeschRoute\(props\.route,/, 'Route aus props.route und deleteID');
        assert.match(confirmFn, /aktuelleRoute\.value/, 'Gegenprobe: der Ereignisweg bleibt');
    });

    // Randfall 9
    test('öffnet das Kit-Ereignis, gilt dessen ID (nicht die globale deleteID)', () => {
        const ereignis = skript.slice(skript.indexOf('onLoeschen('));
        assert.match(ereignis, /aktuelleRoute\.value\s*=\s*loeschRoute\(props\.route,\s*id\)/);
        const confirmFn = skript.slice(skript.indexOf('function confirm'), skript.indexOf('function handleKeydown'));
        // der Rückfall steht erst nach dem Ereignisweg: aktuelleRoute hat Vorrang
        assert.ok(confirmFn.indexOf('aktuelleRoute') < confirmFn.indexOf('deleteID'), 'aktuelleRoute hat Vorrang vor deleteID');
    });

    // Randfall 9
    test('die Komponente setzt deleteID nicht selbst und definiert kein OpenDeleteForm', () => {
        assert.doesNotMatch(skript, /deleteID\s*=[^=]/);
        assert.doesNotMatch(quelle, /OpenDeleteForm/);
    });

    // Randfall 9
    test('der alte Öffnungsweg (classList.remove("hidden") am Overlay) kann den Dialog öffnen', () => {
        // hidden darf nicht allein vom Vue-Zustand abhängen: das Overlay braucht ein Element mit id,
        // an dem classList.remove('hidden') wirkt; Rückfall: offen wird aus der Klasse gelesen oder die Klasse ist statisch.
        assert.match(template, /id="myModalDelete"/);
        assert.match(template, /hidden/, 'Gegenprobe: die Klasse hidden wird vom Template gesetzt');
    });
});

describe('Mehrere Lösch-Buttons', () => {
    // Randfall 4
    test('jeder button-delete öffnet den Dialog mit seiner eigenen ID', () => {
        const knoepfe = ['11', '12', '13'].map(knopf);
        doc.body.append(...knoepfe);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();

        knoepfe[1].click();
        knoepfe[2].click();
        knoepfe[0].click();

        assert.deepEqual(geoeffnet, ['12', '13', '11']);
    });

    // Randfall 4
    test('ein Klick öffnet den Dialog genau einmal, nicht für jeden Button', () => {
        const knoepfe = ['1', '2', '3'].map(knopf);
        doc.body.append(...knoepfe);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();

        knoepfe[0].click();
        assert.equal(geoeffnet.length, 1);
        knoepfe[2].click();

        assert.deepEqual(geoeffnet, ['1', '3']);
    });

    // Randfall 4
    test('die Routen zweier Buttons unterscheiden sich nach ihrer ID', () => {
        const knoepfe = ['21', '22'].map(knopf);
        doc.body.append(...knoepfe);
        initLoeschen(doc);
        const routen = [];
        onLoeschen((e) => routen.push(loeschRoute('/x/0', e.id)), doc);

        knoepfe[0].click();
        knoepfe[1].click();

        assert.deepEqual(routen, ['/x/21', '/x/22']);
    });

    // Randfall 4
    test('zweimaliges initLoeschen verdoppelt die Öffnungen nicht', () => {
        const b = knopf('4');
        doc.body.append(b);
        initLoeschen(doc);
        initLoeschen(doc);
        const { geoeffnet } = mitDialog();

        b.click();

        assert.deepEqual(geoeffnet, ['4']);
    });
});
