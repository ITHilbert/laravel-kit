# DataTable-Komponente (`data-table`)

Kapselt [DataTables.net](https://datatables.net/) als generische, entkoppelte Vue 3-Komponente.
Setzt den ITHilbert-Listen-Standard um: Kopfzeile → Filter-Zeile → Daten-Zeilen → Aktions-Spalte.

**Pfad:** `vue/tailwind/datatable/DataTable.vue`  
**Tag-Name (globale Registrierung):** `<data-table>`  
**Selektiver Import:** `import { DataTable } from '.../laravel-kit/vue/vueapp_TW.js'`

---

## Peer-Dependencies

Die Komponente hat **keine eigene** DataTables.net-Abhängigkeit.
Das Konsumprojekt muss DataTables.net als `window.DataTable` **oder** via `window.$.fn.DataTable` bereitstellen.

Typisch (wie im IST-Zeiten-Projekt):

```js
// resources/js/datatable.js
import $ from 'jquery';
import DataTable from 'datatables.net-dt';

window.$ = window.jQuery = $;
window.DataTable = DataTable;
```

Diese Datei muss im jeweiligen Blade-Layout per `@vite` oder `<script>` eingebunden sein, **bevor** die Vue-App gemountet wird.

---

## Props

| Prop | Typ | Default | Beschreibung |
|------|-----|---------|-------------|
| `columns` | `ColumnDefinition[]` | — | **Pflicht.** Spalten-Definitionen (siehe unten). |
| `ajaxUrl` | `string?` | — | Server-side-Modus: URL zum Yajra-DataTables-Endpoint. |
| `rows` | `Record<string,unknown>[]?` | — | Client-side-Modus: Array von Datenobjekten. |
| `actions` | `('show'\|'edit'\|'delete')[]` | `['show','edit','delete']` | Welche Default-Aktions-Buttons erscheinen. Leer-Array `[]` → keine Aktionsspalte. |
| `actionsLabel` | `string` | `'Aktionen'` | Kopfzellen-Text der Aktionsspalte. |
| `pageLength` | `number` | `25` | Zeilen pro Seite. |
| `language` | `Record<string,unknown>?` | Deutsch | DataTables `language`-Objekt (partiell oder vollständig). |
| `datatableOptions` | `Record<string,unknown>?` | — | Zusätzliche DataTables-Optionen (werden gemergt; `columns`/`ajax`/`data` intern gesetzt). |

### ColumnDefinition

```ts
interface ColumnDefinition {
    key:        string;           // Datenfeldname (z.B. "name", "created_at")
    label:      string;           // Spalten-Überschrift
    filterable?: boolean;         // Default: true — false = kein Filter-Input
    sortable?:  boolean;          // Default: true — false = nicht sortierbar
    render?:    (data, type, row) => string;  // DataTables render-Callback (client-side)
}
```

---

## Events

Die Komponente kennt **keine Routen** und keine Fachdaten. Das Konsumprojekt entscheidet, was bei einem Aktions-Click passiert.

| Event | Payload | Ausgelöst durch |
|-------|---------|-----------------|
| `@show` | `row: Record<string, unknown>` | Klick auf Anzeigen-Button |
| `@edit` | `row: Record<string, unknown>` | Klick auf Bearbeiten-Button |
| `@delete` | `row: Record<string, unknown>` | Klick auf Löschen-Button |

---

## Slots

### `#actions` (scoped, überschreibt Default-Buttons vollständig)

```html
<data-table :columns="cols" :rows="data">
    <template #actions="{ row }">
        <a :href="`/kunden/${row.id}`" class="btn btn-show" title="Anzeigen">
            <i class="fas fa-file-contract"></i>
        </a>
    </template>
</data-table>
```

> **Hinweis:** Wegen der DataTables.net-Architektur (render-Callbacks außerhalb des Vue-Reaktivitätssystems) werden Slot-Inhalte nicht automatisch in DataTables-Zellen gerendert. Der `#actions`-Slot ist für alternative Renderer oder Custom-Implementierungen gedacht. Für den Standardfall die Events nutzen.

---

## Öffentliche Methoden (`ref`-Zugriff)

```ts
const dtRef = ref();

// DataTables-Instanz holen (für erweiterte API-Calls)
dtRef.value.getDtInstance();

// Neu zeichnen
dtRef.value.redraw();

// Ajax-Endpoint neu laden (server-side)
dtRef.value.reload(resetPaging = true);
```

---

## Beispiel: Client-side

```html
<script setup>
import { ref } from 'vue';

const columns = [
    { key: 'name',       label: 'Name' },
    { key: 'email',      label: 'E-Mail' },
    { key: 'created_at', label: 'Erstellt am', filterable: false },
];

const rows = ref([
    { id: 1, name: 'Max Mustermann', email: 'max@example.com', created_at: '2025-01-01' },
    { id: 2, name: 'Erika Muster',   email: 'erika@example.com', created_at: '2025-02-15' },
]);

function onEdit(row) {
    window.location.href = `/kunden/${row.id}/bearbeiten`;
}

function onDelete(row) {
    if (confirm(`${row.name} wirklich löschen?`)) {
        // Ajax-Call oder Form-Submit
    }
}
</script>

<template>
    <data-table
        :columns="columns"
        :rows="rows"
        :actions="['edit', 'delete']"
        :page-length="10"
        @edit="onEdit"
        @delete="onDelete"
    />
</template>
```

---

## Beispiel: Server-side (Yajra DataTables)

```html
<template>
    <data-table
        :columns="columns"
        ajax-url="/api/kunden/datatable"
        @show="row => $router.push(`/kunden/${row.id}`)"
        @edit="row => $router.push(`/kunden/${row.id}/edit`)"
        @delete="row => handleDelete(row)"
    />
</template>

<script setup>
const columns = [
    { key: 'name',  label: 'Name' },
    { key: 'email', label: 'E-Mail' },
    { key: 'stadt', label: 'Stadt' },
];
</script>
```

Server-seitiger Laravel-Controller (Yajra):

```php
use Yajra\DataTables\Facades\DataTables;

public function datatable(): JsonResponse
{
    $query = Kunde::query()->select(['id', 'name', 'email', 'stadt']);

    return DataTables::eloquent($query)->toJson();
}
```

---

## ITHilbert-Listen-Konvention

Die Komponente erzwingt automatisch:

1. **Zeile 1 (Kopfzeile):** Spalten-Labels in `bg-gray-100`, Uppercase, dezentes Styling.
2. **Zeile 2 (Filter-Zeile):** Pro Spalte mit `filterable !== false` ein Input-Feld. Spalten-individuelle Filter triggern DataTables-Column-Search.
3. **Letzte Spalte (Aktionen):** Show / Edit / Delete Buttons mit identischen CSS-Klassen (`btn btn-show`, `btn btn-edit`, `btn btn-delete`) wie die eigenständigen Button-Komponenten (`button-show.vue`, `button-edit.vue`, `button-delete.vue`).
4. **Mobile-First:** `overflow-x-auto`-Container, DataTables `responsive: true`.

---

## Annahmen / Grenzen

- jQuery und DataTables.net werden als **globale Peer-Dependencies** vom Konsumprojekt bereitgestellt (`window.$` / `window.DataTable`). Das Package selbst importiert beides nicht.
- Vue Scoped Slots und DataTables.net-`render`-Callbacks leben in getrennten Rendering-Kontexten. Komplexes Vue-Markup in Zellen erfordert entweder den `render`-Callback (für einfaches HTML) oder einen separaten Mount-Punkt.
- Die Komponente hat kein eigenständiges CSS-Stylesheet. Alle Styles kommen aus Tailwind-Utility-Klassen im Template und aus den globalen `btn`-Klassen des Projekts.
