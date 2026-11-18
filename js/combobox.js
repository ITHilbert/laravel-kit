// Combobox des Kits: macht aus jedem select.selectcb (und im Theme kern aus select[data-kit-combobox]) ein durchsuchbares Eingabefeld (Vanilla-JS, keine Abhängigkeiten).
// Das Select bleibt ausgeblendet im DOM und trägt weiter den Formularwert.
const gebunden = new WeakSet();
const aktualisierer = new WeakMap();

const SELEKTOR = 'select.selectcb, select[data-kit-combobox]';
const klein = (t) => String(t).toLowerCase();

// Option erscheint in der Liste: nicht leerer value, nicht per style="display: none" ausgeblendet.
function sichtbar(option) {
    return option.value !== '' && !/display\s*:\s*none/i.test(option.getAttribute('style') ?? '');
}

// Text als Knoten, jedes Vorkommen von begriff (ohne Groß-/Kleinschreibung) in <strong>. Nie als HTML gedeutet.
function hervorheben(li, text, begriff) {
    if (!begriff) {
        li.appendChild(document.createTextNode(text));
        return;
    }
    const heu = klein(text);
    const nadel = klein(begriff);
    let pos = 0;
    let treffer = heu.indexOf(nadel, pos);
    while (treffer !== -1) {
        if (treffer > pos) li.appendChild(document.createTextNode(text.slice(pos, treffer)));
        const strong = document.createElement('strong');
        strong.appendChild(document.createTextNode(text.slice(treffer, treffer + nadel.length)));
        li.appendChild(strong);
        pos = treffer + nadel.length;
        treffer = heu.indexOf(nadel, pos);
    }
    if (pos < text.length) li.appendChild(document.createTextNode(text.slice(pos)));
}

function element(tag, attribute = {}) {
    const el = document.createElement(tag);
    for (const [k, v] of Object.entries(attribute)) el.setAttribute(k, v);
    return el;
}

// Pfeil für den Auslöser im Kern-Theme: festes inline-SVG (keine Nutzerdaten), für Hilfstechnik ausgeblendet.
const PFEIL = '<svg viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false"><path d="M3 6l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5"></path></svg>';

function binde(select) {
    if (gebunden.has(select)) return;
    gebunden.add(select);

    const optionen = () => Array.from(select.options);
    const ausgewaehlt = () => optionen()[select.selectedIndex] ?? null;

    // Kern-Theme (select[data-kit-combobox] in div.kit-combobox): Teile kommen als Geschwister des Selects in die Wurzel.
    const kern = select.hasAttribute('data-kit-combobox') && select.parentElement?.classList.contains('kit-combobox');
    const box = kern ? select.parentElement : element('span', { class: 'kit-combobox' });
    const eingabe = element('input', { type: 'text', class: kern ? 'kit-combobox__input' : 'kit-combobox-input', autocomplete: 'off', role: 'combobox', 'aria-expanded': 'false' });
    const knopf = element('button', { type: 'button', class: kern ? 'kit-combobox__trigger' : 'kit-combobox-button', tabindex: '-1', 'aria-label': 'Liste öffnen' });
    const liste = element('ul', { class: kern ? 'kit-combobox__list' : 'kit-combobox-list', role: 'listbox' });
    liste.hidden = true;
    if (select.hasAttribute('tabindex')) eingabe.setAttribute('tabindex', select.getAttribute('tabindex'));
    eingabe.value = ausgewaehlt()?.text ?? '';

    if (kern) {
        knopf.innerHTML = PFEIL;
        // Das Label zeigt auf das sichtbare Eingabefeld; Fehlerbezug und Zustand gehen mit.
        if (select.id) {
            eingabe.setAttribute('id', select.id);
            select.removeAttribute('id');
        }
        for (const a of ['aria-invalid', 'aria-describedby']) if (select.hasAttribute(a)) eingabe.setAttribute(a, select.getAttribute(a));
        let anker = select;
        for (const teil of [eingabe, knopf, liste]) {
            anker.insertAdjacentElement('afterend', teil);
            anker = teil;
        }
    } else {
        box.appendChild(eingabe);
        box.appendChild(knopf);
        box.appendChild(liste);
        select.insertAdjacentElement('afterend', box);
    }
    select.style.display = 'none';

    let eintraege = []; // [{ li, option }]
    let aktiv = -1;

    const offen = () => !liste.hidden;

    function schliessen() {
        liste.hidden = true;
        if (kern) box.classList.remove('is-open');
        eingabe.setAttribute('aria-expanded', 'false');
        aktiv = -1;
    }

    function markiere(index) {
        eintraege.forEach((e, i) => e.li.classList.toggle(kern ? 'is-active' : 'active', i === index));
        aktiv = index;
        eintraege[index]?.li.scrollIntoView?.({ block: 'nearest' });
    }

    function oeffnen(begriff) {
        liste.textContent = '';
        eintraege = optionen()
            .filter((o) => sichtbar(o) && (!begriff || klein(o.text).includes(klein(begriff))))
            .map((option) => {
                const li = element('li', kern ? { role: 'option', class: 'kit-combobox__item' } : { role: 'option' });
                hervorheben(li, option.text, begriff);
                liste.appendChild(li);
                return { li, option };
            });
        aktiv = -1;
        liste.hidden = false;
        if (kern) box.classList.add('is-open');
        eingabe.setAttribute('aria-expanded', 'true');
    }

    function waehle(option) {
        option.selected = true;
        eingabe.value = option.text;
        schliessen();
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Fokusverlust: genauer Treffer (ohne Groß-/Kleinschreibung) wird ausgewählt, sonst Feld und Auswahl leeren.
    function pruefen() {
        const text = klein(eingabe.value);
        const treffer = optionen().find((o) => klein(o.text) === text);
        if (treffer) {
            treffer.selected = true;
        } else {
            eingabe.value = '';
            select.selectedIndex = -1;
        }
    }

    eingabe.addEventListener('input', () => oeffnen(eingabe.value));
    eingabe.addEventListener('change', pruefen);
    eingabe.addEventListener('blur', () => {
        schliessen();
        pruefen();
    });
    eingabe.addEventListener('keydown', (ev) => {
        if (ev.key === 'ArrowDown') {
            ev.preventDefault();
            if (!offen()) oeffnen('');
            if (eintraege.length) markiere(Math.min(aktiv + 1, eintraege.length - 1));
        } else if (ev.key === 'ArrowUp') {
            ev.preventDefault();
            if (offen() && eintraege.length) markiere(Math.max(aktiv - 1, 0));
        } else if (ev.key === 'Enter') {
            if (offen() && eintraege[aktiv]) {
                ev.preventDefault();
                waehle(eintraege[aktiv].option);
            }
        } else if (ev.key === 'Escape') {
            if (offen()) {
                ev.preventDefault();
                schliessen();
            }
        }
    });

    // mousedown verhindern, damit das Eingabefeld den Fokus behält (kein blur vor dem Klick).
    knopf.addEventListener('mousedown', (ev) => ev.preventDefault());
    knopf.addEventListener('click', () => {
        if (offen()) {
            schliessen();
        } else {
            oeffnen('');
            eingabe.focus();
        }
    });

    // Auswahl per mousedown (vor blur) oder click; nach mousedown ist die Liste zu, der folgende click greift nicht mehr.
    const eintragGeklickt = (ev) => {
        if (!offen()) return;
        const li = ev.target?.closest?.('li');
        const eintrag = eintraege.find((e) => e.li === li);
        if (!eintrag) return;
        ev.preventDefault();
        waehle(eintrag.option);
    };
    liste.addEventListener('mousedown', eintragGeklickt);
    liste.addEventListener('click', eintragGeklickt);

    // Von außen geändertes Select: Eingabefeld neu aus der Auswahl füllen, offene Liste schließen (Liste liest beim Öffnen neu).
    aktualisierer.set(select, () => {
        eingabe.value = ausgewaehlt()?.text ?? '';
        schliessen();
    });
}

// element: das Select selbst oder ein Element, das select.selectcb enthält.
export function aktualisiereCombobox(element) {
    if (!element) return;
    aktualisierer.get(element)?.();
    for (const select of Array.from(element.querySelectorAll?.(SELEKTOR) ?? [])) aktualisierer.get(select)?.();
}

export function initCombobox(root = document) {
    if (!root) return;
    if (root.matches?.(SELEKTOR)) binde(root);
    for (const select of Array.from(root.querySelectorAll(SELEKTOR))) binde(select);
}
