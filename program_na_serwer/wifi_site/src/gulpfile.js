const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

// mix.browserify('app.js');

elixir(mix => {
    mix.less('AdminLTE.less').version('css/AdminLTE.css');
    mix.less('skins/_all-skins.less');
    mix.scripts('theme.js');
    mix.scripts('app.js');
});
