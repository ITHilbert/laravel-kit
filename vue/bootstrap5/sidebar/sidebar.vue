<template>
    <nav class="navbar navbar-vertical navbar-expand-lg" v-bind="$attrs">
        <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
            <div class="navbar-vertical-content">
                <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                    <slot></slot>
                </ul>
            </div>
        </div>
        <div class="navbar-vertical-footer">
            <button class="btn navbar-vertical-toggle border-0 fw-semi-bold w-100 white-space-nowrap d-flex align-items-center">
                <!-- <span class="uil uil-left-arrow-to-left fs-0"></span>
                <span class="uil uil-arrow-from-right fs-0"></span> -->
                <span class="nav-link-icon">
                <span data-feather="refresh-cw"></span>
                </span>
                <span class="navbar-vertical-footer-text ms-2">Collapsed View</span>
            </button>
        </div>
    </nav>
</template>
