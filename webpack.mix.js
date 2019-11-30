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

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css');

mix.styles([
      'resources/css/libs/bootstrap_4.css',
      'resources/css/libs/font-awesome_new.css',
      'resources/css/libs/fontastic.css',
      'resources/css/libs/style.blue.css',
      'resources/css/libs/custom.css',
  ], 'public/css/libs.css');
mix.scripts([
   'resources/js/libs/jquery.js',
   'resources/js/libs/bootstrap_4.js',
   'resources/js/libs/jquery.mCustomScrollbar.js',
   'resources/js/libs/front_new.js',
], 'public/js/libs.js');  
