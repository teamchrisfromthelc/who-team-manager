/**
 * Jest configuration for WHO Team Manager.
 *
 * Extends the default @wordpress/scripts Jest config.
 *
 * @package WhoTeamManager
 */

const defaultConfig = require( '@wordpress/scripts/config/jest-unit.config.js' );

module.exports = {
	...defaultConfig,
	moduleNameMapper: {
		...( defaultConfig.moduleNameMapper || {} ),
		'\\.(scss|css)$':
			'@wordpress/jest-preset-default/scripts/style-mock.js',
		'^@wordpress/i18n$': '<rootDir>/tests/js/__mocks__/@wordpress/i18n.js',
		'^@wordpress/block-editor$':
			'<rootDir>/tests/js/__mocks__/@wordpress/block-editor.js',
		'^@wordpress/components$':
			'<rootDir>/tests/js/__mocks__/@wordpress/components.js',
		'^@wordpress/data$':
			'<rootDir>/tests/js/__mocks__/@wordpress/data.js',
		'^@wordpress/element$':
			'<rootDir>/tests/js/__mocks__/@wordpress/element.js',
		'^@dnd-kit/core$':
			'<rootDir>/tests/js/__mocks__/@dnd-kit/core.js',
		'^@dnd-kit/sortable$':
			'<rootDir>/tests/js/__mocks__/@dnd-kit/sortable.js',
		'^@dnd-kit/utilities$':
			'<rootDir>/tests/js/__mocks__/@dnd-kit/utilities.js',
		'^@dnd-kit/modifiers$':
			'<rootDir>/tests/js/__mocks__/@dnd-kit/modifiers.js',
	},
	testMatch: [ '**/tests/js/**/*.test.[jt]s?(x)' ],
};
