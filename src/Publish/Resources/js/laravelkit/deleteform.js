//Nötig für das löschen von Datensätzen
var deleteID;

function OpenDeleteForm(id) {
    deleteID = id;
    $('#myModalDelete').modal('show');
}