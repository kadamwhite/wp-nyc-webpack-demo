// Example webpack.config.prod.js file.
const { externals, helpers, presets } = require( '@humanmade/webpack-helpers' );
const { filePath } = helpers;

module.exports = presets.production( {
	externals,
	entry: {
		frontend: filePath( 'src/frontend.js' ),
		editor: filePath( 'src/editor.js' ),
	},
	output: {
		path: filePath( 'build' ),
	},
} );
