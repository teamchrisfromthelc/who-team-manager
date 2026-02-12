const React = require( 'react' );

module.exports = {
	useBlockProps: ( props ) => ( { ...( props || {} ), 'data-testid': 'block-wrapper' } ),
	InspectorControls: ( { children } ) =>
		React.createElement( 'div', { 'data-testid': 'inspector' }, children ),
};
