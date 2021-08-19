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

//Delete Forms
require('./vendor/laravelkit/deleteform.js')

//Meine Vue Components
require('./vendor/vue/vuecomponents.js')



//Delete Forms
//require('./vendor/laravelkit/myFunctions.js')
