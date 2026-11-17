// Typen für den Import von Einzeldatei-Komponenten (*.vue) aus TypeScript.
declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const komponente: DefineComponent<object, object, unknown>
  export default komponente
}
