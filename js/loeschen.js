// Lösch-Button (button-delete) und Dialog-Insel (dialog-delete) sprechen über ein Ereignis, keine Globals.
export const LOESCHEN_EREIGNIS = 'kit:delete';

const gebunden = new WeakSet();

export function initLoeschen(root = document) {
    if (gebunden.has(root)) return;
    gebunden.add(root);
    root.addEventListener('click', (e) => {
        const knopf = e.target?.closest?.('[data-kit-delete]');
        if (!knopf || !root.contains(knopf)) return;
        const ziel = globalThis.document;
        ziel.dispatchEvent(new CustomEvent(LOESCHEN_EREIGNIS, { bubbles: true, detail: { id: String(knopf.getAttribute('data-kit-delete')) } }));
    });
}

export function onLoeschen(callback, ziel = document) {
    const fn = (e) => callback({ id: e.detail.id });
    ziel.addEventListener(LOESCHEN_EREIGNIS, fn);
    return () => ziel.removeEventListener(LOESCHEN_EREIGNIS, fn);
}

// Ersetzt das letzte Pfadsegment der Route durch die (kodierte) ID; Query und Fragment bleiben.
export function loeschRoute(route, id) {
    const m = String(route).match(/^([^?#]*)(.*)$/);
    const pfad = m[1];
    return pfad.slice(0, pfad.lastIndexOf('/') + 1) + encodeURIComponent(id) + m[2];
}
