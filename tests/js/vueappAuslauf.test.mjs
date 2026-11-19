// Regressions-Test #1715, AK19: Die alten Vue-Einstiege vue/vueapp_TW.js, vue/vueapp_B5.js und
// vue/vueapp_B4.js geben beim Laden genau einmal console.warn aus (nicht bei NODE_ENV=production).
//
// Die Dateien binden per webpack `.vue`-Dateien, `vue` und `axios` ein und nutzen `require` in einem
// ES-Modul. Ohne Build laden wir sie mit einem Modul-Hook: .vue/vue/axios werden durch Attrappen ersetzt,
// `require` wird den vueapp-Dateien vorangestellt. Der Quelltext der Dateien selbst bleibt unverändert.
import assert from 'node:assert/strict';
import { register } from 'node:module';
import { afterEach, describe, mock, test } from 'node:test';

const hooks = `
const attrappe = {
  vue: 'export default class Vue { static component() {} static use() {} constructor() {} }',
  vue_datei: 'export default {}',
  axios: 'export default { defaults: { headers: { common: {} } } }',
};
export async function resolve(spec, ctx, next) {
  if (spec.endsWith('.vue')) return { url: 'attrappe:vue_datei', shortCircuit: true };
  if (spec === 'vue') return { url: 'attrappe:vue', shortCircuit: true };
  if (spec === 'axios') return { url: 'attrappe:axios', shortCircuit: true };
  return next(spec, ctx);
}
export async function load(url, ctx, next) {
  if (url.startsWith('attrappe:')) {
    return { format: 'module', source: attrappe[url.slice(9)], shortCircuit: true };
  }
  if (/\\/vue\\/vueapp_[A-Za-z0-9]+\\.js/.test(url)) {
    const vor = "const require = (n) => n === 'axios' ? { defaults: { headers: { common: {} } } } : { default: {} };\\n";
    const { readFileSync } = await import('node:fs');
    const roh = readFileSync(new URL(url.split('?')[0]), 'utf8');
    return { format: 'module', source: vor + roh, shortCircuit: true };
  }
  return next(url, ctx);
}
`;
register('data:text/javascript,' + encodeURIComponent(hooks));

globalThis.window = globalThis;
globalThis.document = globalThis.document ?? { querySelector: () => null, querySelectorAll: () => [] };

const dateien = ['vueapp_TW.js', 'vueapp_B5.js', 'vueapp_B4.js'];
const vorherEnv = process.env.NODE_ENV;
let zaehler = 0;
let warnMock;

afterEach(() => {
    warnMock?.mock.restore();
    if (vorherEnv === undefined) {
        delete process.env.NODE_ENV;
    } else {
        process.env.NODE_ENV = vorherEnv;
    }
});

async function ladenUndWarnungen(name, env) {
    process.env.NODE_ENV = env;
    warnMock = mock.method(console, 'warn', () => {});
    await import(new URL(`../../vue/${name}?lauf=${++zaehler}`, import.meta.url).href);
    return warnMock.mock.calls.map((c) => String(c.arguments[0]));
}

describe('AK19: alte Vue-Einstiege warnen beim Laden', () => {
    for (const name of dateien) {
        // AK19
        test(`${name}: genau eine Warnung außerhalb production, keine mit production`, async () => {
            const entwicklung = await ladenUndWarnungen(name, 'development');
            assert.equal(entwicklung.length, 1, `Warnungen in development: ${JSON.stringify(entwicklung)}`);
            assert.match(entwicklung[0], /^laravel-kit:/);
            assert.ok(entwicklung[0].includes('vue3/'), 'Meldung nennt vue3/');
            assert.ok(entwicklung[0].includes('mountInseln'), 'Meldung nennt mountInseln');
            warnMock.mock.restore();

            // Gegenprobe steht davor: derselbe Aufbau fängt eine Warnung, also ist „keine“ aussagekräftig.
            const produktion = await ladenUndWarnungen(name, 'production');
            assert.deepEqual(produktion, []);
        });
    }
});
