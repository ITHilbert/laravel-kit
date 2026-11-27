// Blade-Lösch-Dialog (Theme kern, <x-kit::dialog-delete>): verdrahtet das Markup mit der Zustandslogik aus loeschen.js.
// Offen/zu steht am Attribut `hidden` der Wurzel [data-kit-delete-dialog]; erzeugeLoeschDialog() schaltet die Klasse
// `hidden` am übergebenen Overlay, deshalb bekommt es hier einen Adapter auf das Attribut.
import { erzeugeLoeschDialog, initLoeschen, onLoeschen } from './loeschen.js';

const gebunden = new WeakSet();

function hiddenAdapter(wurzel) {
    return {
        classList: {
            add: (name) => {
                if (name === 'hidden') wurzel.hidden = true;
            },
            remove: (name) => {
                if (name === 'hidden') wurzel.hidden = false;
            },
        },
    };
}

function binde(wurzel) {
    if (gebunden.has(wurzel)) return;
    gebunden.add(wurzel);

    const dialog = erzeugeLoeschDialog({
        route: () => wurzel.getAttribute('data-route'),
        overlay: hiddenAdapter(wurzel),
        formular: () => globalThis.document.querySelector('#formDelete'),
        globalThis: null,
    });

    onLoeschen(({ id }) => dialog.oeffnenPerEreignis(id));

    wurzel.addEventListener('click', (e) => {
        if (e.target?.closest?.('[data-kit-delete-bestaetigen]')) {
            dialog.bestaetigen();
        } else if (e.target?.closest?.('[data-kit-delete-abbrechen]')) {
            dialog.schliessen();
        }
    });

    globalThis.document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !wurzel.hidden) dialog.schliessen();
    });
}

// Einmal pro Seite aufrufen; mehrfache Aufrufe binden nichts doppelt. Ohne Dialog auf der Seite passiert nichts.
export function initLoeschDialog(root = document) {
    initLoeschen(root);
    root.querySelectorAll('[data-kit-delete-dialog]').forEach(binde);
}
