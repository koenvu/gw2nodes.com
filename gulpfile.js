var elixir = require('laravel-elixir');

require('laravel-elixir-vueify');

elixir(function(mix) {
    mix.sass('app.scss');

    mix.browserify('app.js');

	mix.scripts([
		'leaflet.js',
		'jquery.js',
		'bootstrap.js',
		'leaflet.label.js',
		'bootstrap.switch.js',
		'map.js',
		'notifications.js'
	]);

    mix.version([
    	'css/app.css',
    	'js/app.js',
    	'js/all.js',
    ]);

    mix.browserSync({
		proxy: 'gw2nodes.vm',
		notify: false
	});
});
