require('./bootstrap');


//window.Popper = require('popper.js').default;

//JQuery
window.$ = window.jQuery = require('jquery'), require('jquery-ui');
require("jquery-ui/ui/widgets/autocomplete");


// DataTables -> benötigen jQuery
window.$ = dt = $.extend(require('datatables.net'));
//var dt = require('datatables.net')(window, $);
//import dt from 'datatables.net';

//Bootstrap
require('bootstrap/dist/js/bootstrap.bundle.min');


//overlayscrollbars
require('overlayscrollbars/js/jquery.overlayScrollbars');

//AdminLTE
require('./vendor/adminlte/dist/js/adminlte.min.js');

//Forms
require('./vendor/laravelkit/myform.js')

//Delete Forms
//require('./vendor/laravelkit/myFunctions.js')

//Meine Vue Components
require('./vueapp.js')