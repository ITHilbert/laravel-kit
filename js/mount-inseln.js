// Mount-Helfer des Kits: mountet jedes Element mit data-vue als eigene Vue-App (Insel).
// Props kommen aus data-props (JSON-Objekt). Fehler einer Insel überspringen nur diese Insel (console.warn).
const gemountet = new WeakSet();

function warne(name, grund) {
    console.warn(`[kit] Insel "${name}" übersprungen: ${grund}`);
}

function grundVon(fehler) {
    return fehler instanceof Error ? fehler.message : String(fehler);
}

function leseProps(name, element) {
    const roh = element.getAttribute('data-props');
    if (roh === null || roh === undefined) return {};
    let props;
    try {
        props = JSON.parse(roh);
    } catch (fehler) {
        throw new Error(`data-props ist kein gültiges JSON (${grundVon(fehler)})`);
    }
    if (props === null || typeof props !== 'object' || Array.isArray(props)) {
        throw new Error('data-props ist kein JSON-Objekt');
    }
    return props;
}

export async function mountInseln(verzeichnis, root = document, optionen = {}) {
    const elemente = Array.from(root.querySelectorAll('[data-vue]')).filter((el) => {
        if (gemountet.has(el)) return false;
        // synchron vor dem ersten await merken: schützt vor gleichzeitigen Aufrufen
        gemountet.add(el);
        return true;
    });

    let createApp = optionen.createApp;

    await Promise.all(
        elemente.map(async (element) => {
            const name = element.getAttribute('data-vue');
            try {
                const loader = verzeichnis?.[name];
                if (typeof loader !== 'function') {
                    throw new Error('unbekannter Name, nicht im Verzeichnis');
                }
                const props = leseProps(name, element);
                const modul = await loader();
                if (!modul || !modul.default) {
                    throw new Error('Modul hat keinen default-Export');
                }
                if (!createApp) {
                    createApp = (await import('vue')).createApp;
                }
                createApp(modul.default, props).mount(element);
            } catch (fehler) {
                warne(name, grundVon(fehler));
            }
        }),
    );
}
