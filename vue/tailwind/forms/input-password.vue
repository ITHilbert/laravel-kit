<template>
    <div class="relative flex w-full mt-1">
        <input 
            :type="showPassword ? 'text' : 'password'" 
            :id="name" 
            :name="name" 
            :class="['block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5 px-4 pr-12 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:border-indigo-500 transition-all', $attrs.class, addClass]" 
            v-bind="$attrs"
        >
        <button 
            type="button" 
            @click="togglePassword" 
            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 focus:outline-none"
            title="Passwort anzeigen/verbergen"
        >
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            addClass: {
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
        data() {
            return {
                showPassword: false
            }
        },
        methods: {
            togglePassword() {
                this.showPassword = !this.showPassword;
            }
        }
    }
</script>
