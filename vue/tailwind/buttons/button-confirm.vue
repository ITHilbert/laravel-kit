<template>
  <div class="inline-block">
    <!-- The actual trigger button -->
    <button type="button" @click="showModal = true" :class="buttonClass">
      <slot>
        <span class="flex items-center gap-1.5"><i :class="icon"></i> {{ text }}</span>
      </slot>
    </button>

    <!-- The Modal Overlay -->
    <div v-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
      <!-- Background backdrop -->
      <div 
        v-show="showModal"
        class="fixed inset-0 bg-gray-500/75 transition-opacity" 
        @click="showModal = false"
      ></div>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
          
          <div 
            v-show="showModal"
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
          >
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                  <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                    {{ title }}
                  </h3>
                  <div class="mt-3">
                    <p class="text-sm text-gray-500 leading-relaxed">
                      {{ message }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button type="button" @click="confirm" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                <i :class="icon" class="mr-1.5 mt-1"></i> {{ confirmText }}
              </button>
              <button type="button" @click="showModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                {{ cancelText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    name: 'ButtonConfirm',
    props: {
        title: { type: String, default: 'Bitte bestätigen' },
        message: { type: String, default: 'Bist du sicher?' },
        confirmText: { type: String, default: 'Bestätigen' },
        cancelText: { type: String, default: 'Abbrechen' },
        text: { type: String, default: 'Löschen' },
        icon: { type: String, default: 'fas fa-trash-alt' },
        buttonClass: { type: String, default: 'text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors flex items-center gap-1.5 focus:outline-none' }
    },
    data() {
        return {
            showModal: false
        }
    },
    methods: {
        confirm() {
            this.showModal = false;
            // Native dispatch of submit if nested in form
            const form = this.$el.closest('form');
            if (form) {
                form.submit();
            } else {
                this.$emit('confirmed');
            }
        }
    }
}
</script>
