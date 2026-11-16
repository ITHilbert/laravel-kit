// Combobox des Kits: macht aus jedem select.selectcb ein durchsuchbares Eingabefeld (Vanilla-JS, keine Abhängigkeiten).
// Das Select bleibt ausgeblendet im DOM und trägt weiter den Formularwert.
const gebunden = new WeakSet();

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

function binde(select) {
    if (gebunden.has(select)) return;
    gebunden.add(select);

    const optionen = () => Array.from(select.options);
    const ausgewaehlt = () => optionen()[select.selectedIndex] ?? null;

    const box = element('span', { class: 'kit-combobox' });
    const eingabe = element('input', { type: 'text', class: 'kit-combobox-input', autocomplete: 'off', role: 'combobox', 'aria-expanded': 'false' });
    const knopf = element('button', { type: 'button', class: 'kit-combobox-button', tabindex: '-1', 'aria-label': 'Liste öffnen' });
    const liste = element('ul', { class: 'kit-combobox-list', role: 'listbox' });
    liste.hidden = true;
    if (select.hasAttribute('tabindex')) eingabe.setAttribute('tabindex', select.getAttribute('tabindex'));
    eingabe.value = ausgewaehlt()?.text ?? '';

    box.appendChild(eingabe);
    box.appendChild(knopf);
    box.appendChild(liste);
    select.style.display = 'none';
    select.insertAdjacentElement('afterend', box);

    let eintraege = []; // [{ li, option }]
    let aktiv = -1;

    const offen = () => !liste.hidden;

    function schliessen() {
        liste.hidden = true;
        eingabe.setAttribute('aria-expanded', 'false');
        aktiv = -1;
    }

    function markiere(index) {
        eintraege.forEach((e, i) => e.li.classList.toggle('active', i === index));
        aktiv = index;
        eintraege[index]?.li.scrollIntoView?.({ block: 'nearest' });
    }

    function oeffnen(begriff) {
        liste.textContent = '';
        eintraege = optionen()
            .filter((o) => sichtbar(o) && (!begriff || klein(o.text).includes(klein(begriff))))
            .map((option) => {
                const li = element('li', { role: 'option' });
                hervorheben(li, option.text, begriff);
                liste.appendChild(li);
                return { li, option };
            });
        aktiv = -1;
        liste.hidden = false;
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
}

export function initCombobox(root = document) {
    if (!root) return;
    if (root.matches?.('select.selectcb')) binde(root);
    for (const select of Array.from(root.querySelectorAll('select.selectcb'))) binde(select);
}
