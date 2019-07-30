const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts(['resources/vendor/dashboard.js',
             'resources/vendor/hoverable-collapse.js',
             'resources/vendor/off-canvas.js',
             'resources/vendor/settings.js',
             'resources/vendor/template.js',
             'resources/vendor/todolist.js',
             'resources/vendor/datatables.js',
             'resources/vendor/datatables-module.js',
             'node_modules/selectize/dist/js/standalone/selectize.min.js',
             'node_modules/selectize/dist/js/selectize.min.js'], 'public/js/vendor.js')
    .scripts('resources/js/global.js', 'public/js/app.js')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/datatables.scss', 'public/css/datatables.css')
    .less('node_modules/selectize/dist/less/selectize.less', 'public/css/vendor.css');
