const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/Resources/js/startscript.js', 'js/startscript.js')
    .js(__dirname + '/Resources/js/vuecomponents.js', 'js/vuecomponents.js')
    .js(__dirname + '/Resources/js/laravelkit.js', 'js/laravelkit.js')
    .sass(__dirname + '/Resources/sass/laravelkit.scss', 'css/laravelkit.css');

if (mix.inProduction()) {
    mix.version();
}