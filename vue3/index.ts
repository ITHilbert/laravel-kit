// Einstieg des Vue-3-Kerns (Entscheidung 0013 im Host packages_dev).
// - einzelne Komponenten (benannte Exporte)
// - kitKomponenten: Tag-Name → Loader für mountInseln (js/mount-inseln.js)
// - KitVue3: Plugin, app.use(KitVue3) registriert alle Komponenten unter ihrem Tag-Namen
// Neue Komponenten: importieren, einzeln exportieren und in `komponenten` eintragen.
import type { App, Component, Plugin } from 'vue'
import KitDialog from './components/dialog/dialog.vue'
import KitDialogClose from './components/dialog/close.vue'
import KitDialogTitle from './components/dialog/title.vue'
import KitDialogTrigger from './components/dialog/trigger.vue'
import KitTabs from './components/tabs/tabs.vue'
import KitTab from './components/tabs/tab.vue'
import KitTabPanel from './components/tabs/panel.vue'
import KitAccordion from './components/accordion/accordion.vue'
import KitAccordionItem from './components/accordion/item.vue'

import KitCombobox from './components/combobox/combobox.vue'
import KitSearch from './components/search/search.vue'
import KitInputToken from './components/input-token/input-token.vue'
import KitInputList from './components/input-list/input-list.vue'

export { KitCombobox, KitSearch, KitInputToken, KitInputList }
export { KitTabs, KitTab, KitTabPanel, KitAccordion, KitAccordionItem }
export { KitDialog, KitDialogClose, KitDialogTitle, KitDialogTrigger }

/** Alle Kit-Komponenten, Schlüssel = Tag-Name. */
const komponenten = {
  'kit-dialog': KitDialog,
  'kit-dialog-trigger': KitDialogTrigger,
  'kit-dialog-title': KitDialogTitle,
  'kit-dialog-close': KitDialogClose,
  'kit-tabs': KitTabs,
  'kit-tab': KitTab,
  'kit-tab-panel': KitTabPanel,
  'kit-accordion': KitAccordion,
  'kit-accordion-item': KitAccordionItem,
  'kit-combobox': KitCombobox,
  'kit-search': KitSearch,
  'kit-input-token': KitInputToken,
  'kit-input-list': KitInputList,
} satisfies Record<string, Component>

export type KitTag = keyof typeof komponenten

/** Loader in der Form, die mountInseln erwartet. */
export type KitLoader = () => Promise<{ default: Component }>

const alsLoader = (komponente: Component): KitLoader => () => Promise.resolve({ default: komponente })

export const kitKomponenten = Object.fromEntries(
  (Object.keys(komponenten) as KitTag[]).map((tag) => [tag, alsLoader(komponenten[tag])]),
) as Record<KitTag, KitLoader>

export const KitVue3: Plugin = {
  install(app: App) {
    for (const [tag, komponente] of Object.entries(komponenten)) {
      app.component(tag, komponente)
    }
  },
}
