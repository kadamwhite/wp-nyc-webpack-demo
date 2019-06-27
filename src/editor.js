import { autoloadBlocks } from '@humanmade/webpack-helpers/hmr';

autoloadBlocks( {
	getContext: () => require.context( './blocks', true, /index\.js$/ ),
}, ( context, loadModules ) => {
	if ( module.hot ) {
		module.hot.accept( context.id, loadModules );
	}
} );
