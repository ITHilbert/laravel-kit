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

// Randfall 9 — Ablauf der Dialog-Zustandslogik.
// Vertrag (js/loeschen.js, zusätzlich zu den obigen Exporten):
//   erzeugeLoeschDialog({ route, overlay, formular, globalThis }) -> { oeffnenPerEreignis(id), bestaetigen(), schliessen() }
//     route:      Lösch-Route des Dialogs (Prop `route`), String oder Funktion, die den String liefert
//     overlay:    das Overlay-Element (id="myModalDelete"); versteckt = Klasse `hidden`
//     formular:   Funktion, die beim Bestätigen das Formular #formDelete liefert (Objekt mit setAttribute und submit) oder null
//     globalThis: Objekt, das die globale `deleteID` des alten Öffnungswegs trägt (im Browser `globalThis`)
//   oeffnenPerEreignis(id): merkt die Route loeschRoute(route, id) für genau diesen Öffnungsvorgang, entfernt `hidden`.
//   schliessen(): setzt `hidden` und vergisst die Ereignis-ID.
//   bestaetigen(): schließt (setzt `hidden`); Route = die gemerkte Ereignis-Route, sonst loeschRoute(route, deleteID)
//     (deleteID undefined, null oder '' zählt als nicht gesetzt); die Ereignis-ID ist danach vergessen.
//     Mit Route: formular().setAttribute('action', route), dann formular().submit(); ohne Route oder Formular wird nicht gesendet.
//   Der alte Öffnungsweg öffnet das Overlay von außen mit overlay.classList.remove('hidden'); die Logik darf sich
//   nicht auf einen eigenen "offen"-Zustand stützen. Die Logik setzt deleteID nie selbst.
const { erzeugeLoeschDialog } = await import('../../js/loeschen.js');

function formularAttrappe() {
    const f = { aktion: null, gesendet: [], setAttribute: (k, v) => k === 'action' && (f.aktion = v), submit: () => f.gesendet.push(f.aktion) };
    return f;
}

function dialog(extra = {}) {
    const overlay = h('div', { id: 'myModalDelete', class: 'dialog-overlay hidden', route: '/admin/users/0' });
    const form = formularAttrappe();
    const host = {};
    const d = erzeugeLoeschDialog({ route: '/admin/users/0', overlay, formular: () => form, globalThis: host, ...extra });
    return { d, overlay, form, host };
}

const versteckt = (overlay) => overlay.classList.contains('hidden');

describe('dialog-delete: Ablauf der Öffnungswege (Randfall 9)', () => {
    // Randfall 9
    test('Ereignis 3, bestätigen: die Route der Ereignis-ID wird gesendet', () => {
        const { d, overlay, form } = dialog();

        d.oeffnenPerEreignis('3');
        assert.equal(versteckt(overlay), false, 'Gegenprobe: der Dialog ist offen');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/3']);
        assert.equal(versteckt(overlay), true);
    });

    // Randfall 9
    test('nach Ereignis 3 und Absenden öffnet der alte Weg mit deleteID = 7 und sendet /…/7, nicht /…/3', () => {
        const { d, overlay, form, host } = dialog();
        d.oeffnenPerEreignis('3');
        d.bestaetigen();
        assert.deepEqual(form.gesendet, ['/admin/users/3'], 'Gegenprobe: erster Vorgang sendete 3');

        host.deleteID = 7;
        overlay.classList.remove('hidden');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/3', '/admin/users/7']);
        assert.equal(form.aktion, '/admin/users/7');
    });

    // Randfall 9
    test('nach Ereignis 3 und Schließen (ohne Absenden) nutzt der alte Weg wieder deleteID', () => {
        const { d, overlay, form, host } = dialog();
        d.oeffnenPerEreignis('3');
        d.schliessen();
        assert.equal(versteckt(overlay), true, 'Gegenprobe: Schließen versteckt das Overlay');

        host.deleteID = 7;
        overlay.classList.remove('hidden');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/7']);
    });

    // Randfall 9
    test('alter Weg mit deleteID = 7 allein sendet /…/7 (Route aus dem Prop)', () => {
        const { d, overlay, form, host } = dialog();
        host.deleteID = 7;
        overlay.classList.remove('hidden');

        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/7']);
        assert.equal(versteckt(overlay), true);
    });

    // Randfall 9
    test('öffnet das Ereignis, gilt dessen ID auch bei gesetzter deleteID', () => {
        const { d, form, host } = dialog();
        host.deleteID = 7;

        d.oeffnenPerEreignis('3');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/3']);
    });

    // Randfall 9
    test('zweites Ereignis ersetzt das erste: zuletzt geöffnete ID gilt', () => {
        const { d, form } = dialog();

        d.oeffnenPerEreignis('3');
        d.schliessen();
        d.oeffnenPerEreignis('5');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/5']);
    });

    // Randfall 9
    test('zwei Ereignis-Vorgänge hintereinander senden je die eigene ID', () => {
        const { d, form } = dialog();

        d.oeffnenPerEreignis('3');
        d.bestaetigen();
        d.oeffnenPerEreignis('5');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/3', '/admin/users/5']);
    });

    // Randfall 9
    test('nach Absenden ist die Ereignis-ID vergessen: alter Weg ohne deleteID sendet nichts', () => {
        const { d, overlay, form, host } = dialog();
        d.oeffnenPerEreignis('3');
        d.bestaetigen();
        assert.deepEqual(form.gesendet, ['/admin/users/3'], 'Gegenprobe: der erste Vorgang sendete');

        overlay.classList.remove('hidden');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/3']);
        assert.equal(host.deleteID, undefined);
    });

    // Randfall 9
    test('deleteID nicht gesetzt oder leer: Bestätigen sendet nichts, schließt aber', () => {
        const { d, overlay, form, host } = dialog();
        host.deleteID = 7;
        overlay.classList.remove('hidden');
        d.bestaetigen();
        assert.deepEqual(form.gesendet, ['/admin/users/7'], 'Gegenprobe: mit deleteID wird gesendet');

        delete host.deleteID;
        overlay.classList.remove('hidden');
        d.bestaetigen();
        assert.equal(versteckt(overlay), true, 'nicht gesetzt: geschlossen');

        host.deleteID = '';
        overlay.classList.remove('hidden');
        d.bestaetigen();
        assert.equal(versteckt(overlay), true, 'leer gesetzt: geschlossen');

        host.deleteID = null;
        overlay.classList.remove('hidden');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/7']);
    });

    // Randfall 9
    test('fehlt das Formular #formDelete, wird nichts gesendet und nichts geworfen', () => {
        const { d, overlay, form } = dialog({ formular: () => null });
        d.oeffnenPerEreignis('3');

        assert.doesNotThrow(() => d.bestaetigen());

        assert.equal(versteckt(overlay), true);
        assert.deepEqual(form.gesendet, []);
    });

    // Randfall 9
    test('schließen versteckt das Overlay, Ereignis öffnet es wieder', () => {
        const { d, overlay } = dialog();
        assert.equal(versteckt(overlay), true, 'Gegenprobe: Ausgang versteckt');

        d.oeffnenPerEreignis('3');
        assert.equal(versteckt(overlay), false);
        d.schliessen();

        assert.equal(versteckt(overlay), true);
    });

    // Randfall 9
    test('die Ereignis-ID wird URL-kodiert und Query der Route bleibt', () => {
        const { d, form } = dialog({ route: '/a/b/0?seite=2' });

        d.oeffnenPerEreignis('x/y');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/a/b/x%2Fy?seite=2']);
    });

    // Randfall 9
    test('route darf eine Funktion sein (Prop ändert sich nach dem Erzeugen)', () => {
        let route = '/alt/0';
        const { d, form } = dialog({ route: () => route });
        route = '/neu/0';

        d.oeffnenPerEreignis('3');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/neu/3']);
    });

    // Randfall 9
    test('die Logik setzt deleteID nicht selbst und kennt kein OpenDeleteForm', () => {
        const { d, form, host } = dialog();

        d.oeffnenPerEreignis('3');
        d.bestaetigen();

        assert.deepEqual(form.gesendet, ['/admin/users/3'], 'Gegenprobe: der Ablauf lief');
        assert.equal('deleteID' in host, false);
        assert.equal(typeof globalThis.OpenDeleteForm, 'undefined');
    });
});

// Randfall 9 — Quelltext der SFC: die Komponente verdrahtet nur noch; die Logik steht in js/loeschen.js.
describe('dialog-delete: Verdrahtung der Komponente (Randfall 9, Quelltext)', () => {
    const quelle = readFileSync(new URL('../../vue/tailwind/dialog/delete.vue', import.meta.url), 'utf8');
    const template = quelle.slice(quelle.indexOf('<template>'), quelle.indexOf('</template>\n\n<script'));
    const skript = quelle.slice(quelle.indexOf('<script'));
    const overlayTag = template.match(/<div\b[^>]*\sid="myModalDelete"[^>]*>/s)?.[0] ?? '';

    // Randfall 9
    test('das Overlay behält id="myModalDelete" und ist das äußerste Element', () => {
        assert.ok(overlayTag, 'Overlay-Element mit id="myModalDelete" fehlt');
        assert.match(overlayTag, /class="dialog-overlay"/);
        assert.equal(template.indexOf(overlayTag), template.indexOf('<div'), 'das Overlay ist das erste Element');
    });

    // Randfall 9
    test('das Overlay trägt das Attribut route mit dem Wert des Props', () => {
        assert.match(overlayTag, /:route="route"/);
    });

    // Randfall 9
    test('das Attribut route bleibt Prop des Dialogs', () => {
        assert.match(skript, /route\??:\s*string/);
        assert.match(skript, /route:\s*''/);
    });

    // Randfall 9
    test('die Komponente nutzt die Zustandslogik aus js/loeschen.js', () => {
        assert.match(skript, /import\s*\{[^}]*\berzeugeLoeschDialog\b[^}]*\}\s*from\s*'\.\.\/\.\.\/\.\.\/js\/loeschen\.js'/);
        assert.match(skript, /erzeugeLoeschDialog\(/);
        assert.match(skript, /oeffnenPerEreignis/);
        assert.match(skript, /bestaetigen/);
        assert.match(skript, /schliessen/);
    });

    // Randfall 9
    test('die Komponente hält keinen eigenen Routen-Zustand und liest deleteID nicht selbst', () => {
        assert.doesNotMatch(skript, /aktuelleRoute/);
        assert.doesNotMatch(skript, /deleteID/);
        assert.doesNotMatch(quelle, /OpenDeleteForm/);
    });

    // Randfall 9
    test('das Overlay verliert hidden nicht durch Vue-Neuzeichnen (Klasse konstant)', () => {
        assert.match(template, /hidden/, 'Gegenprobe: die Klasse hidden wird vom Template gesetzt');
        assert.match(skript, /const anfangsVersteckt = true/);
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
