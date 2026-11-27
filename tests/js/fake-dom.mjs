// Minimaler DOM-Ersatz für die JS-Tests des Kits (keine npm-Abhängigkeit).
// Kann: Elemente mit Attributen/Klassen/Kindern, HTML-ähnlicher Aufbau per h(),
// Selektoren (Tag, #id, .klasse, [attr], [attr="wert"], Verbund, Komma, Nachfahre, >),
// closest/matches/contains, Events mit Bubbling, CustomEvent.
export class FakeEvent {
    constructor(type, init = {}) {
        this.type = type;
        this.bubbles = init.bubbles ?? true;
        this.detail = init.detail;
        this.key = init.key;
        this.keyCode = init.keyCode;
        this.shiftKey = init.shiftKey ?? false;
        this.defaultPrevented = false;
        this.target = null;
        this.currentTarget = null;
    }
    preventDefault() {
        this.defaultPrevented = true;
    }
    stopPropagation() {
        this.bubbles = false;
    }
}

// Textknoten (für gemischten Inhalt wie `B<strong>an</strong>ane`).
export class FakeText {
    constructor(text = '') {
        this.nodeType = 3;
        this.textContent = String(text);
        this.parentElement = null;
    }
    get parentNode() {
        return this.parentElement;
    }
}

const VOID = new Set(['br', 'hr', 'img', 'input']);
const esc = (t) => t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
const dekodiere = (t) =>
    t.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#0?39;|&#x27;/g, "'").replace(/&amp;/g, '&');

class Klassen {
    constructor(el) {
        this.el = el;
    }
    liste() {
        return (this.el.getAttribute('class') ?? '').split(/\s+/).filter(Boolean);
    }
    schreiben(l) {
        this.el.setAttribute('class', l.join(' '));
    }
    add(...n) {
        const l = this.liste();
        n.forEach((x) => l.includes(x) || l.push(x));
        this.schreiben(l);
    }
    remove(...n) {
        this.schreiben(this.liste().filter((x) => !n.includes(x)));
    }
    contains(n) {
        return this.liste().includes(n);
    }
    toggle(n, force) {
        const an = force ?? !this.contains(n);
        an ? this.add(n) : this.remove(n);
        return an;
    }
}

function teile(s, trenner) {
    const r = [];
    let t = '';
    let q = null;
    let k = 0;
    for (const c of s) {
        if (q) {
            if (c === q) q = null;
        } else if (c === '"' || c === "'") q = c;
        else if (c === '[') k++;
        else if (c === ']') k--;
        if (!q && k === 0 && c === trenner) {
            r.push(t);
            t = '';
        } else t += c;
    }
    r.push(t);
    return r;
}

function verbundPasst(el, v) {
    let rest = v.trim();
    while (rest) {
        let m;
        if ((m = rest.match(/^\[([^\]=~|^$*]+)(?:=(?:"([^"]*)"|'([^']*)'|([^\]]*)))?\]/))) {
            const wert = m[2] ?? m[3] ?? m[4];
            if (!el.hasAttribute(m[1])) return false;
            if (wert !== undefined && el.getAttribute(m[1]) !== wert) return false;
        } else if ((m = rest.match(/^\.([\w-]+)/))) {
            if (!el.classList.contains(m[1])) return false;
        } else if ((m = rest.match(/^#([\w-]+)/))) {
            if (el.id !== m[1]) return false;
        } else if ((m = rest.match(/^([a-zA-Z][\w-]*|\*)/))) {
            if (m[1] !== '*' && el.tagName.toLowerCase() !== m[1].toLowerCase()) return false;
        } else throw new Error(`Selektor nicht unterstützt: ${v}`);
        rest = rest.slice(m[0].length);
    }
    return true;
}

function teile_kette(s) {
    // "a > b c" -> [{v:'a'},{v:'b',komb:'>'},{v:'c',komb:' '}]
    const tokens = s.trim().replace(/\s*>\s*/g, ' > ').split(/\s+(?![^\[]*\])/).filter(Boolean);
    const kette = [];
    let komb = ' ';
    for (const t of tokens) {
        if (t === '>') komb = '>';
        else {
            kette.push({ v: t, komb });
            komb = ' ';
        }
    }
    return kette;
}

function passt(el, selektor) {
    return teile(selektor, ',').some((alt) => {
        const kette = teile_kette(alt);
        const pruefe = (e, i) => {
            if (!verbundPasst(e, kette[i].v)) return false;
            if (i === 0) return true;
            if (kette[i].komb === '>') return !!e.parentElement && pruefe(e.parentElement, i - 1);
            for (let p = e.parentElement; p; p = p.parentElement) if (pruefe(p, i - 1)) return true;
            return false;
        };
        return pruefe(el, kette.length - 1);
    });
}

export class FakeElement {
    constructor(tagName = 'div', attrs = {}) {
        this.tagName = tagName.toUpperCase();
        this.attrs = new Map();
        this.childNodes = [];
        this.parentElement = null;
        this.listeners = {};
        this.classList = new Klassen(this);
        for (const [k, v] of Object.entries(attrs)) this.setAttribute(k, v);
    }
    get id() {
        return this.getAttribute('id') ?? '';
    }
    get dataset() {
        const el = this;
        return new Proxy(
            {},
            {
                get: (_, k) => el.getAttribute('data-' + String(k).replace(/[A-Z]/g, (c) => '-' + c.toLowerCase())) ?? undefined,
            },
        );
    }
    setAttribute(k, v) {
        this.attrs.set(k, String(v));
    }
    getAttribute(k) {
        return this.attrs.has(k) ? this.attrs.get(k) : null;
    }
    hasAttribute(k) {
        return this.attrs.has(k);
    }
    removeAttribute(k) {
        this.attrs.delete(k);
    }
    get nodeType() {
        return 1;
    }
    get children() {
        return this.childNodes.filter((n) => n.nodeType === 1);
    }
    get textContent() {
        return this.childNodes.map((n) => n.textContent).join('');
    }
    set textContent(t) {
        this.childNodes.forEach((c) => (c.parentElement = null));
        this.childNodes = t === '' || t == null ? [] : [Object.assign(new FakeText(t), { parentElement: this })];
    }
    get innerHTML() {
        return this.childNodes.map((n) => (n.nodeType === 3 ? esc(n.textContent) : n.outerHTML)).join('');
    }
    // Minimaler Parser: Tags, Text, Entities (&lt; &gt; &amp; &quot; &#39;). Unmaskiertes `<b>` wird zum Element.
    set innerHTML(html) {
        this.textContent = '';
        const stack = [this];
        const re = /<(\/?)([a-zA-Z][\w-]*)\s*([^>]*)>|([^<]+)/g;
        let m;
        while ((m = re.exec(String(html)))) {
            const top = stack[stack.length - 1];
            if (m[4] !== undefined) top.appendChild(new FakeText(dekodiere(m[4])));
            else if (m[1]) {
                if (stack.length > 1) stack.pop();
            } else {
                const el = new FakeElement(m[2]);
                top.appendChild(el);
                if (!/\/\s*$/.test(m[3]) && !VOID.has(m[2].toLowerCase())) stack.push(el);
            }
        }
    }
    get outerHTML() {
        const t = this.tagName.toLowerCase();
        const a = [...this.attrs].map(([k, v]) => ` ${k}="${v}"`).join('');
        return `<${t}${a}>${this.innerHTML}</${t}>`;
    }
    _loese() {
        this.parentElement?.removeChild(this);
    }
    appendChild(c) {
        if (c.parentElement) c._loese ? c._loese() : c.parentElement.removeChild(c);
        c.parentElement = this;
        this.childNodes.push(c);
        return c;
    }
    removeChild(c) {
        this.childNodes = this.childNodes.filter((n) => n !== c);
        c.parentElement = null;
        return c;
    }
    insertBefore(neu, ref) {
        if (!ref) return this.appendChild(neu);
        if (neu.parentElement) neu.parentElement.removeChild(neu);
        const i = this.childNodes.indexOf(ref);
        neu.parentElement = this;
        this.childNodes.splice(i, 0, neu);
        return neu;
    }
    insertAdjacentElement(pos, el) {
        if (pos === 'afterend') this.parentElement.insertBefore(el, this.nextSibling);
        else if (pos === 'beforebegin') this.parentElement.insertBefore(el, this);
        else if (pos === 'afterbegin') this.insertBefore(el, this.childNodes[0] ?? null);
        else this.appendChild(el);
        return el;
    }
    after(...ns) {
        let ref = this.nextSibling;
        ns.forEach((n) => this.parentElement.insertBefore(typeof n === 'string' ? new FakeText(n) : n, ref));
    }
    before(...ns) {
        ns.forEach((n) => this.parentElement.insertBefore(typeof n === 'string' ? new FakeText(n) : n, this));
    }
    remove() {
        this._loese();
    }
    replaceChildren(...ns) {
        this.textContent = '';
        ns.forEach((n) => this.appendChild(typeof n === 'string' ? new FakeText(n) : n));
    }
    get nextSibling() {
        const l = this.parentElement?.childNodes ?? [];
        return l[l.indexOf(this) + 1] ?? null;
    }
    get nextElementSibling() {
        const l = this.parentElement?.children ?? [];
        return l[l.indexOf(this) + 1] ?? null;
    }
    get previousElementSibling() {
        const l = this.parentElement?.children ?? [];
        return l[l.indexOf(this) - 1] ?? null;
    }
    set id(v) {
        this.setAttribute('id', v);
    }
    get className() {
        return this.getAttribute('class') ?? '';
    }
    set className(v) {
        this.setAttribute('class', v);
    }
    get type() {
        return this.getAttribute('type') ?? '';
    }
    set type(v) {
        this.setAttribute('type', v);
    }
    get hidden() {
        return this.hasAttribute('hidden');
    }
    set hidden(v) {
        v ? this.setAttribute('hidden', '') : this.removeAttribute('hidden');
    }
    // style.display wird auf das style-Attribut abgebildet (Lesen und Schreiben).
    get style() {
        const el = this;
        return {
            get display() {
                const m = (el.getAttribute('style') ?? '').match(/display\s*:\s*([^;]+)/);
                return m ? m[1].trim() : '';
            },
            set display(v) {
                const rest = (el.getAttribute('style') ?? '').replace(/display\s*:\s*[^;]+;?/, '').trim();
                el.setAttribute('style', (rest ? rest + ' ' : '') + (v ? `display: ${v};` : ''));
            },
        };
    }
    // Formularwerte. Ein Select ohne ausgewählte Option hat selectedIndex -1 (kein Browser-Standard "erste Option").
    get options() {
        return this.querySelectorAll('option');
    }
    get selected() {
        return this._sel ?? this.hasAttribute('selected');
    }
    set selected(v) {
        this._sel = !!v;
        const sel = this.closest('select');
        if (v && sel) sel.options.forEach((o) => o !== this && (o._sel = false));
    }
    get selectedIndex() {
        return this.options.findIndex((o) => o.selected);
    }
    set selectedIndex(i) {
        this.options.forEach((o, n) => (o._sel = n === i));
    }
    get text() {
        return this.textContent;
    }
    get value() {
        const t = this.tagName;
        if (t === 'SELECT') return this.options.find((o) => o.selected)?.value ?? '';
        if (t === 'OPTION') return this.getAttribute('value') ?? this.textContent;
        return this._value ?? this.getAttribute('value') ?? '';
    }
    set value(v) {
        const t = this.tagName;
        if (t === 'SELECT') this.selectedIndex = this.options.findIndex((o) => o.value === String(v));
        else if (t === 'OPTION') this.setAttribute('value', v);
        else this._value = String(v);
    }
    // Fokus wie im Browser: nur für Elemente im Dokument und ohne `hidden`-Vorfahr; sonst bleibt der Fokus, wo er ist.
    focus() {
        let wurzel = this;
        for (let e = this; e; e = e.parentElement) {
            if (e.hasAttribute?.('hidden')) return;
            wurzel = e;
        }
        if (wurzel instanceof FakeDocument) wurzel._aktiv = this;
    }
    select() {}
    append(...cs) {
        cs.forEach((c) => this.appendChild(c));
    }
    get parentNode() {
        return this.parentElement;
    }
    matches(s) {
        return passt(this, s);
    }
    closest(s) {
        for (let e = this; e; e = e.parentElement) if (e.matches(s)) return e;
        return null;
    }
    contains(andere) {
        for (let e = andere; e; e = e.parentElement) if (e === this) return true;
        return false;
    }
    querySelectorAll(s) {
        const r = [];
        const lauf = (e) => {
            for (const c of e.children) {
                if (c.matches(s)) r.push(c);
                lauf(c);
            }
        };
        lauf(this);
        return r;
    }
    querySelector(s) {
        return this.querySelectorAll(s)[0] ?? null;
    }
    addEventListener(t, fn) {
        (this.listeners[t] ??= []).push(fn);
    }
    removeEventListener(t, fn) {
        this.listeners[t] = (this.listeners[t] ?? []).filter((f) => f !== fn);
    }
    dispatchEvent(ev) {
        ev.target ??= this;
        for (let e = this; e; e = e.parentElement) {
            ev.currentTarget = e;
            for (const fn of [...(e.listeners[ev.type] ?? [])]) fn.call(e, ev);
            if (!ev.bubbles) break;
        }
        return !ev.defaultPrevented;
    }
    click() {
        return this.dispatchEvent(new FakeEvent('click'));
    }
}

export class FakeDocument extends FakeElement {
    constructor() {
        super('#document');
        this.body = this.appendChild(new FakeElement('body'));
    }
    // Entfernte oder ausgeblendete Elemente haben keinen Fokus; dann gilt body.
    get activeElement() {
        const a = this._aktiv;
        if (!a || !this.contains(a)) return this.body;
        for (let e = a; e; e = e.parentElement) if (e.hasAttribute?.('hidden')) return this.body;
        return a;
    }
    createElement(t) {
        return new FakeElement(t);
    }
    createTextNode(t) {
        return new FakeText(t);
    }
}

// h('div', {class: 'a'}, kind1, kind2)
export function h(tag, attrs = {}, ...kinder) {
    const el = new FakeElement(tag, attrs);
    kinder.forEach((k) => (typeof k === 'string' ? el.appendChild(new FakeText(k)) : el.appendChild(k)));
    return el;
}

export class FakeCustomEvent extends FakeEvent {}

// Installiert document/CustomEvent global; gibt das Dokument zurück.
export function installiere() {
    const doc = new FakeDocument();
    globalThis.document = doc;
    globalThis.CustomEvent = FakeCustomEvent;
    globalThis.Event = FakeEvent;
    return doc;
}
