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
mix.styles([
    'resources/css/libs/bootstrap.css',
    'resources/css/libs/datatables.css',
    'resources/css/libs/font-awesome_new.css',
    'resources/css/libs/jquery-ui.css',
    'resources/css/libs/fontastic.css',
    'resources/css/libs/jquery.mCustomScrollbar.css',
    'resources/css/libs/style.default.premium.css',
    'resources/css/libs/dropify.min.css',
    'resources/css/libs/swiper-bundle.min.css',
    'resources/css/libs/custom.css',
    'resources/css/libs/welcome.css',
], 'public/css/libs.css')
    .postCss("resources/css/app.css", "public/css", [
        require("tailwindcss")]);
mix.scripts([
    'resources/js/libs/jquery.js',
    'resources/js/libs/jquery-ui.js',
    'resources/js/libs/dropify.min.js',
    'resources/js/libs/bootstrap.js',
    'resources/js/libs/datatables.js',
    'resources/js/libs/jquery.mCustomScrollbar.js',
    'resources/js/libs/front.js',
    'resources/js/libs/jqBootstrapValidation.js',
    'resources/js/libs/sweetalert.min.js',
    'resources/js/libs/swiper-bundle.min.js',
    'resources/js/libs/jquery.waypoints.min.js',
    'resources/js/libs/custom.js',
], 'public/js/libs.js');
