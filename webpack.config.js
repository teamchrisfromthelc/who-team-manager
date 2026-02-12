/**
 * Custom Webpack config for WHO Team Manager.
 *
 * Extends the default @wordpress/scripts config to add the
 * Job Title sidebar panel as a second entry point.
 *
 * @package WhoTeamManager
 */

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve( __dirname, 'src', 'index.js' ),
		'plugins/job-title-panel': path.resolve(
			__dirname,
			'src',
			'plugins',
			'job-title-panel.js'
		),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
	},
};
