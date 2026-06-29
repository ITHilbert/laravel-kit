# ITHilbert Laravel Kit

**Beschreibung**: Tools für das Programmieren mit Laravel.

## Dokumentation

Die ausführliche Dokumentation befindet sich im Ordner `docs/`:
- [Kontext & Zielsetzung](docs/00_README_Kontext.md)
- [Architekturübersicht](docs/01_Architekturübersicht.md)
- [Modulstruktur](docs/07_Modulstruktur.md)
- [API-Definitionen](docs/02_API-Definitionen.md)
- [KI-Zusammenarbeit & Autonome Pipeline](docs/ki-zusammenarbeit-architektur.md)

### Vue-Komponenten (Tailwind)
- [DataTable-Komponente](docs/vue-datatable.md) — generische DataTables.net-Kapselung mit Filter-Zeile, Aktions-Spalte und Server-/Client-side Modus

## Installation

```bash
composer require ithilbert/laravel-kit
```

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

### Icons: Inline-SVG (kein Font Awesome)

Die Kit-Komponenten (`<button-edit>`, `<button-show>`, `<button-delete>`) rendert native Inline-SVGs als Icons, **nicht** Font Awesome.

- **Kein CDN-Import nötig:** Buttons sind out-of-the-box funktional
- **DSGVO-konform:** Keine externen Fonts/Icons
- **Custom-Icons:** Falls eigene Icons nötig, neue Vue-Komponente bauen (z.B. `resources/js/components/button-custom.vue`)

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
