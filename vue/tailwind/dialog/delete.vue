<template >
    <!-- JQery muss eingebunden sein -->
    <div id="myModalDelete" class="modal fade" :route="route">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ titel }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ body }}</p>
                </div>
                <div class="modal-footer">
                    <button id="DialogDeleteButton1" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-cancel" data-dismiss="modal"><i class="far fa-times-circle"></i> {{ btn1text }}</button>
                    <button id="DialogDeleteButton2" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-delete" data-dismiss="modal"><i class="fas fa-minus-circle"></i> {{ btn2text }}</button>
                </div>
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
        data: function(){
            return {
                'defaultclass': 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-euro ',
            }
        },
        methods:{
            formatValue(value) {
                let val = (value/1).toFixed(2).replace('.', ',')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            },
        },
        mounted () {
            $('#DialogDeleteButton1').on('click', function() {
                $('#myModalDelete').modal('hide');
            })
            
            $('#DialogDeleteButton2').on('click', function() {
                $('#myModalDelete').modal('hide');
                let pfad = $('#myModalDelete').attr('route');
                //console.log("Route: " + pfad);
                pfad = pfad.substr(0, pfad.length-1);
                pfad = pfad + deleteID;
                //window.location.href =  pfad;
                let form = document.getElementById("formDelete");
                form.setAttribute ('action', pfad);
                form.submit();

            });


        }
    }
</script>