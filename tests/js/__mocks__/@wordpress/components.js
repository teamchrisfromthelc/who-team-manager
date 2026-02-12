const React = require( 'react' );

module.exports = {
	PanelBody: ( { children, title } ) =>
		React.createElement( 'div', { 'data-testid': 'panel-body', 'aria-label': title }, children ),
	RangeControl: ( { label, value, onChange, min, max } ) =>
		React.createElement( 'input', {
			'data-testid': 'range-control',
			'aria-label': label,
			type: 'range',
			value,
			min,
			max,
			onChange: ( e ) => onChange( Number( e.target.value ) ),
		} ),
	Spinner: () =>
		React.createElement( 'div', { 'data-testid': 'spinner' }, 'Loading...' ),
	Placeholder: ( { label, instructions, children } ) =>
		React.createElement(
			'div',
			{ 'data-testid': 'placeholder' },
			React.createElement( 'span', null, label ),
			instructions ? React.createElement( 'span', null, instructions ) : null,
			children
		),
};
