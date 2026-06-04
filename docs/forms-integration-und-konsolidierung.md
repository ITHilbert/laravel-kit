# Forms-Komponenten: Integration & Konsolidierung (input/ → forms/)

Dieses Dokument hält zwei zusammenhängende Erkenntnisse fest:

1. **Integrations-Falle:** Warum Form-Inputs in einem Projekt "nackt" (ohne Rahmen, Label neben dem Feld) erscheinen — und wie man es behebt.
2. **Architektur-Entscheid:** Auflösung des doppelten Ordners `vue/tailwind/input/` zugunsten von `vue/tailwind/forms/`.

> Hintergrund zur Barrel-Architektur und zum 2-Klassen-Semantic-Layer siehe [`../vue/README.md`](../vue/README.md) (Abschnitte 2–4). Dieses Dokument ergänzt die dort beschriebene Theorie um den konkreten Fehlerfall.

---

## 1. Symptom

Form-Felder (z.B. `<input-text>`, `<input-password>`) werden im Projekt fehlerhaft dargestellt:

- Das Eingabefeld hat **keinen Rahmen, kein Padding** (rohes `<input>`).
- Das **Label steht neben statt über** dem Feld (rendert inline statt block).
- Platzhaltertext klebt direkt am Label.

## 2. Ursache

Die `forms/`-Komponenten folgen bewusst dem **Semantic-Layer**: Im `<template>` stehen nur
semantische Klassen (`class="input-text"`, `class="hlabel"`), die Styles liegen in
**separaten `.css`-Dateien** (`@apply`).

Diese `.css`-Dateien werden **nicht** automatisch durch den Tailwind-`@source`-Scan erfasst —
`@source` scannt nur `.vue`/`.blade.php` nach Utility-Klassen, **nicht** nach `@apply`-Regeln
in `.css`. Wenn das Projekt die **Barrel-CSS nicht importiert**, fehlen die Klassen
`.input-text`, `.hlabel` usw. komplett im gebauten CSS → die Komponenten sind ungestylt.

> ⚠️ Häufige Fehlannahme: `@source '...laravel-kit/vue/**/*.vue'` allein reiche aus.
> Das deckt nur Komponenten ab, die ihre Utilities **inline im Template** halten.
> Die `forms/`-Komponenten tun das bewusst NICHT (Semantic-Layer) — sie brauchen die Barrel-CSS.

## 3. Lösung — Barrel-CSS im Projekt importieren (Pflicht)

In der zentralen CSS des Projekts (z.B. `resources/css/app.css`), **nach** `@import 'tailwindcss'`,
die Barrel-Dateien einbinden:

```css
@import 'tailwindcss';
/* ... @source / @theme ... */

/* laravel-kit Komponenten-Styles (Barrel-Dateien) */
@import '../../vendor/ithilbert/laravel-kit/vue/tailwind/forms/forms.css';
@import '../../vendor/ithilbert/laravel-kit/vue/tailwind/buttons/buttons.css';
```

Pro genutzter UI-Kategorie die jeweilige Barrel-Datei importieren (`forms.css`, `buttons.css`, …).
Danach Build prüfen — die Klassen müssen im Output landen:

```bash
npm run build
grep -o '\.input-text[^{]*{' public/build/assets/*.css   # muss Treffer liefern
grep -o '\.hlabel[^{]*{'     public/build/assets/*.css   # muss Treffer liefern
```

## 4. Label-Konvention der Input-Komponenten

`input-text` und `input-password` rendern ein optionales Label über dem Feld via `label`-Prop.
Das Label nutzt die semantische Klasse `.hlabel` (aus `hlabel.css`, Teil der `forms.css`-Barrel):

```html
<input-text name="db_host" id="db_host" label="DB Host" :value="'127.0.0.1'"></input-text>
```

```vue
<!-- forms/input-text.vue (gekürzt) -->
<template>
    <div class="w-full">
        <label v-if="label" :for="id || name" class="hlabel">{{ label }}</label>
        <input type="text" :id="id || name" :name="name" class="input-text" v-bind="$attrs">
    </div>
</template>

<script setup lang="ts">
defineProps<{ name?: string; id?: string; label?: string }>();
defineOptions({ inheritAttrs: false });
</script>
```

`label` ist optional — ohne Prop wird kein `<label>` gerendert (rückwärtskompatibel).

---

## 5. Architektur-Entscheid: `input/` aufgelöst, `forms/` ist kanonisch

Es existierten historisch **zwei parallele Ordner** mit überlappenden Komponenten:

| | `vue/tailwind/input/` (entfernt) | `vue/tailwind/forms/` (kanonisch) |
|---|---|---|
| Syntax | Options API, `data()` | `<script setup lang="ts">`, typisiert |
| Styling | Utility-Mega-String inline (`defaultclass`) | Semantic-Layer + externe `.css` (`@apply`) |
| v-model | teils gebastelt (`currentValue`) | sauber (`modelValue` + `emit`) |
| `name`/`id` | uneinheitlich | konsistent |

**Beschluss:** `forms/` ist die einzig gültige Heimat aller Form-Controls (deckt auch
Checkbox/Radio/Toggle/Select ab, die fachlich kein "input" sind). `input/` wurde vollständig
entfernt; ein Datei-für-Datei-Vergleich ergab, dass `forms/` bei allen überlappenden
Komponenten die bessere Implementierung enthielt. Einzige Ausnahme war der Label-Support, der
zuvor nur in `input/input-text`/`input-password` (Options API) existierte — er wurde in die
modernen `forms/`-Versionen übernommen (siehe Abschnitt 4).

Nur in `input/` vorhandene Komponenten (`combobox2`, `combobox4`, `combobox_1`) wurden nach
`forms/` verschoben.

### Folgen für Integratoren
- Importe auf `vue/tailwind/input/...` existieren nicht mehr → auf `vue/tailwind/forms/...` umstellen.
- Die Master-Plugin-Datei `vue/vueapp_TW.js` importiert ausschließlich aus `forms/`.
