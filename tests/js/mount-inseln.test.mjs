// Tests für den Mount-Helfer (T2: AK7, AK8, Randfall 5).
//
// Vertrag (js/mount-inseln.js):
//   async mountInseln(verzeichnis, root = document, optionen = {})
//   - verzeichnis: { name: () => import('…') }, der Loader liefert ein Modul mit `default` = Komponente.
//   - optionen.createApp: ersetzt Vues createApp (nur für Tests; sonst wird createApp aus 'vue' geladen).
//     createApp(komponente, props) liefert ein Objekt mit mount(element).
//   - Das Promise wird fertig, wenn alle Inseln gemountet bzw. übersprungen sind.
//   - Warnungen: console.warn(text), text enthält den Namen und den Grund (bei Ladefehler die Fehlermeldung).
import assert from 'node:assert/strict';
import { afterEach, beforeEach, describe, mock, test } from 'node:test';
import { h, installiere } from './fake-dom.mjs';

const { mountInseln } = await import('../../js/mount-inseln.js');

let doc;
let warnMock;
const warnungen = {
    get length() {
        return warnMock.mock.calls.length;
    },
    map: (f) => warnMock.mock.calls.map(f),
};
let apps;
let gemountet;

function createApp(komponente, props) {
    const app = { komponente, props };
    apps.push(app);
    return {
        mount(el) {
            gemountet.push({ el, komponente, props });
        },
    };
}

const komp = (name) => ({ name });
const lader = (k) => () => Promise.resolve({ default: k });
const insel = (name, props) => h('div', props === undefined ? { 'data-vue': name } : { 'data-vue': name, 'data-props': props });
const warntext = () => warnungen.map((a) => a.arguments.map(String).join(' '));

beforeEach(() => {
    doc = installiere();
    apps = [];
    gemountet = [];
    warnMock = mock.method(console, 'warn', () => {});
});

afterEach(() => {
    mock.restoreAll();
});

describe('mountInseln', () => {
    // AK7
    test('jedes Element mit data-vue wird mit seiner Komponente aus dem Verzeichnis gemountet', async () => {
        const A = komp('A');
        const B = komp('B');
        const a = insel('card-a', '{}');
        const b = insel('card-b', '{}');
        const ohne = h('div', { id: 'ohne' });
        doc.body.append(a, ohne, b);

        await mountInseln({ 'card-a': lader(A), 'card-b': lader(B) }, doc, { createApp });

        assert.equal(gemountet.length, 2);
        assert.equal(gemountet.find((m) => m.el === a).komponente, A);
        assert.equal(gemountet.find((m) => m.el === b).komponente, B);
        assert.equal(gemountet.some((m) => m.el === ohne), false);
    });

    // AK7
    test('jede Insel ist eine eigene App, auch bei gleichem Namen', async () => {
        const A = komp('A');
        const e1 = insel('x', '{"n":1}');
        const e2 = insel('x', '{"n":2}');
        doc.body.append(e1, e2);

        await mountInseln({ x: lader(A) }, doc, { createApp });

        assert.equal(apps.length, 2);
        assert.notEqual(apps[0], apps[1]);
        assert.deepEqual(gemountet.find((m) => m.el === e1).props, { n: 1 });
        assert.deepEqual(gemountet.find((m) => m.el === e2).props, { n: 2 });
    });

    // AK7
    test('die Props kommen aus data-props', async () => {
        const e = insel('x', '{"titel":"Löschen","anzahl":3,"liste":[1,2],"tief":{"a":true}}');
        doc.body.append(e);

        await mountInseln({ x: lader(komp('X')) }, doc, { createApp });

        assert.deepEqual(gemountet[0].props, { titel: 'Löschen', anzahl: 3, liste: [1, 2], tief: { a: true } });
    });

    // AK7
    test('fehlt data-props, gibt es keine Props und keine Warnung', async () => {
        const e = insel('x');
        doc.body.append(e);

        await mountInseln({ x: lader(komp('X')) }, doc, { createApp });

        assert.equal(gemountet.length, 1, 'Gegenprobe: die Insel wurde gemountet');
        assert.deepEqual(Object.keys(gemountet[0].props ?? {}), []);
        assert.equal(warnungen.length, 0);
    });

    // AK7
    test('gemountet wird nur unter root', async () => {
        const innen = h('section');
        const drin = insel('x', '{}');
        innen.append(drin);
        const draussen = insel('x', '{}');
        doc.body.append(innen, draussen);

        await mountInseln({ x: lader(komp('X')) }, innen, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [drin],
        );
    });

    // AK7
    test('ohne root-Angabe gilt document', async () => {
        const e = insel('x', '{}');
        doc.body.append(e);

        await mountInseln({ x: lader(komp('X')) }, undefined, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [e],
        );
    });

    // AK7
    test('die Komponente wird erst beim Mounten geladen (Lazy-Import)', async () => {
        let geladen = 0;
        const verzeichnis = {
            benutzt: () => (geladen++, Promise.resolve({ default: komp('B') })),
            unbenutzt: () => (geladen += 100, Promise.resolve({ default: komp('U') })),
        };
        doc.body.append(insel('benutzt', '{}'));

        await mountInseln(verzeichnis, doc, { createApp });

        assert.equal(geladen, 1);
        assert.equal(gemountet.length, 1);
    });
});

describe('Fehler isoliert', () => {
    // AK8
    test('unbekannter Name: nur diese Insel fehlt, Warnung nennt den Namen, die übrigen werden gemountet', async () => {
        const vor = insel('gut', '{}');
        const kaputt = insel('gibt-es-nicht', '{}');
        const nach = insel('gut', '{}');
        doc.body.append(vor, kaputt, nach);

        await mountInseln({ gut: lader(komp('G')) }, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [vor, nach],
        );
        assert.equal(warnungen.length, 1);
        assert.match(warntext()[0], /gibt-es-nicht/);
        assert.ok(warntext()[0].length > 'gibt-es-nicht'.length + 5, 'Warnung nennt keinen Grund');
    });

    // AK8
    test('data-props ist kein JSON: Insel übersprungen, Warnung nennt Namen und Grund, übrige gemountet', async () => {
        const kaputt = insel('x', '{nicht json');
        const gut = insel('y', '{}');
        doc.body.append(kaputt, gut);

        await mountInseln({ x: lader(komp('X')), y: lader(komp('Y')) }, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [gut],
        );
        assert.equal(warnungen.length, 1);
        assert.match(warntext()[0], /\bx\b/);
        assert.match(warntext()[0], /props/i);
    });

    // AK8
    for (const [bezeichnung, wert] of [
        ['Array', '[1,2]'],
        ['String', '"text"'],
        ['Zahl', '5'],
        ['null', 'null'],
        ['true', 'true'],
        ['leerer Wert', ''],
    ]) {
        test(`data-props ist kein JSON-Objekt (${bezeichnung}): Insel übersprungen, übrige gemountet`, async () => {
            const kaputt = insel('x', wert);
            const gut = insel('y', '{}');
            doc.body.append(kaputt, gut);

            await mountInseln({ x: lader(komp('X')), y: lader(komp('Y')) }, doc, { createApp });

            assert.deepEqual(
                gemountet.map((m) => m.el),
                [gut],
            );
            assert.equal(warnungen.length, 1);
            assert.match(warntext()[0], /\bx\b/);
        });
    }

    // AK8
    test('Ladefehler (Promise abgelehnt): Insel übersprungen, Warnung nennt Namen und Fehlermeldung, übrige gemountet', async () => {
        const kaputt = insel('lahm', '{}');
        const gut = insel('gut', '{}');
        doc.body.append(kaputt, gut);
        const verzeichnis = {
            lahm: () => Promise.reject(new Error('Netz weg')),
            gut: lader(komp('G')),
        };

        await mountInseln(verzeichnis, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [gut],
        );
        assert.equal(warnungen.length, 1);
        assert.match(warntext()[0], /lahm/);
        assert.match(warntext()[0], /Netz weg/);
    });

    // AK8
    test('Ladefehler (Loader wirft sofort): Insel übersprungen, Warnung nennt Namen und Grund', async () => {
        const kaputt = insel('wirft', '{}');
        const gut = insel('gut', '{}');
        doc.body.append(kaputt, gut);
        const verzeichnis = {
            wirft: () => {
                throw new Error('kaputter Import');
            },
            gut: lader(komp('G')),
        };

        await mountInseln(verzeichnis, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [gut],
        );
        assert.match(warntext()[0], /wirft/);
        assert.match(warntext()[0], /kaputter Import/);
    });

    // AK8
    test('Modul ohne default-Export gilt als Ladefehler dieser Insel', async () => {
        const kaputt = insel('leer', '{}');
        const gut = insel('gut', '{}');
        doc.body.append(kaputt, gut);

        await mountInseln({ leer: () => Promise.resolve({}), gut: lader(komp('G')) }, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [gut],
        );
        assert.equal(warnungen.length, 1);
        assert.match(warntext()[0], /leer/);
    });

    // AK8
    test('mehrere kaputte Inseln: je eine Warnung, alle gesunden gemountet', async () => {
        const e = [insel('a', '{x'), insel('b', '{}'), insel('unbekannt', '{}'), insel('b', '{}')];
        doc.body.append(...e);

        await mountInseln({ a: lader(komp('A')), b: lader(komp('B')) }, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [e[1], e[3]],
        );
        assert.equal(warnungen.length, 2);
    });

    // AK8
    test('mountInseln wirft bei einer kaputten Insel nicht (Promise wird erfüllt)', async () => {
        doc.body.append(insel('nix', '{}'));

        await assert.doesNotReject(mountInseln({}, doc, { createApp }));
        assert.equal(warnungen.length, 1, 'Gegenprobe: die Warnung kam');
    });
});

describe('Doppelter Aufruf', () => {
    // Randfall 5
    test('zweimal auf denselben root: bereits gemountete Inseln werden nicht doppelt gemountet', async () => {
        const e = insel('x', '{}');
        doc.body.append(e);
        const verzeichnis = { x: lader(komp('X')) };

        await mountInseln(verzeichnis, doc, { createApp });
        assert.equal(gemountet.length, 1, 'Gegenprobe: erster Aufruf mountet');
        await mountInseln(verzeichnis, doc, { createApp });

        assert.equal(gemountet.length, 1);
        assert.equal(apps.length, 1);
    });

    // Randfall 5
    test('zweiter Aufruf mountet nur neu hinzugekommene Inseln', async () => {
        const alt = insel('x', '{}');
        doc.body.append(alt);
        const verzeichnis = { x: lader(komp('X')) };
        await mountInseln(verzeichnis, doc, { createApp });

        const neu = insel('x', '{}');
        doc.body.append(neu);
        await mountInseln(verzeichnis, doc, { createApp });

        assert.deepEqual(
            gemountet.map((m) => m.el),
            [alt, neu],
        );
    });

    // Randfall 5
    test('gleichzeitige Aufrufe (ohne Warten dazwischen) mounten ebenfalls nur einmal', async () => {
        const e = insel('x', '{}');
        doc.body.append(e);
        const verzeichnis = { x: lader(komp('X')) };

        await Promise.all([mountInseln(verzeichnis, doc, { createApp }), mountInseln(verzeichnis, doc, { createApp })]);

        assert.equal(gemountet.length, 1);
    });

    // Randfall 5
    test('Aufruf auf root und danach auf einem Teil davon mountet nichts doppelt', async () => {
        const innen = h('section');
        const e = insel('x', '{}');
        innen.append(e);
        doc.body.append(innen);
        const verzeichnis = { x: lader(komp('X')) };

        await mountInseln(verzeichnis, doc, { createApp });
        assert.equal(gemountet.length, 1, 'Gegenprobe');
        await mountInseln(verzeichnis, innen, { createApp });

        assert.equal(gemountet.length, 1);
    });
});
