# Laravel-Kit Vue.js Komponenten

Willkommen im zentralen Interface-Repository des `laravel-kit`. Hier liegen alle global wiederverwendbaren Vue 3 Systemkomponenten, die in unseren Laravel-Projekten genutzt werden.

Dieses Verzeichnis unterliegt strengen strukturellen und architektonischen Richtlinien, um das Theming, die Performance (Tree-Shaking) und die projektübergreifende Portabilität zu maximieren.

---

## 1. Unsere Architektur-Prinzipien

Alle Komponenten in diesem Verzeichnis folgen strikt diesen drei Säulen:

1. **Vue 3 Composition API:** Alle Komponenten sind mit `<script setup lang="ts">` geschrieben und voll mit TypeScript-Typisierung (z.B. über `defineProps<{ ... }>()`) unterbaut.
2. **Tailwind CSS v4 (Nesting & CSS):** Wir verzichten vollständig auf SCSS-Abhängigkeiten (`sass` ist nicht notwendig) und nutzen nativ das Tailwind v4 CSS-Nesting.
3. **Der 2-Klassen Semantic-Layer:** Die Komponenten sind kontextfrei und unabhängig vom übergeordneten Layout des Projekts. Sie positionieren sich nicht selbst im Grid!

---

## 2. Der Semantic-Layer (Warum keine Utilities im Template?)

Um eine UI-Komponente wie einen Button oder ein Eingabefeld global für alle denkbaren Kunden-Projekte wiederverwendbar zu machen, dürfen im `<template>` der Vue-Datei **keine festcodierten (layout- oder thementreibenden) Tailwind-Utility-Klassen** stehen.

Stattdessen nutzen wir zwei semantische Klassen:

1. **Basis-Klasse:** z.B. `btn` (Für strukturelle Grundeigenschaften wie Paddings, Borders, Transitions)
2. **Varianten-Klasse:** z.B. `btn-cancel` (Für spezifische Farben, Interaktionszustände)

### So sieht es in der Vue-Datei aus:
```html
<!-- vue/tailwind/buttons/button-cancel.vue -->
<template>
  <button type="button" class="btn btn-cancel" v-bind="$attrs">
    <slot></slot>
  </button>
</template>
```

### So sieht es in der zugehörigen CSS-Datei aus:
Die Umsetzung der Klassen erfolgt dann rein in den CSS-Dateien via `@apply`.

```css
/* button-cancel.css */
.btn-cancel {
    @apply bg-red-100 text-red-700 border-red-200;
    
    /* Tailwind v4 macht es möglich: Nativ CSS Nesting */
    &:hover {
        @apply bg-red-200;
    }
}
```

Dadurch bleibt das Projekt flexibel. Möchte ein Kundenprojekt, dass der Cancel-Button plötzlich lila mit Schatten ist, reicht es deren lokale `app.css` anzupassen oder nur die Token in Tailwind anzupassen – die Vue-Struktur bleibt unangetastet.

---

## 3. Die CSS-Barrel-Architektur

Damit ein Projekt nicht Dutzende Mini-CSS-Dateien (z.B. `button-cancel.css`, `button-submit.css`) manuell im Root importieren muss, gibt es pro UI-Kategorie sogenannte **Barrel-Dateien**. 

Beispiel: Die Datei `buttons/buttons.css`.
Sie definiert zuerst unseren gemeinsamen Basis-Style (DRY) und importiert dann sauber gekapselt alle abgeleiteten Varianten:

```css
/* buttons.css (Barrel) */

/* 1. Die gemeinsame Basis */
.btn {
    @apply inline-flex items-center px-4 py-2 border rounded-md shadow-sm transition-all duration-200;
}

/* 2. Alle Kind-Derivate (Varianten) */
@import './button-cancel.css';
@import './button-create.css';
@import './button-submit.css';
/* ... */
```
Das Hauptprojekt importiert dann in seiner `resources/css/custom.css` auschliesslich diese `buttons.css`.

---

## 4. Nutzung im Parent-View (Struktur vs. Kontext)

Aufgabe einer UI-Abstraktion (wie in diesem Repo) ist das Definieren des Aussehens. Aufgabe des integrierenden Laravel-Blade oder Wrapper-Vue-Templates ist die Positionierung (**Kontext**).

1. Die Vue-Komponente platziert sich nicht im Layout (`mt-4`, `w-full` etc. sind **verboten**).
2. Der umschließende Parent füttert diese Werte über `$attrs` in die Komponente ein.

**Beispiel für den korrekten Aufruf in Blade:**
```html
<form>
    <!-- Marge und Gesamtbreite werden extern definiert -->
    <input-text name="email" class="mt-4 w-full md:w-1/2"></input-text>
</form>
```

---

## 5. Registrierung der Komponenten

Alle hier erstellten Komponenten werden über eine globale Auto-Katalogisierung zur Benutzung in Laravel freigeschaltet.

Unsere Master-Plugin-Datei `vueapp_TW.js` dient hierbei als Import-Verteiler.
Jede neue `.vue`-Komponente muss hier als benannter (`export { Component }`) sowie als iterierbarer (`app.component(...)`) Export für die Laufzeit hinterlegt werden.
