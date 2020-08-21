const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

//mix.setPublicPath('../../public').mergeManifest();

if (mix.inProduction()) {


    /*     mix.js(__dirname + '/src/Resources/js/startscript.js', __dirname + '/src/Public/js/startscript.js')
            .js(__dirname + '/src/Resources/js/vuecomponents.js', __dirname + '/src/Public/js/vuecomponents.js')
            .js(__dirname + '/src/Resources/js/laravelkit.js', __dirname + '/src/Public/js/laravelkit.js')
            .sass(__dirname + '/src/Resources/sass/laravelkit.scss', __dirname + '/src/Public/css/laravelkit.css'); */

} else {
    //CSS
    mix.sass(__dirname + '/src/Resources/sass/adminlte.scss', __dirname + '/src/Public/css/app.css');
    mix.sass(__dirname + '/src/Resources/sass/vuecomponents.scss', __dirname + '/src/Public/css/vuecomponents.css');

    //JS
    mix.js(__dirname + '/src/Resources/js/adminlte.js', __dirname + '/src/Public/js/app.js');
    mix.js(__dirname + '/src/Resources/js/vuecomponents.js', __dirname + '/src/Public/js/vuecomponents.js');
}