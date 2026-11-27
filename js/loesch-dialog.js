// Blade-Lösch-Dialog (Theme kern, <x-kit::dialog-delete>): verdrahtet das Markup mit der Zustandslogik aus loeschen.js.
// Offen/zu steht am Attribut `hidden` der Wurzel [data-kit-delete-dialog]; erzeugeLoeschDialog() schaltet die Klasse
// `hidden` am übergebenen Overlay, deshalb bekommt es hier einen Adapter auf das Attribut.
import { erzeugeLoeschDialog, initLoeschen, onLoeschen } from './loeschen.js';

const gebunden = new WeakSet();
const ausloeserBeobachtet = new WeakSet();
let ausloeser = null;

// Merkt den Lösch-Button, der den Dialog öffnet. Muss vor initLoeschen() registriert sein, weil dessen Click-Handler
// das Öffnen-Ereignis synchron auslöst.
function beobachteAusloeser(root) {
    if (ausloeserBeobachtet.has(root)) return;
    ausloeserBeobachtet.add(root);
    root.addEventListener('click', (e) => {
        const knopf = e.target?.closest?.('[data-kit-delete]');
        if (knopf) ausloeser = knopf;
    });
}

function fokussierbare(wurzel) {
    return Array.from(wurzel.querySelectorAll('button')).filter((el) => !el.disabled);
}

function hiddenAdapter(wurzel) {
    return {
        classList: {
            add: (name) => {
                if (name !== 'hidden') return;
                const war = !wurzel.hidden;
                wurzel.hidden = true;
                if (!war) return;
                const ziel = ausloeser;
                ausloeser = null;
                if (ziel && globalThis.document.body?.contains?.(ziel)) ziel.focus();
            },
            remove: (name) => {
                if (name !== 'hidden') return;
                wurzel.hidden = false;
                wurzel.querySelector('[data-kit-delete-abbrechen][data-variant="cancel"]')?.focus();
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

    onLoeschen(({ id }) => {
        // Leere oder fehlende ID (AK13): Dialog bleibt zu, es wird nichts gesendet.
        if (id === null || id === undefined || String(id).trim() === '') return;
        dialog.oeffnenPerEreignis(id);
    });

    wurzel.addEventListener('click', (e) => {
        if (e.target?.closest?.('[data-kit-delete-bestaetigen]')) {
            dialog.bestaetigen();
        } else if (e.target?.closest?.('[data-kit-delete-abbrechen]')) {
            dialog.schliessen();
        }
    });

    globalThis.document.addEventListener('keydown', (e) => {
        if (wurzel.hidden) return;
        if (e.key === 'Escape') {
            dialog.schliessen();
        } else if (e.key === 'Tab') {
            const liste = fokussierbare(wurzel);
            if (liste.length === 0) return;
            const erstes = liste[0];
            const letztes = liste[liste.length - 1];
            const aktiv = globalThis.document.activeElement;
            if (e.shiftKey && aktiv === erstes) {
                e.preventDefault();
                letztes.focus();
            } else if (!e.shiftKey && aktiv === letztes) {
                e.preventDefault();
                erstes.focus();
            }
        }
    });
}

// Einmal pro Seite aufrufen; mehrfache Aufrufe binden nichts doppelt. Ohne Dialog auf der Seite passiert nichts.
export function initLoeschDialog(root = document) {
    beobachteAusloeser(root);
    initLoeschen(root);
    root.querySelectorAll('[data-kit-delete-dialog]').forEach(binde);
}
