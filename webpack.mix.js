let mix = require('laravel-mix');
require('laravel-mix-eslint');

mix.setPublicPath('public');
mix
  .sass('resources/assets/sass/app.scss', './public/css/app.css')
  .js('resources/assets/js/app.js', './public/js/')
  .eslint()
  .version();
