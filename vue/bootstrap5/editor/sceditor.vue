<!-- SCEditor => https://www.sceditor.com/
Aufruf: <html-editor name="content" value="{{ old('content', $page->content) }}" :rows="30" @error('content') class="is-invalid" @enderror></html-editor>
Wichtig: Wert per Value übergeben. Direkt an den Slot funktioniert nicht.
-->
<template>
    <textarea id="sceditorID" :name="name" :class="['form-control text-area', $attrs.class, addClass]" :rows="rows" v-model="currentValue" v-bind="$attrs"><slot></slot></textarea>
</template>

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

  <script>
  export default {
    props: {
            rows: {
                type: Number,
                default: '3'
            },
            value: {
                required: false,
                type: String,
                default: '',
            },
            addClass: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: ''
            },
        },
        data() {
            return {
                currentValue: this.getDefaultValue(),
                scriptsLoaded: false // Flag-Variable für den Ladezustand der Skripte
            }
        },

        methods: {
            getDefaultValue() {
                if (this.$slots.default && this.$slots.default.length) {
                    return this.$slots.default[0].text; // innerHTML
                }

                return this.value
            },
            host(){
                if (!window.location.origin) {
                    return window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port: '');
                }else{
                    return window.location.origin
                }
            }
        },
        beforeMount() {
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


            // Überprüfen, ob die Skripte vollständig geladen sind
            const checkScriptsLoaded = () => {
            if (typeof sceditor !== 'undefined') {
                this.scriptsLoaded = true; // Setzen Sie die Flag-Variable auf true, wenn die Skripte geladen sind
                //this.initializeEditor(); // Rufen Sie die Methode auf, um den Editor zu initialisieren
            } else {
                setTimeout(checkScriptsLoaded, 100);
            }
            };

            checkScriptsLoaded();
        },
        mounted() {
            window.onload = function() {
                sceditor.plugins['customformat'] = function () {
                    var base = this;

                    var formats = {
                        h1: {
                            start: '<h1>',
                            end: '</h1>',
                            txtExec: ['<h1>', '</h1>']
                        },
                        h2: {
                            start: '<h2>',
                            end: '</h2>',
                            txtExec: ['<h2>', '</h2>']
                        }
                        // Füge hier weitere Formate hinzu, falls benötigt
                    };
                };

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
                    'Insert a table': 'Tabelle einfügen',
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

                sceditor.command.set("headers", {
                    exec: function(caller) {
                        var editor = this;
                        var $content = $("<div />");

                        for (var i = 1; i <= 6; i++) {
                            $(
                                '<a class="sceditor-header-option" href="#">' +
                                    '<h' + i + '>Überschrift ' + i + '</h' + i + '>' +
                                '</a>'
                            )
                            .data('headersize', i)
                            .click(function (e) {
                                var selection = editor.getRangeHelper().selectedHtml();

                                if (selection) {
                                    // Wenn Text ausgewählt ist, formatieren Sie nur den ausgewählten Bereich
                                    var header = '<h' + $(this).data('headersize') + '>';
                                    var formattedText = header + selection + '</h' + $(this).data('headersize') + '>';

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


                const textarea = document.getElementById('sceditorID');
                var myEditor = sceditor.create(textarea, {
                    format: 'xhtml',
                    style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css',
                    toolbar: 'headers|bold,italic,underline,strike,subscript,superscript|left,center,right,justify|font,size,color,removeformat|bulletlist,orderedlist|table|quote|horizontalrule|image|link,unlink|date,time|ltr,rtl|print,maximize|source|h1,h2',
                    emoticonsEnabled: false,
                    emoticonsCompat: false,
                    plugins: 'plaintext,undo,format',
                    pastetext: {
                        addButton: true,
                        enabled: false // Set to true to start in enabled state
                    },
                });
            };
        },
    };
  </script>


