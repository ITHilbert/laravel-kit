<!-- https://getbootstrap.com/docs/5.0/components/modal/ -->
<template>
    <div class="modal" tabindex="-1" :id="id">
        <div class="modal-dialog">
            <div class="modal-content">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        id: {
            type: String,
            required: true
        }
    }
}
</script>
