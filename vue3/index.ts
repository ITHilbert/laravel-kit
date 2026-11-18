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
import KitInputText from './components/input-text/input-text.vue'
import KitInputEmail from './components/input-email/input-email.vue'
import KitInputPassword from './components/input-password/input-password.vue'
import KitInputDate from './components/input-date/input-date.vue'
import KitInputColor from './components/input-color/input-color.vue'
import KitTextarea from './components/textarea/textarea.vue'
import KitInputNumber from './components/input-number/input-number.vue'
import KitInputInt from './components/input-int/input-int.vue'
import KitInputEuro from './components/input-euro/input-euro.vue'
import KitInputPercent from './components/input-percent/input-percent.vue'
import KitSelect from './components/select/select.vue'
import KitCheckbox from './components/checkbox/checkbox.vue'
import KitRadio from './components/radio/radio.vue'
import KitToggle from './components/toggle/toggle.vue'
import KitInputFile from './components/input-file/input-file.vue'
import KitLabel from './components/label/label.vue'
import KitForm from './components/form/form.vue'
import KitInputGroup from './components/input-group/input-group.vue'
import KitInputGroupText from './components/input-group/input-group-text.vue'

export { KitLabel, KitForm, KitInputGroup, KitInputGroupText }
export { KitInputNumber, KitInputInt, KitInputEuro, KitInputPercent, KitSelect, KitCheckbox, KitRadio, KitToggle, KitInputFile }
export { KitInputText, KitInputEmail, KitInputPassword, KitInputDate, KitInputColor, KitTextarea }
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
  'kit-input-text': KitInputText,
  'kit-input-email': KitInputEmail,
  'kit-input-password': KitInputPassword,
  'kit-input-date': KitInputDate,
  'kit-input-color': KitInputColor,
  'kit-textarea': KitTextarea,
  'kit-input-number': KitInputNumber,
  'kit-input-int': KitInputInt,
  'kit-input-euro': KitInputEuro,
  'kit-input-percent': KitInputPercent,
  'kit-select': KitSelect,
  'kit-checkbox': KitCheckbox,
  'kit-radio': KitRadio,
  'kit-toggle': KitToggle,
  'kit-input-file': KitInputFile,
  'kit-label': KitLabel,
  'kit-form': KitForm,
  'kit-input-group': KitInputGroup,
  'kit-input-group-text': KitInputGroupText,
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
