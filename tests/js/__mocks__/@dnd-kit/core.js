const React = require( 'react' );

module.exports = {
	DndContext: ( { children } ) =>
		React.createElement( 'div', { 'data-testid': 'dnd-context' }, children ),
	closestCenter: jest.fn(),
	KeyboardSensor: jest.fn(),
	PointerSensor: jest.fn(),
	useSensor: () => ( {} ),
	useSensors: () => [],
};
