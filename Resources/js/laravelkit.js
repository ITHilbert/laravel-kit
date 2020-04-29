$(document).ready(function() {
    //Daten anpassen vor dem submit
    require('./lib/myform.js');

});

//Für das Delete Form
require('./lib/deleteform.js');

//Datatables.net
var dt = require('datatables.net')(window, $);