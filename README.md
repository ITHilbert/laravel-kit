# ITHilbert Laravel Kit

**Beschreibung**: Tools für das Programmieren mit Laravel.

## Dokumentation

Die ausführliche Dokumentation befindet sich im Ordner `.project/doku/`:
- [Forms-Komponenten: Integration & Konsolidierung](.project/doku/forms-integration-und-konsolidierung.md)

### Vue-Komponenten (Tailwind)
- [DataTable-Komponente](.project/doku/vue-datatable.md) — generische DataTables.net-Kapselung mit Filter-Zeile, Aktions-Spalte und Server-/Client-side Modus

## Installation

```bash
composer require ithilbert/laravel-kit
```

## Kit-Components (Blade)

Kit-Components sind Blade-Components mit dem Präfix `kit`. Aufruf: `<x-kit::NAME …>`. Der Name entspricht der gleichnamigen Vue-Komponente (`<x-kit::card-main>` entspricht `<card-main>`). Ihre Attribute stimmen mit der Vue-Komponente überein. Weitere Attribute wie `class`, `id`, `required` oder `data-*` landen am Hauptelement.

Das Markup kommt aus dem Template des konfigurierten Themes. Kit-Components erzeugen keine Vue-Tags und keine `v-`- oder `:`-Attribute. Ein Ausdruck wie `{{ 7*7 }}` in einem Attribut wird als Text ausgegeben.

### Theme-Schalter

Das Theme legt die Config `laravelkit.theme` fest (Env `LARAVELKIT_THEME`, Paket-Config `src/Config/laravelkit.php`):

- `tailwind` (Default, wenn `LARAVELKIT_THEME` nicht gesetzt ist)
- `bootstrap5`

Jeder andere Wert führt beim Rendern einer Kit-Component zu einer `ITHilbert\LaravelKit\Kit\UnbekanntesThemeException`. Die Meldung nennt den Wert und die erlaubten Werte. Ein leerer Wert (`LARAVELKIT_THEME=` oder `null`) gilt ebenfalls als unbekannt. Ein Rückfall auf ein anderes Theme erfolgt nicht.

```env
LARAVELKIT_THEME=bootstrap5
```

### Verfügbare Kit-Components

- Karten: `card-main`, `card-main-header`, `card-body`, `card-footer`
- Buttons: `button-create`, `button-edit`, `button-show`, `button-delete`, `button-save`, `button-back`, `button-submit`, `button-cancel`, `button` (`variant` ergibt die Klasse `btn-<variant>`, Default `primary`; `type` den Typ, Default `button`)
- Formularfelder: `label`, `input-text`, `input-email`, `input-password`, `input-date`, `input-euro`, `input-number`, `input-hidden`, `input-file`, `input-file-img`, `text-area`, `checkbox`, `combobox`, `select`
  - `combobox`: `options` als Array, Collection oder JSON-Text mit `cbKey`/`cbCaption`; `value` wählt die Option.
  - `select`: `options` wie `combobox`, zusätzlich als Schlüssel→Text-Array (`['a' => 'Alpha']`); `value` wählt die Option, bei `multiple` alle Optionen aus einem Array. `placeholder` ergibt eine erste Option mit leerem Wert.
  - `label`: `for` setzt das Ziel; der Text wird escaped.
  - Theme `bootstrap5`: `input-text`, `input-email`, `input-password`, `input-date`, `input-number`, `input-euro`, `text-area` und `combobox` erzeugen mit `label="Text"` davor ein `<label for="<feld-id>" class="form-label">` (über `kit::label`, Text escaped, leer = kein Label). `for` zeigt auf die id des Feldes; die `combobox` bekommt mit `label` ohne `id` die id aus dem Namen.
  - `input-file`: `accept`, `multiple` und `required` gehen an das `<input>`. Ein Wert wird nie vorbelegt.
- Tabs: `tabs-header`, `tab-header`, `tabs-body`, `tab-body`
- Sonstiges: `breadcrumb`, `show-euro`, `show-date`

Fehlt ein Pflicht-Attribut, das die Vue-Komponente mit Default hat (z. B. `button-save` ohne Text), gilt derselbe Default.

### Beispiel

```blade
<x-kit::card-main>
    <x-kit::card-main-header>Kunde anlegen</x-kit::card-main-header>
    <x-kit::card-body>
        <form method="POST" action="{{ route('kunden.store') }}">
            @csrf
            <x-kit::input-text name="name" label="Name" :value="old('name')" required />
            <x-kit::input-euro name="stundensatz" :value="old('stundensatz')" />
            <x-kit::button-save />
        </form>
    </x-kit::card-body>
</x-kit::card-main>
```

### Log-Tabelle abschalten

Die Migration der Tabelle `log` wird standardmäßig geladen. Mit der Config `laravelkit.log_tabelle` (Env `LARAVELKIT_LOG_TABELLE=false`) entfällt sie; alle anderen Migrationen laufen weiter.

### old() und Fehleranzeige

Die Felder `input-text`, `input-email`, `input-date`, `input-number`, `input-euro`, `text-area`, `combobox` und `select` belegen ihren Wert aus `old(<Feldname>)` vor, wenn kein `value` übergeben ist (bei `text-area` auch kein Slot-Inhalt). Ein übergebenes `value` hat Vorrang. In Bearbeiten-Formularen schreibt man deshalb:

```blade
<x-kit::input-text name="name" label="Name" :value="old('name', $kunde->name)" />
```

Feldnamen in Array-Schreibweise werden in Punktschreibweise gelesen: `adresse[ort]` liest `old('adresse.ort')`, `tags[]` liest `old('tags')`. Liefert `old()` ein Array für ein Einzelfeld, bleibt das Feld leer. `input-password`, `input-file` und `input-file-img` werden nie vorbelegt.

Die Fehleranzeige ist standardmäßig aus. Sie wird pro Feld mit dem Attribut `fehler` (`:fehler="false"` schaltet aus) oder für alle Felder über die Config `laravelkit.fehleranzeige` (Env `LARAVELKIT_FEHLERANZEIGE=true`) aktiviert; das Attribut geht der Config vor, ein leeres `fehler=""` folgt der Config, ein Attribut `error="Text"` aktiviert das Feld ebenfalls. Das Attribut `fehler` erscheint nicht im HTML, `old()` bleibt immer aktiv.

Ist die Fehleranzeige aktiv und enthält der geteilte `$errors`-Bag Fehler zum Feldnamen, zeigen `input-text`, `input-email`, `input-password`, `input-date`, `input-number`, `input-euro`, `input-file`, `input-file-img`, `text-area`, `combobox` und `select` die erste Meldung unter dem Feld:

- Die Meldung steht in einem Element mit der id `<id>-error`. Tailwind: Klasse `field-error`, Bootstrap 5: `invalid-feedback d-block`.
- Das Feld bekommt `aria-invalid="true"` und `aria-describedby="<id>-error"`. Ein übergebenes `aria-describedby` bleibt erhalten, die Fehler-id wird angehängt.
- Im Theme `bootstrap5` bekommt das Feld zusätzlich die Klasse `is-invalid`.
- Die Meldung wird escaped. Ein Attribut `error="Text"` überschreibt die Meldung aus dem Bag.
- Ohne `$errors` im View (z. B. Rendern ohne Middleware) erscheint nichts.

**Upgrade-Hinweis:** Wer Fehler selbst anzeigt, ändert nichts (das Kit zeigt standardmäßig keine). Wer die Kit-Anzeige will, setzt `fehler` am Feld oder `LARAVELKIT_FEHLERANZEIGE=true`.

Die id ist das Attribut `id`, sonst der Feldname ohne `[` und `]`: `adresse[ort]` ergibt `adresse_ort`, `tags[]` ergibt `tags`.

### Dateien hochladen

`input-file` und `input-file-img` funktionieren nur in einem Formular mit `enctype="multipart/form-data"`. Das Kit setzt das Attribut nicht selbst:

```blade
<form method="post" enctype="multipart/form-data">
    @csrf
    <x-kit::input-file name="bild" label="Bild" accept="image/png" />
    <x-kit::button-save />
</form>
```

### HForm und Blade-Formulare

Für Blade-Formulare ist `HForm` durch die Formular-Components oben ersetzbar. `HForm` bleibt für Bestandsprojekte erhalten.

### CSS-Klassen

Das Kit-CSS liefert `field-error` für die Fehlermeldung (`vue/tailwind/forms/field-error.css`, über `forms.css` eingebunden) sowie `btn-primary` und `btn-secondary` für `<x-kit::button>` (`vue/tailwind/buttons/`). Im Theme `bootstrap5` gelten die Bootstrap-Klassen `invalid-feedback`, `is-invalid`, `btn-primary` und `btn-secondary`. Die CSS-Dateien müssen im Projekt eingebunden sein (siehe „Integrations-Voraussetzungen“).

### Vue-Checkbox (`checkbox`, tailwind und bootstrap5)

`checked` entscheidet über das Häkchen: `checked="1"`, `checked="checked"`, `checked="true"`, `:checked="true"` und `:checked="1"` haken an (Groß-/Kleinschreibung egal), `checked="0"`, `checked="false"`, `:checked="false"` nicht – auch nicht bei `value="1"`. Ohne `checked` gilt wie bisher `value` 1 als angehakt. `checked` ohne Wert (`<checkbox checked>`) ist `checked=""` und hakt **nicht** an; zum Anhaken `checked="1"` oder `:checked="true"` schreiben. `value` bleibt der gesendete Wert.

### Checkbox im Theme bootstrap5

Im Theme `bootstrap5` bestimmt das Attribut `checked` den Zustand. `value` ist nur der gesendete Wert (Default `1`). Ohne `checked` ist die Checkbox nicht angehakt. Die Vue-Version aus `vue/bootstrap5` nimmt nur ohne `checked` den `value` als Zustand (siehe Abschnitt „Vue-Checkbox“); die Kit-Component ist ohne `checked` nie angehakt und weicht darin bewusst ab.

```blade
<x-kit::checkbox name="aktiv" value="1" :checked="$kunde->aktiv" />
```

Im Theme `tailwind` folgt die Kit-Component der Vue-Version: angehakt ist sie, wenn `value` gleich `1` oder `true` ist.

### Tabs

Eine Tab-Gruppe bindet ihre Reiter und Inhalte über `data-kit-tabs` (am `tabs-header`) und `data-kit-tabs-body` (am `tabs-body`). Der Name (`name`) eines Reiters und seines Inhalts muss zusammenpassen. Die Reiter-Namen gelten seitenweit: Sie erzeugen die IDs `<name>-tab` und `<name>`. Zwei Gruppen auf einer Seite schalten unabhängig voneinander, solange ihre Namen eindeutig sind.

Ohne Gruppennamen (kein `data-kit-tabs`, die Components setzen dann einen leeren Wert) gehört ein `tabs-header` zum nächsten folgenden `tabs-body` in Dokumentreihenfolge. Header und Body müssen dafür nicht im selben Elternelement stehen. Der Header darf zum Beispiel in `card-main-header` liegen und der Body in `card-body`:

```blade
<x-kit::card-main>
    <x-kit::card-main-header>
        <x-kit::tabs-header>
            <x-kit::tab-header name="tab-daten" active>Daten</x-kit::tab-header>
            <x-kit::tab-header name="tab-adresse">Adresse</x-kit::tab-header>
        </x-kit::tabs-header>
    </x-kit::card-main-header>
    <x-kit::card-body>
        <x-kit::tabs-body>
            <x-kit::tab-body name="tab-daten" active>…</x-kit::tab-body>
            <x-kit::tab-body name="tab-adresse">…</x-kit::tab-body>
        </x-kit::tabs-body>
    </x-kit::card-body>
</x-kit::card-main>
```

Mit Gruppennamen gehören alle `tabs-body` mit demselben Wert in `data-kit-tabs-body` zur Gruppe, egal wo sie stehen. Gleiche Namen in zwei Gruppen erzeugen doppelte IDs. Bootstrap-JS kann dann falsch reagieren, das ist nicht unterstützt.

`active` (Boolean, Default `false`) markiert den Reiter und seinen Inhalt, der beim Laden sichtbar sein soll. Ohne `active` ist beim Laden kein Reiter sichtbar.

```blade
<x-kit::tabs-header data-kit-tabs="stammdaten">
    <x-kit::tab-header name="tab-daten" active>Daten</x-kit::tab-header>
    <x-kit::tab-header name="tab-adresse">Adresse</x-kit::tab-header>
</x-kit::tabs-header>
<x-kit::tabs-body data-kit-tabs-body="stammdaten">
    <x-kit::tab-body name="tab-daten" active>…</x-kit::tab-body>
    <x-kit::tab-body name="tab-adresse">…</x-kit::tab-body>
</x-kit::tabs-body>
```

Das Umschalten übernimmt das Kit-Skript `js/tabs.js` ohne Vue. Es wird einmal pro Seite eingebunden:

```javascript
import { initTabs } from '../../vendor/ithilbert/laravel-kit/js/tabs.js';

initTabs();
```

Das Tailwind-Theme braucht zusätzlich `tabs.css` (siehe Integrations-Voraussetzungen), damit `.tab-body.active` greift.

### Lösch-Button und Lösch-Dialog

`button-delete` nimmt `value` entgegen, die ID des Datensatzes. Der Button gibt sie als `data-kit-delete` aus und hat `type="button"`. Ohne `value` fehlt `data-kit-delete`. Ein Klick meldet die ID über das Ereignis `kit:delete`. `initLoeschen()` aus `js/loeschen.js` nimmt die Meldung entgegen und muss einmal pro Seite laufen:

```blade
<x-kit::button-delete value="{{ $kunde->id }}" />
```

```javascript
import { initLoeschen } from '../../vendor/ithilbert/laravel-kit/js/loeschen.js';

initLoeschen();
```

Den Dialog bindet die Insel `dialog-delete` (Theme `tailwind`) über `data-vue` und `data-props` ein. Die Props sind `titel`, `body` und `route`. Die Route endet auf ein Pfadsegment, das der Dialog durch die ID des Datensatzes ersetzt. Ein Dialog pro Seite genügt für alle `button-delete`-Buttons der Seite:

```blade
<div data-vue="dialog-delete" data-props="{{ json_encode([
    'titel' => 'Kunde löschen',
    'body' => 'Den Kunden wirklich löschen?',
    'route' => route('kunden.destroy', 0),
]) }}"></div>
```

Der Dialog sendet nicht selbst. Er setzt die `action` des Formulars `#formDelete` der Seite auf die Lösch-Route und sendet es ab. Das Formular mit `id="formDelete"` legt das Projekt an. Methode (`@method('DELETE')`) und CSRF-Feld (`@csrf`) gehören in dieses Formular.

**Bisheriger Öffnungsweg:** Projekte, die den Dialog selbst öffnen, funktionieren weiter. Sie setzen eine globale `deleteID` und rufen `document.getElementById('myModalDelete').classList.remove('hidden')` auf. Das Overlay behält dafür `id="myModalDelete"` und trägt das Attribut `route` mit dem Wert des Props. „Löschen“ bildet die Route dann aus `deleteID`. Öffnet ein `button-delete` den Dialog, gilt dessen ID, auch wenn `deleteID` gesetzt ist. Diese ID gilt nur für diesen einen Öffnungsvorgang. Nach Schließen (Abbruch, Escape, Klick neben den Dialog) oder Absenden ist sie vergessen, ein danach über den alten Weg geöffneter Dialog nutzt wieder `deleteID`. Ist keine Route bestimmbar (kein Ereignis, `deleteID` nicht gesetzt oder leer) oder fehlt `#formDelete`, schließt „Löschen“ den Dialog, ohne zu senden.

Die Zustandslogik des Dialogs steht in `erzeugeLoeschDialog()` aus `js/loeschen.js`. Die Vue-Komponente verdrahtet sie nur.

Die Insel wird mit dem Mount-Helfer gemountet (siehe unten).

### Mount-Helfer `mountInseln`

`mountInseln(verzeichnis, root = document)` aus `js/mount-inseln.js` mountet Vue-Inseln (Vue 3). Für jedes Element mit `data-vue` unter `root` lädt er die Komponente aus dem Insel-Verzeichnis und mountet sie als eigene App. Die Props kommen aus `data-props` (JSON-Objekt). Fehlt `data-props`, hat die Insel keine Props.

Das Insel-Verzeichnis ist ein Objekt. Der Schlüssel entspricht dem Wert von `data-vue`, der Wert ist ein Lazy-Loader:

```javascript
import { mountInseln } from '../../vendor/ithilbert/laravel-kit/js/mount-inseln.js';

mountInseln({
    'dialog-delete': () => import('../../vendor/ithilbert/laravel-kit/vue/tailwind/dialog/delete.vue'),
    'kunden-liste': () => import('./components/KundenListe.vue'),
});
```

Ein Element wird nur einmal gemountet. Ein zweiter Aufruf auf demselben `root` überspringt bereits gemountete Inseln. Der Helfer setzt Vue 3 voraus. Vue 2.7 wird nicht unterstützt.

**Fehler isolieren:** Ein unbekannter Name, ein ungültiges `data-props` (kein JSON oder kein JSON-Objekt) oder ein Ladefehler überspringt nur diese Insel. Dabei erscheint eine Warnung in der Konsole (`console.warn`, mit Name und Grund). Die übrigen Inseln werden gemountet.

### Whole-DOM-Mount (veraltet)

Die Dateien `vue/vueapp_TW.js`, `vue/vueapp_B5.js` und `vue/vueapp_B4.js` registrieren alle Vue-Komponenten des Kits als Komponenten-Plugin für die ganze Seite. Dieser Whole-DOM-Mount ist **veraltet**. Neue Seiten mounten Inseln mit `mountInseln`.

## Integrations-Voraussetzungen

Das Laravel-Kit bringt Vue-Komponenten (`<data-table>`, `<tabs>`, `<button-*>`) mit, die Abhängigkeiten vom Konsumprojekt haben. Diese müssen explizit geladen werden.

### DataTable + Buttons + Tabs: Globale Abhängigkeiten einrichten

#### 1. jQuery + DataTables.net global setzen

Erstelle `resources/js/datatable.js` im Konsumprojekt:

```javascript
import $ from 'jquery';
import DataTable from 'datatables.net-dt';

window.$ = window.jQuery = $;
window.DataTable = DataTable;
```

**Wichtig:** Diese Datei MUSS im Blade-Layout **vor** der Vue-App geladen sein (damit jQuery-Init abläuft, bevor Komponenten jQuery brauchen).

**In `resources/views/layouts/app.blade.php` oder Template:**
```blade
<script src="{{ asset('js/datatable.js') }}"></script>
```

#### 2. CSS aus dem Kit importieren

In `resources/css/app.css` folgende Imports hinzufügen (NACH `@import 'tailwindcss'`, weil Buttons/Tabs `@apply` nutzen):

```css
@import 'tailwindcss';
@import '../../vendor/ithilbert/laravel-kit/vue/tailwind/buttons/buttons.css';
@import '../../vendor/ithilbert/laravel-kit/vue/tailwind/tabs/tabs.css';
```

**Auswirkung wenn fehlend:**
- Ohne `buttons.css`: `<button-edit>`, `<button-delete>` etc. sind unsichtbar
- Ohne `tabs.css`: Alle Tab-Inhalte sichtbar untereinander (CSS-Regeln `.tab-body.active` fehlen)

#### 3. Vue globalProperty für window

**Veraltet:** Dieser Mount der ganzen Seite (Whole-DOM) wird durch `mountInseln` abgelöst (siehe „Kit-Components (Blade)“).

In `resources/js/vueapp.js` (wo die Vue-App initialisiert wird):

```javascript
const app = createApp({});

// KRITISCH: window als globalProperty BEVOR Komponenten registriert werden
app.config.globalProperties.window = window;

// Dann Komponenten registrieren (oder mit @edit/@delete Event-Handlern arbeiten, die window nutzen)
app.component('data-table', defineAsyncComponent(() => 
  import('../../vendor/ithilbert/laravel-kit/vue/tailwind/datatable/DataTable.vue')
));

app.mount('#vue-app');
```

**Auswirkung wenn fehlend:** Event-Handler wie `@edit="row => window.location.href = ..."` crashen mit "Cannot read properties of undefined (reading 'location')".

### HForm: Escaping (Verhaltensänderung)
`HForm` escapt alle übergebenen Werte (Name, Wert, Klasse, Attributwerte, Label-, Options-, Textarea- und Kommentartexte, Pfade) wie Laravels `e()`. Attribute in `$attributes` mit ungültigem Schlüssel (Leerzeichen, `"`, `'`, `>`, `/`, `=` oder leer) werden nicht ausgegeben. Früher wurden Werte ungeprüft eingesetzt.

Gewolltes HTML (z. B. ein Label mit `<span>`) übergibst du als `Htmlable`; es bleibt unverändert:

```php
use Illuminate\Support\HtmlString;

HForm::label(new HtmlString('Name <span class="text-danger">*</span>'), 'name');
```

### HForm: Label und Eingabefeld (Verhaltensänderung)
`HForm::checkBox()` gibt dem `<input>` eine `id` (aus `$attributes['id']`, sonst `name`) und setzt `for` des Labels auf dieselbe id; ein Klick auf das Label hakt die Box an. Mehrere Boxen einer Gruppe (`rechte[]`) bekommen ihre eigene id über `$attributes` — ohne eigene id hätten sie alle dieselbe id `rechte[]`, und jedes Label hakte die erste Box an. `HForm::label()` gibt übergebene `$attributes` jetzt im `<label>` aus.

### HButton: Escaping und Attribute (Verhaltensänderung)
`HButton` (`project`, `create`, `show`, `edit`, `delete`, `submit`, `save`, `back`) escapt jetzt wie `HForm` alle übergebenen Werte – Route, Text, Tooltip, Klasse, ID und die Werte in `$attributes` – wie Laravels `e()`. Früher wurden sie ungeprüft eingesetzt: Ein Text wie `<b>Neu</b>` erschien als Fettschrift, jetzt erscheint er als Text. Gewolltes HTML im Text oder Tooltip übergibst du als `Htmlable`, z. B. `new HtmlString(…)`; es bleibt unverändert. Die Vorgabetexte („Löschen“, „Zurück“, …) zeigen dieselben Texte wie bisher. Wer einen Text mit HTML-Entity als normalen String übergibt (`'L&ouml;schen'`), sieht jetzt `&ouml;` – solche Texte als UTF-8 (`'Löschen'`) oder als `HtmlString` übergeben.

Schlüssel in `$attributes` mit ungültigem Namen entfallen wie bei `HForm`. Ein Schlüssel, der ein von der Methode fest gesetztes Attribut doppelt (`href`, `class`, `title`, `data-toggle`, `type`, `onclick`, `data-kit-delete`), wird nicht ausgegeben – auch in anderer Schreibweise (`Class`, `TITLE`). Dasselbe gilt für alle Methoden von `HForm` (z. B. `ID` neben der fest gesetzten `id`, `for` und `class` bei `label()`); kommt ein Schlüssel in `$attributes` mehrfach in verschiedener Schreibweise vor, gilt der erste. So enthält keine Ausgabe ein Attribut doppelt. Ausnahme: `HForm::checkBox()` übernimmt die `id` aus `$attributes` in jeder Schreibweise (`id`, `ID`).

`HButton::delete()` ruft eine globale Funktion `OpenDeleteForm(id)` nur noch auf, wenn es sie gibt; ohne sie wirft der Klick keinen `ReferenceError` mehr. Der Lösch-Dialog öffnet über `data-kit-delete` (siehe „Lösch-Button und Lösch-Dialog“).

### Icons: Inline-SVG (kein Font Awesome)

Die Kit-Komponenten (`<button-edit>`, `<button-show>`, `<button-delete>`) rendert native Inline-SVGs als Icons, **nicht** Font Awesome.

- **Kein CDN-Import nötig:** Buttons sind out-of-the-box funktional
- **DSGVO-konform:** Keine externen Fonts/Icons
- **Custom-Icons:** Falls eigene Icons nötig, neue Vue-Komponente bauen (z.B. `resources/js/components/button-custom.vue`)

### MyDateTime: Zeitzone der App

`MyDateTime` nutzt die Zeitzone der App (`config('app.timezone')`) und setzt keine eigene mehr. Projekte, die Berlin-Zeit brauchen, stellen `timezone` in `config/app.php` auf `Europe/Berlin` (oder lesen dort `APP_TIMEZONE` und setzen die Variable) — ein fest eingetragenes `'UTC'` ignoriert `APP_TIMEZONE`.

### AI Developer CLI Tools (Voraussetzung für KI Agenten)
Damit die KI-Tools und die asynchrone AI-Pipeline (Hintergrund-Worker) korrekt funktionieren, müssen die entsprechenden CLI-Tools auf dem System installiert sein:

```bash
# OpenAI Codex CLI für das CodexReviewTool installieren:
sudo npm i -g @openai/codex

# Gemini CLI für die Haupt-Agent-Pipeline installieren:
npm install -g gemini-chat-cli  # (Oder das entsprechende von dir genutzte NPM-Package für Gemini)
```
*(Stelle sicher, dass im jeweiligen Laravel-Projekt in der `.env` Datei die entsprechenden Variablen wie z.B. `OPENAI_API_KEY` oder `GEMINI_API_KEY` gesetzt sind.)*

## Namespace
`ITHilbert\LaravelKit`

## Remote & MCP Integration

Das Laravel-Kit bringt gebündelte Tools mit, um das Cloud-Deployment und Remote-Debugging des verbundenen Live-Servers (insbesondere für Laravel Boost MCP) zu vereinfachen.

Trage zuerst in dem Projekt, welches dieses Kit nutzt, folgende Ziel-Parameter in die `.env` ein (Voraussetzung: Dein lokaler SSH-Key ist auf dem Zielserver hinterlegt):

```env
# MCP Live Server Dev Sync
LIVE_SERVER_HOST="123.123.123.12"
LIVE_SERVER_USER="root"
LIVE_SERVER_PATH="/var/www/meineapp"
LIVE_SERVER_PORT=22
```

Damit werden in deiner lokalen Umgebung automatisch folgende Features aktiviert:

### Interaktive Developer Commands
- `php artisan ssh:open` - Öffnet dir ein natives, interaktives SSH-Terminal das direkt in den konfigurierten Projektordner auf deinem Ziel-Linux einspringt.
- `php artisan ftp:open` - Öffnet dir blitzschnell eine interaktive SFTP-Sitzung am exakt richtigen Ablageort des Servers.

### AI-Agent Tools (MCP Server)
- **`RemoteExecuteTool`**: Verleiht deinem KI-Agenten die Macht, asynchrone Shell-Befehle (z.B. Optimierungen, Migrations) per sicherer `Process`-Facade auf dem Host auszuführen.
- **`RemoteSyncTool`**: Verleiht der KI einen gezielten Datei-Transfer Push (via natives `rsync`), um Modifikationen sofort remote live zu nehmen, ohne dass du den vollen Cloud-Deploy Button im Dashboard drücken musst!

### MyDateTime: LC_TIME und unvollständige Datumsangaben

laravel-kit setzt `LC_TIME` nicht mehr. Hosts, die eine deutsche Locale brauchen, setzen sie selbst, z. B. im `AppServiceProvider`.

Angaben, die `MyDateTime` nicht als vollständiges Datum oder Uhrzeit erkennt, erzeugen keine Warnung und keine Exception. Das Objekt ist dann leer (Typ `Nothing`, Zeitstempel 0). Dazu gehören:

- unvollständige Datumsangaben: `15.03`, `2026-03`, `15.03.`, `2026-03-`, `15.03 10:00`
- führendes Minus mit weiterem Trenner: `-03-15`, `-2026-03` (negative Zeitstempel wie `-86400` bleiben Zeitstempel)
- nicht numerische Teile: `aa.bb.cccc`, `2018-05-25T10:00`, `15:`, `aa:bb`
- Teile mit Nachkommastellen: `2018-05-25 15:25.5`
- `bool`: `true` und `false` ergeben kein Datum (bisher ergab `true` den aktuellen Zeitpunkt)

`HForm::date()` zeigt bei leerem Ergebnis den übergebenen Wert escaped unverändert an, damit die Eingabe sichtbar bleibt. Eine leere Eingabe (`''`, `null`) ergibt ein leeres Feld.

Unverändert bleiben vollständige Formate (`2018-05-25`, `25.05.2018`, mit Uhrzeit, Uhrzeit allein, `now`, `NULL`, `0000-00-00`) und Angaben mit Leerraum am Rand. Sekundenbruchteile einer Uhrzeit werden abgeschnitten. Ein Zeitstempel behält seinen Typ: `int` bleibt `int`, `float` bleibt `float`. Teile mit Leerraum oder Vorzeichen zählen als Zahl (`25. 05. 2018`, `+25.05.2018`).

### Browser-Helper: IP über `request()->ip()`

`Browser::getIP()` nutzt `request()->ip()` und beachtet damit die Trusted Proxies des Hosts. Ist keine IP ermittelbar (z. B. CLI oder Test ohne `REMOTE_ADDR`), liefert sie `'N/A'` ohne Warnung. `getBrowser()` und `getPlatform()` liefern ohne `HTTP_USER_AGENT` ebenfalls `'N/A'`.

**Upgrade-Hinweis:** Bisher wurden `Client-IP` und `X-Forwarded-For` ungeprüft übernommen. Hosts hinter einem Proxy oder Load-Balancer müssen jetzt die Trusted Proxies konfigurieren (Laravel: `trustProxies` in `bootstrap/app.php`), sonst liefert `getIP()` die Adresse des Proxys statt die des Besuchers.

## db:backup-seeders

Exportiert alle Tabellen per `iseed` (je Tabelle, `--orderby` nach Primärschlüssel bzw. erster Spalte, `--skip-fk-checks`, `--force`).
`laravelkit.backup_seeders.ignore_tables` nimmt u. a. `migrations` und `sessions` aus. Fehlt eine Tabelle im Export wegen eines iseed-Fehlers, läuft der Rest weiter und der Befehl endet mit Exit-Code 1.
Der Befehl fügt in `database/seeders/DatabaseSeeder.php` (falls vorhanden) am Anfang von `run()` eine Sperre (`// laravelkit:seed-sperre`) ein: `db:seed` bricht außerhalb von `local` und `testing` ab, da iseed-Seeder Tabellen leeren.
`install:iSeed` installiert `orangehill/iseed` als Dev-Abhängigkeit (`composer require --dev`).
