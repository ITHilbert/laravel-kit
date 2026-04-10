/**
 * laravel-kit Vue 3 Komponenten-Plugin (Tailwind)
 *
 * Verwendung im Projekt:
 *   import LaravelKit from '.../laravel-kit/vue/vueapp_TW.js';
 *   app.use(LaravelKit);
 *
 * Oder selektiv:
 *   import { ButtonCancel, ButtonSave } from '.../laravel-kit/vue/vueapp_TW.js';
 *   app.component('button-cancel', ButtonCancel);
 */

// === Importe ===
import AccordionBody from './tailwind/accordion/accordion-body.vue';
import AccordionHeader from './tailwind/accordion/accordion-header.vue';
import AccordionItem from './tailwind/accordion/accordion-item.vue';
import Accordion from './tailwind/accordion/accordion.vue';
import BreadcrumbItem from './tailwind/breadcrumb/breadcrumb-item.vue';
import Breadcrumb from './tailwind/breadcrumb/breadcrumb.vue';
import HButton from './tailwind/buttons/HButton.vue';
import ButtonBack from './tailwind/buttons/button-back.vue';
import ButtonCancel from './tailwind/buttons/button-cancel.vue';
import ButtonConfirm from './tailwind/buttons/button-confirm.vue';
import ButtonCreate from './tailwind/buttons/button-create.vue';
import ButtonDelete from './tailwind/buttons/button-delete.vue';
import ButtonEdit from './tailwind/buttons/button-edit.vue';
import ButtonReset from './tailwind/buttons/button-reset.vue';
import ButtonSave from './tailwind/buttons/button-save.vue';
import ButtonShow from './tailwind/buttons/button-show.vue';
import ButtonSubmit from './tailwind/buttons/button-submit.vue';
import Hbutton from './tailwind/buttons/hbutton.vue';
import CardBody from './tailwind/card/card-body.vue';
import CardBottom from './tailwind/card/card-bottom.vue';
import CardColumns from './tailwind/card/card-columns.vue';
import CardDeck from './tailwind/card/card-deck.vue';
import CardFooter from './tailwind/card/card-footer.vue';
import CardGroup from './tailwind/card/card-group.vue';
import CardHeader from './tailwind/card/card-header.vue';
import CardImgBottom from './tailwind/card/card-img-bottom.vue';
import CardImgTop from './tailwind/card/card-img-top.vue';
import CardMainHeader from './tailwind/card/card-main-header.vue';
import CardMain from './tailwind/card/card-main.vue';
import CardText from './tailwind/card/card-text.vue';
import CardTitle from './tailwind/card/card-title.vue';
import Card from './tailwind/card/card.vue';
import JCard from './tailwind/card/jCard.vue';
import Delete from './tailwind/dialog/delete.vue';
import Sceditor from './tailwind/editor/sceditor.vue';
import Tinymce from './tailwind/editor/tinymce.vue';
import Checkbox from './tailwind/forms/checkbox.vue';
import Combobox from './tailwind/forms/combobox.vue';
import Hform from './tailwind/forms/hform.vue';
import Hlabel from './tailwind/forms/hlabel.vue';
import InputColor from './tailwind/forms/input-color.vue';
import InputDate from './tailwind/forms/input-date.vue';
import InputEmail from './tailwind/forms/input-email.vue';
import InputEuro from './tailwind/forms/input-euro.vue';
import InputFileImg from './tailwind/forms/input-file-img.vue';
import InputFile from './tailwind/forms/input-file.vue';
import InputGroupText from './tailwind/forms/input-group-text.vue';
import InputGroup from './tailwind/forms/input-group.vue';
import InputHidden from './tailwind/forms/input-hidden.vue';
import InputInt from './tailwind/forms/input-int.vue';
import InputList from './tailwind/forms/input-list.vue';
import InputNumber from './tailwind/forms/input-number.vue';
import InputPassword from './tailwind/forms/input-password.vue';
import InputPercent from './tailwind/forms/input-percent.vue';
import InputText from './tailwind/forms/input-text.vue';
import InputToken from './tailwind/forms/input-token.vue';
import Radiobox from './tailwind/forms/radiobox.vue';
import Search from './tailwind/forms/search.vue';
import TextArea from './tailwind/forms/text-area.vue';
import Toggle from './tailwind/forms/toggle.vue';
import VueSelectItem from './tailwind/forms/vue-select-item.vue';
import VueSelect from './tailwind/forms/vue-select.vue';
import Col1 from './tailwind/grid/col-1.vue';
import Col10 from './tailwind/grid/col-10.vue';
import Col11 from './tailwind/grid/col-11.vue';
import Col12 from './tailwind/grid/col-12.vue';
import Col2 from './tailwind/grid/col-2.vue';
import Col3 from './tailwind/grid/col-3.vue';
import Col4 from './tailwind/grid/col-4.vue';
import Col5 from './tailwind/grid/col-5.vue';
import Col6 from './tailwind/grid/col-6.vue';
import Col7 from './tailwind/grid/col-7.vue';
import Col8 from './tailwind/grid/col-8.vue';
import Col9 from './tailwind/grid/col-9.vue';
import Row from './tailwind/grid/row.vue';
import Group from './tailwind/group.vue';
import Checkbox from './tailwind/input/checkbox.vue';
import Combobox from './tailwind/input/combobox.vue';
import Combobox2 from './tailwind/input/combobox2.vue';
import Combobox4 from './tailwind/input/combobox4.vue';
import Combobox_1 from './tailwind/input/combobox_1.vue';
import Hlabel from './tailwind/input/hlabel.vue';
import InputColor from './tailwind/input/input-color.vue';
import InputDate from './tailwind/input/input-date.vue';
import InputEmail from './tailwind/input/input-email.vue';
import InputEuro from './tailwind/input/input-euro.vue';
import InputFileImg from './tailwind/input/input-file-img.vue';
import InputFile from './tailwind/input/input-file.vue';
import InputHidden from './tailwind/input/input-hidden.vue';
import InputInt from './tailwind/input/input-int.vue';
import InputList from './tailwind/input/input-list.vue';
import InputNumber from './tailwind/input/input-number.vue';
import InputPassword from './tailwind/input/input-password.vue';
import InputPercent from './tailwind/input/input-percent.vue';
import InputText from './tailwind/input/input-text.vue';
import Radiobox from './tailwind/input/radiobox.vue';
import Search from './tailwind/input/search.vue';
import TextArea from './tailwind/input/text-area.vue';
import ModalBody from './tailwind/modal/modal-body.vue';
import ModalButtonClose from './tailwind/modal/modal-button-close.vue';
import ModalFooter from './tailwind/modal/modal-footer.vue';
import ModalHeader from './tailwind/modal/modal-header.vue';
import ModalOpenButton from './tailwind/modal/modal-open-button.vue';
import Modal from './tailwind/modal/modal.vue';
import NavGroup from './tailwind/nav/nav-group.vue';
import NavItem from './tailwind/nav/nav-item.vue';
import Nav from './tailwind/nav/nav.vue';
import Code from './tailwind/show/code.vue';
import MarkdownViewer from './tailwind/show/markdown-viewer.vue';
import ShowDate from './tailwind/show/show-date.vue';
import ShowEuro from './tailwind/show/show-euro.vue';
import ShowText from './tailwind/show/show-text.vue';
import SidebarChildItem from './tailwind/sidebar/sidebar-child-item.vue';
import SidebarGroup from './tailwind/sidebar/sidebar-group.vue';
import SidebarHeader from './tailwind/sidebar/sidebar-header.vue';
import SidebarItem from './tailwind/sidebar/sidebar-item.vue';
import SidebarLine from './tailwind/sidebar/sidebar-line.vue';
import SidebarParentItem from './tailwind/sidebar/sidebar-parent-item.vue';
import Sidebar from './tailwind/sidebar/sidebar.vue';
import Pen from './tailwind/svg/pen.vue';
import Star from './tailwind/svg/star.vue';
import TabBody from './tailwind/tabs/tab-body.vue';
import TabHeader from './tailwind/tabs/tab-header.vue';
import TabsBody from './tailwind/tabs/tabs-body.vue';
import TabsHeader from './tailwind/tabs/tabs-header.vue';
import Rating from './tailwind/view/rating.vue';
import HtmlCodeViewer from './tailwind/viewer/html-code-viewer.vue';

// === Alle Komponenten als benannte Exporte (für selektive Nutzung) ===
export {
    AccordionBody,
    AccordionHeader,
    AccordionItem,
    Accordion,
    BreadcrumbItem,
    Breadcrumb,
    HButton,
    ButtonBack,
    ButtonCancel,
    ButtonConfirm,
    ButtonCreate,
    ButtonDelete,
    ButtonEdit,
    ButtonReset,
    ButtonSave,
    ButtonShow,
    ButtonSubmit,
    Hbutton,
    CardBody,
    CardBottom,
    CardColumns,
    CardDeck,
    CardFooter,
    CardGroup,
    CardHeader,
    CardImgBottom,
    CardImgTop,
    CardMainHeader,
    CardMain,
    CardText,
    CardTitle,
    Card,
    JCard,
    Delete,
    Sceditor,
    Tinymce,
    Checkbox,
    Combobox,
    Hform,
    Hlabel,
    InputColor,
    InputDate,
    InputEmail,
    InputEuro,
    InputFileImg,
    InputFile,
    InputGroupText,
    InputGroup,
    InputHidden,
    InputInt,
    InputList,
    InputNumber,
    InputPassword,
    InputPercent,
    InputText,
    InputToken,
    Radiobox,
    Search,
    TextArea,
    Toggle,
    VueSelectItem,
    VueSelect,
    Col1,
    Col10,
    Col11,
    Col12,
    Col2,
    Col3,
    Col4,
    Col5,
    Col6,
    Col7,
    Col8,
    Col9,
    Row,
    Group,
    Checkbox,
    Combobox,
    Combobox2,
    Combobox4,
    Combobox_1,
    Hlabel,
    InputColor,
    InputDate,
    InputEmail,
    InputEuro,
    InputFileImg,
    InputFile,
    InputHidden,
    InputInt,
    InputList,
    InputNumber,
    InputPassword,
    InputPercent,
    InputText,
    Radiobox,
    Search,
    TextArea,
    ModalBody,
    ModalButtonClose,
    ModalFooter,
    ModalHeader,
    ModalOpenButton,
    Modal,
    NavGroup,
    NavItem,
    Nav,
    Code,
    MarkdownViewer,
    ShowDate,
    ShowEuro,
    ShowText,
    SidebarChildItem,
    SidebarGroup,
    SidebarHeader,
    SidebarItem,
    SidebarLine,
    SidebarParentItem,
    Sidebar,
    Pen,
    Star,
    TabBody,
    TabHeader,
    TabsBody,
    TabsHeader,
    Rating,
    HtmlCodeViewer,
};

// === Plugin-Export (für app.use()) ===
const components = {
    'accordion-body': AccordionBody,
    'accordion-header': AccordionHeader,
    'accordion-item': AccordionItem,
    'accordion': Accordion,
    'breadcrumb-item': BreadcrumbItem,
    'breadcrumb': Breadcrumb,
    'hbutton': HButton,
    'button-back': ButtonBack,
    'button-cancel': ButtonCancel,
    'button-confirm': ButtonConfirm,
    'button-create': ButtonCreate,
    'button-delete': ButtonDelete,
    'button-edit': ButtonEdit,
    'button-reset': ButtonReset,
    'button-save': ButtonSave,
    'button-show': ButtonShow,
    'button-submit': ButtonSubmit,
    'hbutton': Hbutton,
    'card-body': CardBody,
    'card-bottom': CardBottom,
    'card-columns': CardColumns,
    'card-deck': CardDeck,
    'card-footer': CardFooter,
    'card-group': CardGroup,
    'card-header': CardHeader,
    'card-img-bottom': CardImgBottom,
    'card-img-top': CardImgTop,
    'card-main-header': CardMainHeader,
    'card-main': CardMain,
    'card-text': CardText,
    'card-title': CardTitle,
    'card': Card,
    'jcard': JCard,
    'delete': Delete,
    'sceditor': Sceditor,
    'tinymce': Tinymce,
    'checkbox': Checkbox,
    'combobox': Combobox,
    'hform': Hform,
    'hlabel': Hlabel,
    'input-color': InputColor,
    'input-date': InputDate,
    'input-email': InputEmail,
    'input-euro': InputEuro,
    'input-file-img': InputFileImg,
    'input-file': InputFile,
    'input-group-text': InputGroupText,
    'input-group': InputGroup,
    'input-hidden': InputHidden,
    'input-int': InputInt,
    'input-list': InputList,
    'input-number': InputNumber,
    'input-password': InputPassword,
    'input-percent': InputPercent,
    'input-text': InputText,
    'input-token': InputToken,
    'radiobox': Radiobox,
    'search': Search,
    'text-area': TextArea,
    'toggle': Toggle,
    'vue-select-item': VueSelectItem,
    'vue-select': VueSelect,
    'col-1': Col1,
    'col-10': Col10,
    'col-11': Col11,
    'col-12': Col12,
    'col-2': Col2,
    'col-3': Col3,
    'col-4': Col4,
    'col-5': Col5,
    'col-6': Col6,
    'col-7': Col7,
    'col-8': Col8,
    'col-9': Col9,
    'row': Row,
    'group': Group,
    'checkbox': Checkbox,
    'combobox': Combobox,
    'combobox2': Combobox2,
    'combobox4': Combobox4,
    'combobox_1': Combobox_1,
    'hlabel': Hlabel,
    'input-color': InputColor,
    'input-date': InputDate,
    'input-email': InputEmail,
    'input-euro': InputEuro,
    'input-file-img': InputFileImg,
    'input-file': InputFile,
    'input-hidden': InputHidden,
    'input-int': InputInt,
    'input-list': InputList,
    'input-number': InputNumber,
    'input-password': InputPassword,
    'input-percent': InputPercent,
    'input-text': InputText,
    'radiobox': Radiobox,
    'search': Search,
    'text-area': TextArea,
    'modal-body': ModalBody,
    'modal-button-close': ModalButtonClose,
    'modal-footer': ModalFooter,
    'modal-header': ModalHeader,
    'modal-open-button': ModalOpenButton,
    'modal': Modal,
    'nav-group': NavGroup,
    'nav-item': NavItem,
    'nav': Nav,
    'code': Code,
    'markdown-viewer': MarkdownViewer,
    'show-date': ShowDate,
    'show-euro': ShowEuro,
    'show-text': ShowText,
    'sidebar-child-item': SidebarChildItem,
    'sidebar-group': SidebarGroup,
    'sidebar-header': SidebarHeader,
    'sidebar-item': SidebarItem,
    'sidebar-line': SidebarLine,
    'sidebar-parent-item': SidebarParentItem,
    'sidebar': Sidebar,
    'pen': Pen,
    'star': Star,
    'tab-body': TabBody,
    'tab-header': TabHeader,
    'tabs-body': TabsBody,
    'tabs-header': TabsHeader,
    'rating': Rating,
    'html-code-viewer': HtmlCodeViewer,
};

export default {
    install(app) {
        Object.entries(components).forEach(([name, component]) => {
            app.component(name, component);
        });
    },
};
