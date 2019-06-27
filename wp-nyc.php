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

use Asset_Loader;

function enqueue_block_editor_assets() {
  Asset_Loader\autoenqueue(
    plugin_dir_path( __FILE__ ) . '/build/asset-manifest.json',
    // Expect the bundle to be generated as editor.js
    'editor.js',
    [
      'handle'  => 'wp-nyc-editor-scripts',
      'scripts' => [ 'wp-element', 'wp-component', 'wp-editor' ],
    ]
  );
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_assets' );


function enqueue_frontend_assets() {
  Asset_Loader\autoenqueue(
    plugin_dir_path( __FILE__ ) . '/build/asset-manifest.json',
    // Expect the bundle to be generated as frontend.js
    'frontend.js',
    [
      'handle'  => 'wp-nyc-editor-frontend-scripts',
      'scripts' => [],
    ]
  );
}
add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_frontend_assets' );
