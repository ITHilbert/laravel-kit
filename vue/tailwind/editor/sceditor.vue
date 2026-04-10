<template>
    <textarea 
        :id="id" 
        :name="name" 
        :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 text-area', addClass]" 
        :rows="rows" 
        v-model="currentValue" 
        v-bind="$attrs"
    ><slot></slot></textarea>
</template>

<script setup lang="ts">
import { ref, onMounted, useSlots, onBeforeMount } from 'vue';

const props = withDefaults(defineProps<{
    rows?: number | string;
    value?: string;
    addClass?: string;
    name?: string;
    id?: string;
}>(), {
    rows: 3,
    value: '',
    addClass: '',
    name: '',
    id: 'sceditorID'
});

defineOptions({ inheritAttrs: false });

const slots = useSlots();
const currentValue = ref(getDefaultValue());
const scriptsLoaded = ref(false);

function getDefaultValue() {
    if (slots.default) {
        const slotContent = slots.default();
        if (slotContent && slotContent.length > 0 && typeof slotContent[0].children === 'string') {
            return slotContent[0].children;
        }
    }
    return props.value;
}

onBeforeMount(() => {
    if (typeof document === 'undefined') return;

    if (!document.querySelector('script[src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"]')) {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js';
        document.body.appendChild(script);

        const styleLink = document.createElement('link');
        styleLink.rel = 'stylesheet';
        styleLink.href = 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css';
        document.head.appendChild(styleLink);

        const xhtmlScript = document.createElement('script');
        xhtmlScript.src = 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js';
        document.body.appendChild(xhtmlScript);

        const formatScript = document.createElement('script');
        formatScript.src = 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/plugins/format.js';
        document.body.appendChild(formatScript);

        const undoScript = document.createElement('script');
        undoScript.src = 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/plugins/undo.js';
        document.body.appendChild(undoScript);

        const plainScript = document.createElement('script');
        plainScript.src = 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/plugins/plaintext.js';
        document.body.appendChild(plainScript);
    }

    const checkScriptsLoaded = () => {
        // @ts-ignore
        if (typeof sceditor !== 'undefined') {
            scriptsLoaded.value = true;
        } else {
            setTimeout(checkScriptsLoaded, 100);
        }
    };
    checkScriptsLoaded();
});

onMounted(() => {
    if (typeof window === 'undefined') return;

    const initEditor = () => {
        // @ts-ignore
        if (typeof sceditor === 'undefined') {
            setTimeout(initEditor, 100);
            return;
        }
        
        // @ts-ignore
        if (typeof $ === 'undefined' && typeof window.jQuery === 'undefined') {
            console.warn('jQuery ist nicht definiert. Custom-Headers für SCEditor werden ignoriert.');
        }

        // @ts-ignore
        sceditor.plugins['customformat'] = function () {
            var formats = {
                h1: { start: '<h1>', end: '</h1>', txtExec: ['<h1>', '</h1>'] },
                h2: { start: '<h2>', end: '</h2>', txtExec: ['<h2>', '</h2>'] }
            };
        };

        // @ts-ignore
        sceditor.locale['de'] = {
            'Bold': 'Fett',
            'Italic': 'Kursiv',
            'Underline': 'Unterstrichen',
            'Strikethrough': 'Durchgestrichen',
            'Subscript': 'Tiefgestellt',
            'Superscript': 'Hochgestellt',
            'Align left': 'Linksbündig ausrichten',
            'Center': 'Zentrieren',
            'Align right': 'Rechtsbündig ausrichten',
            'Justify': 'Blocksatz',
            'Font Name': 'Schriftname',
            'Font Size': 'Schriftgröße',
            'Font Color': 'Schriftfarbe',
            'Remove Formatting': 'Formatierung entfernen',
            'Cut': 'Ausschneiden',
            'Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X': 'Ihr Browser erlaubt das Ausschneiden von Text nicht, bitte Nutzen Sie das Tastenkürzel Strg / Cmd-X',
            'Copy': 'Kopieren',
            'Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C': 'Ihr Browser erlaubt das Kopieren von Text nicht, bitte Nutzen Sie das Tastenkürzel Strg / Cmd-C',
            'Paste': 'Einfügen',
            'Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V': 'Ihr Browser erlaubt das Einfügen von Text nicht, bitte Nutzen Sie das Tastenkürzel Strg / Cmd-V',
            'Paste your text inside the following box:': 'Fügen Sie Ihren Text in die folgende Box ein',
            'Paste Text': 'Text einfügen',
            'Bullet list': 'Aufzählungsliste',
            'Numbered list': 'Nummerierte Liste',
            'Add indent': 'Ebene hinzufügen',
            'Remove one indent': 'Eine Ebene entfernen',
            'Undo': 'Rückgängig machen',
            'Redo': 'Wiederherstellen',
            'Rows:': 'Zeilen',
            'Cols:': 'Spalten',
            'Insert a table': 'Tabelle einfügen', // changed to default wording as in original code there was a tailwind class injected
            'Insert a horizontal rule': 'Horizontale Linie einfügen',
            'Code': 'Code',
            'Insert a Quote': 'Zitat einfügen',
            'Width (optional):': 'Breite (Optional)',
            'Height (optional):': 'Höhe (Optional)',
            'Insert an image': 'Ein Bild einfügen',
            'E-mail:': 'E-Mail',
            'Insert an email': 'E-Mail einfügen',
            'URL:': 'URL',
            'Insert a link': 'Link einfügen',
            'Unlink': 'Link entfernen',
            'More': 'Mehr',
            'Left-to-Right': 'Links nach rechts',
            'Right-to-Left': 'Rechts nach links',
            'Insert an emoticon': 'Emoticon einfügen',
            'Video URL:': 'Video URL',
            'Insert': 'Einfügen',
            'Insert a YouTube video': 'YouTube Video einfügen',
            'Insert current date': 'Aktuelles Datum einfügen',
            'Insert current time': 'Aktuelle Uhrzeit einfügen',
            'Print': 'Drucken',
            'Maximize': 'Maximieren',
            'View source': 'Quelltext ansehen',
            dateFormat: 'day.month.year'
        };

        // @ts-ignore
        if (typeof window.$ !== 'undefined' || typeof window.jQuery !== 'undefined') {
            // @ts-ignore
            const jq = window.$ || window.jQuery;
            // @ts-ignore
            sceditor.command.set("headers", {
                exec: function(caller: any) {
                    var editor = this;
                    var $content = jq("<div />");

                    for (var i = 1; i <= 6; i++) {
                        jq(
                            '<a class="sceditor-header-option" href="#">' +
                                '<h' + i + '>Überschrift ' + i + '</h' + i + '>' +
                            '</a>'
                        )
                        .data('headersize', i)
                        .click(function (e: any) {
                            var selection = editor.getRangeHelper().selectedHtml();

                            if (selection) {
                                var header = '<h' + jq(this).data('headersize') + '>';
                                var formattedText = header + selection + '</h' + jq(this).data('headersize') + '>';

                                editor.insert(formattedText);
                            }

                            editor.closeDropDown(true);
                            e.preventDefault();
                        })
                        .appendTo($content);
                    }

                    editor.createDropDown(caller, "header-picker", $content.get(0));
                },
                tooltip: "Überschriften formatieren"
            });
        }

        const textarea = document.getElementById(props.id);
        if(textarea) {
            // @ts-ignore
            sceditor.create(textarea, {
                format: 'xhtml',
                style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css',
                toolbar: 'headers|bold,italic,underline,strike,subscript,superscript|left,center,right,justify|font,size,color,removeformat|bulletlist,orderedlist|table|quote|horizontalrule|image|link,unlink|date,time|ltr,rtl|print,maximize|source|h1,h2',
                emoticonsEnabled: false,
                emoticonsCompat: false,
                plugins: 'plaintext,undo,format',
                pastetext: {
                    addButton: true,
                    enabled: false
                },
            });
        }
    };

    if (document.readyState === 'complete') {
        initEditor();
    } else {
        window.addEventListener('load', initEditor);
    }
});
</script>

<style>
.sceditor-button-headers div[unselectable="on"] {
    background: url('/vendor/pages/headers-button.png') !important;
}
.sceditor-header-option {
    display: block;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    color: #222;
}
.sceditor-header-option:hover { background: #eee; }
</style>
