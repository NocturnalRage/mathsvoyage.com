let mix = require('laravel-mix');
require('laravel-mix-eslint');

mix.setPublicPath('public');
mix
  .sass('resources/assets/sass/app.scss', './public/css/app.css')
  .js('resources/assets/js/app.js', './public/js/')
  .js('resources/assets/js/quiz.js', './public/js/')
  .js('resources/assets/js/times-tables-quiz.js', './public/js/')
  .js('resources/assets/js/general-arithmetic-quiz.js', './public/js/')
  .js('resources/assets/js/workedSolutions.js', './public/js/')
  .eslint()
  .version();
