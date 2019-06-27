// Example webpack.config.prod.js file.
const { helpers, presets } = require( '@humanmade/webpack-helpers' );
const { filePath } = helpers;

module.exports = presets.production( {
	entry: {
		editor: filePath( 'src/editor.js' ),
		frontend: filePath( 'src/frontend.js' ),
	},
	output: {
		path: filePath( 'build' ),
	},
} );
