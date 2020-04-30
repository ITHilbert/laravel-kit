const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env' /*, debug: true*/ }));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

//mix.setPublicPath('../../public').mergeManifest();

mix.js(__dirname + '/src/Resources/js/startscript.js', __dirname + '/src/Public/js/startscript.js')
    .js(__dirname + '/src/Resources/js/vuecomponents.js', __dirname + '/src/Public/js/vuecomponents.js')
    .js(__dirname + '/src/Resources/js/laravelkit.js', __dirname + '/src/Public/js/laravelkit.js')
    .sass(__dirname + '/src/Resources/scss/laravelkit.scss', __dirname + '/src/Public/css/laravelkit.css');

/* if (mix.inProduction()) {
    mix.version();
} */