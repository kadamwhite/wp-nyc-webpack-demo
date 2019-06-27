<?php
/*
Plugin Name: WP NYC Webpack Toolkit Demo
Description: Demonstration of Human Made's frontend bundling toolkit.
Version: 1.0.0
Author: K Adam White
License: GPLv2 or later
Text Domain: wpnyc
*/

namespace WP_NYC_Webpack_Toolkit_Demo;

/**
 * Enqueue editor assets based on the generated `asset-manifest.json` file.
 *
 * @return void
 */
function enqueue_block_assets() {
	wp_enqueue_script(
		'wp-nyc-editor-blocks',
		plugin_dir_url( __FILE__ ) . 'build/editor.js',
		[
			'wp-components',
			'wp-data',
			'wp-element',
		]
	);
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_assets' );


function enqueue_frontend_assets() {
	wp_enqueue_script(
		'wp-nyc-frontend',
		plugin_dir_url( __FILE__ ) . 'build/frontend.js',
		[]
	);
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_frontend_assets' );
