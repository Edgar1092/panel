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

mix.config.publicPath='../public_html/panel';
mix.js('resources/js/app.js', '../public_html/panel/js')
   .sass('resources/sass/app.scss', '../public_html/panel/css');
