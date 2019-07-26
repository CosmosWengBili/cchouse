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
             'resources/vendor/datatables-module.js'], 'public/js/vendor.js')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/datatables.scss', 'public/css/datatables.css');
