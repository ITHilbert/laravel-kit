// Regressions-Test (Host-Befund „window.livewire is undefined“): README, Abschnitt Whole-DOM-Mount –
// „Die Dateien vue/vueapp_TW.js, vue/vueapp_B5.js und vue/vueapp_B4.js registrieren alle Vue-Komponenten
// des Kits als Komponenten-Plugin für die ganze Seite.“
//
// Das npm-Plugin `livewire-vue` ist für Livewire 2 und wirft beim Import „window.livewire is undefined“,
// sobald Livewire 3/4 (global `window.Livewire`) läuft. Der Import steht vor `new Vue(...)`, dadurch
// mountet die App nicht. Die Dateien dürfen `livewire-vue` deshalb nicht laden.
//
// Quelltext-Prüfung statt Laden des Moduls: die Dateien binden per webpack `require('./x.vue')` ein
// (.vue ist in node nicht ladbar). Kommentare werden vor der Prüfung entfernt.
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { describe, test } from 'node:test';

const dateien = ['vueapp_B4.js', 'vueapp_B5.js', 'vueapp_TW.js'];

function quelltext(name) {
    const roh = readFileSync(new URL(`../../vue/${name}`, import.meta.url), 'utf8');
    return roh.replace(/\/\*[\s\S]*?\*\//g, '').replace(/^\s*\/\/.*$/gm, '').replace(/\s\/\/.*$/gm, '');
}

describe('vueapp lädt livewire-vue nicht', () => {
    for (const name of dateien) {
        test(`${name}: kein Import/Require von livewire-vue`, () => {
            const code = quelltext(name);
            assert.ok(code.includes('Vue'), 'Gegenprobe: Quelltext nach Kommentarentfernung nicht leer');
            assert.doesNotMatch(code, /import\s+(?:[^;\n]*\sfrom\s+)?['"]livewire-vue['"]/, `${name} importiert livewire-vue`);
            assert.doesNotMatch(code, /require\(\s*['"]livewire-vue['"]\s*\)/, `${name} lädt livewire-vue per require`);
            assert.doesNotMatch(code, /import\(\s*['"]livewire-vue['"]\s*\)/, `${name} lädt livewire-vue dynamisch`);
        });
    }

    for (const name of ['vueapp_B4.js', 'vueapp_B5.js']) {
        test(`${name}: mountet weiterhin per new Vue({ el: '#vue-app' })`, () => {
            assert.match(quelltext(name), /new Vue\(\s*\{[\s\S]*el:\s*['"]#vue-app['"]/);
        });
    }

    test('vueapp_TW.js: bleibt Komponenten-Plugin mit install', () => {
        assert.match(quelltext('vueapp_TW.js'), /install/);
    });
});
