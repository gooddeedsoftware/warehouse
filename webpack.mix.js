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
mix.js('resources/js/user.js', 'public/js')
mix.js('resources/js/productpackage.js', 'public/js')
mix.js('resources/js/customer.js', 'public/js')
mix.js('resources/js/ccsheet.js', 'public/js')
mix.js('resources/js/shipping.js', 'public/js')
mix.js('resources/js/product.js', 'public/js')
    .version()
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/customStyle.scss', 'public/css')
    .sass('resources/sass/production.scss', 'public/css')
    .sass('resources/sass/staging.scss', 'public/css');
