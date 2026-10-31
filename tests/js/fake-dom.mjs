// Minimaler DOM-Ersatz für die JS-Tests des Kits (keine npm-Abhängigkeit).
// Kann: Elemente mit Attributen/Klassen/Kindern, HTML-ähnlicher Aufbau per h(),
// Selektoren (Tag, #id, .klasse, [attr], [attr="wert"], Verbund, Komma, Nachfahre, >),
// closest/matches/contains, Events mit Bubbling, CustomEvent.
export class FakeEvent {
    constructor(type, init = {}) {
        this.type = type;
        this.bubbles = init.bubbles ?? true;
        this.detail = init.detail;
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
        this.children = [];
        this.parentElement = null;
        this.listeners = {};
        this.textContent = '';
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
    appendChild(c) {
        c.parentElement = this;
        this.children.push(c);
        return c;
    }
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
    createElement(t) {
        return new FakeElement(t);
    }
}

// h('div', {class: 'a'}, kind1, kind2)
export function h(tag, attrs = {}, ...kinder) {
    const el = new FakeElement(tag, attrs);
    kinder.forEach((k) => (typeof k === 'string' ? (el.textContent += k) : el.appendChild(k)));
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
