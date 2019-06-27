# WordPress NYC Webpack Helpers Demo

This repo holds the code for a presentation given at a dev-oriented NYC WordPress meetup on June 27, 2019.

Starting from a [bare-bones plugin](https://github.com/kadamwhite/wp-nyc-webpack-demo/tree/step-0-basic-repo), this repo walks you through setting up and configuring **[@humanmade/webpack-helpers](https://humanmade.github.io/webpack-helpers)** to painlessly build frontend assets!

## Step 1: Install Helpers & Set Up Production Build

See [this PR](https://github.com/kadamwhite/wp-nyc-webpack-demo/pull/1/files) for a comprehensive code diff of this step.

Starting with the tag linked above, run this command to install Webpack and the [Human Made](https://humanmade.com) Webpack Helpers:

```bash
npm install --save-dev webpack webpack-cli webpack-dev-server @humanmade/webpack-helpers
```

Then, we'll populate our `.config/webpack.prod.js` file, which we will be using to build a production version of our plugin code using [Webpack](https://webpack.js.org):

```diff
+// Example webpack.config.prod.js file.
+const { helpers, presets } = require( '@humanmade/webpack-helpers' );
+const { filePath } = helpers;
+
+module.exports = presets.production( {
+	entry: {
+		editor: filePath( 'src/editor.js' ),
+		frontend: filePath( 'src/frontend.js' ),
+	},
+	output: {
+		path: filePath( 'build' ),
+	},
+} );
```

and then add an [npm script](https://docs.npmjs.com/misc/scripts) which we'll use to trigger our build:

```diff
   "version": "1.0.0",
   "description": "Demonstration of Human Made's frontend bundling toolkit.",
   "scripts": {
+    "build": "webpack --config=.config/webpack.prod.js"
   },
   "keywords": [],
```
Now, if we run `npm run build`, we'll get a `editor.js` and `frontend.js` output into the plugin's `build/` directory.

## Step 2: Add Babel & Write Our First Block

See [this PR](https://github.com/kadamwhite/wp-nyc-webpack-demo/pull/2/files) for a comprehensive diff of this step.

Now that our build is working, let's add some configuration so that we can begin writing blocks using modern JavaScript.

We'll be leaning on the Webpack Helpers module's built-in Babel preset, which is based on the preset provided by the WordPress core team. Create a new file called `.babelrc.js` in the plugin root (the leading `.` is important) and copy this content into it:

```js
module.exports = require( '@humanmade/webpack-helpers/babel-preset' );
```

Next, we'll make a small edit to our `webpack.prod.js` file to tell our Webpack build about the built-in WordPress script modules:

```diff
 // Example webpack.config.prod.js file.
-const { helpers, presets } = require( '@humanmade/webpack-helpers' );
+const { externals, helpers, presets } = require( '@humanmade/webpack-helpers' );
 const { filePath } = helpers;
 
 module.exports = presets.production( {
+	externals,
 	entry: {
 		editor: filePath( 'src/editor.js' ),
 		frontend: filePath( 'src/frontend.js' ),
 	}
 	output: {
 		path: filePath( 'build' ),
```
By including this `externals` object in our config (and incidentally, `externals,` here equates to the traditional JavaScript syntax of `externals: externals`), Webpack will now use the `wp` global to load in any WordPress core modules, without any further configuration required. We'll see how nice this is in just a second.

Next, we'll change our `editor.js` file to define our first block.

Now that we've added that `externals` entry to our configuration we can use the modern JavaScript `import` keyword to load our code, then use [`registerBlockType`](https://developer.wordpress.org/block-editor/developers/block-api/block-registration/) as normal:

```diff
-console.log( 'I am running in the editor' );
+import { registerBlockType } from '@wordpress/blocks';
+import { Fragment } from '@wordpress/element';
+
+registerBlockType( 'wpnyc/demo-1', {
+	title: 'Demo Block',
+	description: 'A simple demo block',
+	category: 'widgets',
+	icon: 'star-filled',
+	edit( { isSelected } ) {
+		return (
+			<Fragment>
+				<p>Demo Editor Content</p>
+				{ isSelected && (
+					<p><em>(selected, woo!)</em></p>
+				) }
+			</Fragment>
+		);
+	},
+	save() {
+		return (
+			<p>Demo Saved Content</p>
+		);
+	},
+} );

```

If we rebuild, our demo block will be available in the editor!

Next, we'll learn how we can use the Webpack DevServer to have our code live-reload as we work.

## Step 3: Add the Webpack DevServer

We installed [Webpack DevServer](https://github.com/webpack/webpack-dev-server) way back in step 1; let's configure it now.

Add another script command to your package.json file, to tell Webpack to use our (currently empty) dev configuration file:

```diff
   "version": "1.0.0",
   "description": "Demonstration of Human Made's frontend bundling toolkit.",
   "scripts": {
+    "start": "webpack-dev-server --config=.config/webpack.dev.js",
     "build": "webpack --config=.config/webpack.prod.js"
   },
   "keywords": [],
   "author": "K Adam White",
```

Next, we'll copy and paste our production configuration file into `.config/webpack.dev.js` and make a few small changes. We'll use the `presets.development` helper instead of `presets.production`, and then add some configuration to generate what we call an **asset manifest** which we'll use to tell WordPress where to find our development server.

```diff
+// Example webpack.config.dev.js file.
+const { externals, helpers, presets } = require( '@humanmade/webpack-helpers' );
+const { choosePort, filePath } = helpers;
+
+module.exports = choosePort( 8080 )
+	.then( port => presets.development( {
+		name: 'wp-nyc-editor-blocks',
+		externals,
+		devServer: {
+			port,
+		},
+		entry: {
+			editor: filePath( 'src/editor.js' ),
+			frontend: filePath( 'src/frontend.js' ),
+		},
+		output: {
+			path: filePath( 'build' ),
+			publicPath: `http://localhost:${ port }/`,
+		},
+	} ) );
```

If you run `npm start` now, you should see a new file generated in your directory called `build/asset-manifest.json`. It'll look something like this:

```json
{
  "editor.js": "http://localhost:8080/editor.js",
  "editor.js.map": "http://localhost:8080/editor.js.map",
  "frontend.js": "http://localhost:8080/frontend.js",
  "frontend.js.map": "http://localhost:8080/frontend.js.map"
}
```
Our dev server is running locally on `localhost`, using Node. This will normally be entirely invisible to WordPress, so we'll read in this file from our plugin's PHP code.

Rather than writing all the code to interpret this file and load the right bundle in the right instance, let's use another pre-made library to do a lot of the work for us: Human Made's [`asset-loader` plugin](https://github.com/humanmade/asset-loader)!

**Note**: This demo assumes that the Asset Loader is installed and activated as a WordPress plugin, but you may also install the asset loader using Composer if you prefer.

```diff
 namespace WP_NYC_Webpack_Toolkit_Demo;
 
+use Asset_Loader;
+
 /**
  * Enqueue editor assets based on the generated `asset-manifest.json` file.
  *
  * @return void
  */
 function enqueue_block_assets() {
-	wp_enqueue_script(
-		'wp-nyc-editor-blocks',
-		plugin_dir_url( __FILE__ ) . 'build/editor.js',
+	Asset_Loader\autoenqueue(
+		plugin_dir_path( __FILE__ ) . 'build/asset-manifest.json',
+		// Expect the bundle to be generated as editor.js
+		'editor.js',
 		[
-			'wp-blocks',
-			'wp-components',
-			'wp-data',
-			'wp-element',
+			'handle'  => 'wp-nyc-editor-blocks',
+			'scripts' => [
+				'wp-blocks',
+				'wp-components',
+				'wp-data',
+				'wp-element',
+			],
 		]
 	);
 }
@@ -31,10 +37,14 @@ add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_asse
 
 
 function enqueue_frontend_assets() {
-	wp_enqueue_script(
-		'wp-nyc-frontend',
-		plugin_dir_url( __FILE__ ) . 'build/frontend.js',
-		[]
+	Asset_Loader\autoenqueue(
+		plugin_dir_path( __FILE__ ) . 'build/asset-manifest.json',
+		// Expect the bundle to be generated as frontend.js
+		'frontend.js',
+		[
+			'handle'  => 'wp-nyc-editor-frontend-scripts',
+			'scripts' => [],
+		]
 	);
 }
 add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_frontend_assets' );
```

We don't have time to get into the specifics of what's going on here, but Asset Loader is designed to work as you'd expect if you use the syntax above.

Now, if we run `npm start` and make changes to our project, when we reload our WordPress site we'll see the latest & greatest changes!

## Step 4: Hot Reloading

See [this PR](https://github.com/kadamwhite/wp-nyc-webpack-demo/pull/4) for a comprehensive diff of this step.

A lot of the promise of Webpack and related tools is that they're supposed to let us see our changes live in the editor as we make them. There's a lot of tools for doing this in React, but until recently there haven't been many for doing the same with WordPress "Gutenberg" editor blocks. Human Made's Webpack Helpers aim to change this!

We'll move our block code from `editor.js` to a new file `blocks/demo-1/index.js`:

```diff
+++ src/blocks/demo-1/index.js
@@ -0,0 +1,25 @@
+import { Fragment } from '@wordpress/element';
+
+export const name = 'wpnyc/demo-1';
+
+export const settings = {
+	title: 'Demo Block',
+	description: 'A simple demo block',
+	category: 'widgets',
+	icon: 'star-filled',
+	edit( { isSelected } ) {
+		return (
+			<Fragment>
+				<p>Demo Editor Content</p>
+				{ isSelected && (
+					<p><em>(selected, woo!)</em></p>
+				) }
+			</Fragment>
+		);
+	},
+	save() {
+		return (
+			<p>Demo Saved Content</p>
+		);
+	},
+};
```

then change our `editor.js` file to pull in some [Hot Reloading (or "HMR") helpers for WordPress](https://github.com/kadamwhite/block-editor-hmr):

```diff
-import { registerBlockType } from '@wordpress/blocks';
-import { Fragment } from '@wordpress/element';
+import { autoloadBlocks } from '@humanmade/webpack-helpers/hmr';
 
-registerBlockType( 'wpnyc/demo-1', {
-	title: 'Demo Block',
-	description: 'A simple demo block',
-	category: 'widgets',
-	icon: 'star-filled',
-	edit( { isSelected } ) {
-		return (
-			<Fragment>
-				<p>Demo Editor Content</p>
-				{ isSelected && (
-					<p><em>(selected, woo!)</em></p>
-				) }
-			</Fragment>
-		);
-	},
-	save() {
-		return (
-			<p>Demo Saved Content</p>
-		);
-	},
+autoloadBlocks( {
+	getContext: () => require.context( './blocks', true, /index\.js$/ ),
+}, ( context, loadModules ) => {
+	if ( module.hot ) {
+		module.hot.accept( context.id, loadModules );
+	}
 } );
```

Now, if we restart our `npm start` development server and make changes to our block index file, we should see those changes live in your browser!

Run `npm run build` to generate a production-ready bundle, and your WordPress plugin will only use the dev server if it is running.
