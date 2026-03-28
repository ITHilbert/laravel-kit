<template>
    <nav class="navbar navbar-vertical navbar-expand-lg" v-bind="$attrs">
        <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
            <div class="navbar-vertical-content">
                <ul class="navbar-nav flex-col" id="navbarVerticalNav">
                    <slot></slot>
                </ul>
            </div>
        </div>
        <div class="navbar-vertical-footer">
            <button class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors navbar-vertical-toggle border-0 fw-semi-bold w-full white-space-nowrap flex items-center">
                <!-- <span class="uil uil-left-arrow-to-left fs-0"></span>
                <span class="uil uil-arrow-from-right fs-0"></span> -->
                <span class="nav-link-icon">
                <span data-feather="refresh-cw"></span>
                </span>
                <span class="navbar-vertical-footer-text ml-2">Collapsed View</span>
            </button>
        </div>
    </nav>
</template>
