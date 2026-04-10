<template>
    <!-- Tailwind Modal – kein Bootstrap benötigt -->
    <div
        id="myModalDelete"
        :route="route"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden"
        @click.self="close"
    >
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-base font-semibold text-gray-900">{{ titel }}</h5>
                <button
                    type="button"
                    class="text-gray-400 hover:text-gray-600 transition-colors"
                    @click="close"
                    aria-label="Schließen"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Body -->
            <div class="px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.834-1.964-.834-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ body }}</p>
                </div>
            </div>
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200">
                <button
                    id="DialogDeleteButton1"
                    type="button"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors"
                    @click="close"
                >
                    {{ btn1text }}
                </button>
                <button
                    id="DialogDeleteButton2"
                    type="button"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors"
                    @click="confirm"
                >
                    {{ btn2text }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'titel': {
                default: 'Löschen'
            },
            'body': {
                default: 'Wollen Sie den Datensatz wirklich löschen?'
            },
            'btn1text': {
                default: 'Abbruch'
            },
            'btn2text': {
                default: 'Löschen'
            },
            'route': {
                type: String,
                default: ''
            },
        },
        methods: {
            close() {
                document.getElementById('myModalDelete').classList.add('hidden');
            },
            confirm() {
                this.close();
                let pfad = document.getElementById('myModalDelete').getAttribute('route');
                // Route endet auf "/0" – die 0 wird durch die echte ID ersetzt
                pfad = pfad.substring(0, pfad.lastIndexOf('/') + 1) + deleteID;
                let form = document.getElementById('formDelete');
                form.setAttribute('action', pfad);
                form.submit();
            },
        },
        mounted() {
            // ESC-Taste schließt den Dialog
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.close();
                }
            });
        }
    }
</script>