// Example webpack.config.prod.js file.
const { externals, helpers, presets } = require( '@humanmade/webpack-helpers' );
const { choosePort, filePath } = helpers;

module.exports = choosePort( 8080 )
	.then( port => presets.development( {
		name: 'wp-nyc-editor-blocks',
		externals,
		devServer: {
			port,
		},
		entry: {
			editor: filePath( 'src/editor.js' ),
			frontend: filePath( 'src/frontend.js' ),
		},
		output: {
			path: filePath( 'build' ),
			publicPath: `http://localhost:${ port }/`,
		},
	} ) );
