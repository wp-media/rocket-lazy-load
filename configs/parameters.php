<?php

return [
	'plugin_name'               => sanitize_key( 'Rocket Lazy Load' ),
	'plugin_basename'           => ROCKET_LL_BASENAME,
	'template_path'             => realpath( plugin_dir_path( __DIR__ ) ) . '/views/',
	'assets_baseurl'            => plugin_dir_url( __DIR__ ) . 'assets/',
	'is_mu_plugin'              => false,
	'translation_key'           => 'rocket-lazy-load',
	'prefix'                    => 'rocket_lazyload_',
	'rocket_lazyload_settings'  => 'options'
];