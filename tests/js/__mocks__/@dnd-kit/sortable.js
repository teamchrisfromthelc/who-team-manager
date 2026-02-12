const React = require( 'react' );

module.exports = {
	SortableContext: ( { children } ) =>
		React.createElement( 'div', { 'data-testid': 'sortable-context' }, children ),
	sortableKeyboardCoordinates: jest.fn(),
	rectSortingStrategy: jest.fn(),
	useSortable: () => ( {
		attributes: {},
		listeners: {},
		setNodeRef: jest.fn(),
		transform: null,
		transition: null,
		isDragging: false,
	} ),
};
