// Tab-Skript des Kits: schaltet tabs-header/tab-header und tabs-body/tab-body ohne Vue um.
// Bindung über data-Attribute: data-kit-tabs / data-kit-tab (Reiter), data-kit-tabs-body / data-kit-tab-body (Inhalte).
const gebunden = new WeakSet();

function wurzel(el) {
    let e = el;
    while (e.parentElement) e = e.parentElement;
    return e;
}

// Leere Gruppen-ID: der nächste folgende Body im selben Elternelement gehört zum Header.
function folgenderBody(gruppe) {
    const geschwister = gruppe.parentElement ? Array.from(gruppe.parentElement.children) : [];
    for (const e of geschwister.slice(geschwister.indexOf(gruppe) + 1)) {
        if (e.hasAttribute('data-kit-tabs-body')) return e;
    }
    return null;
}

function schalte(knopf) {
    const gruppe = knopf.closest('[data-kit-tabs]');
    if (!gruppe) return;
    const id = gruppe.getAttribute('data-kit-tabs');
    const name = knopf.getAttribute('data-kit-tab');
    const oben = wurzel(gruppe);

    for (const k of gruppe.querySelectorAll('[data-kit-tab]')) {
        const an = k.getAttribute('data-kit-tab') === name;
        k.classList.toggle('active', an);
        k.setAttribute('aria-selected', an ? 'true' : 'false');
    }
    const bodies = id === '' ? [folgenderBody(gruppe)] : [...oben.querySelectorAll('[data-kit-tabs-body]')].filter((b) => b.getAttribute('data-kit-tabs-body') === id);
    for (const body of bodies) {
        if (!body) continue;
        for (const p of body.querySelectorAll('[data-kit-tab-body]')) {
            const an = p.getAttribute('data-kit-tab-body') === name;
            p.classList.toggle('active', an);
            p.classList.toggle('show', an);
        }
    }
}

export function initTabs(root = document) {
    if (gebunden.has(root)) return;
    gebunden.add(root);
    root.addEventListener('click', (e) => {
        const knopf = e.target?.closest?.('[data-kit-tab]');
        if (knopf && root.contains(knopf)) schalte(knopf);
    });
}
