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
                    <button id="DialogDeleteButton1" class="btn btn-cancel" data-dismiss="modal"><i class="far fa-times-circle"></i> {{ btn1text }}</button>
                    <button id="DialogDeleteButton2" class="btn btn-delete" data-dismiss="modal"><i class="fas fa-minus-circle"></i> {{ btn2text }}</button>
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
                'defaultclass': 'form-control input-euro ',
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