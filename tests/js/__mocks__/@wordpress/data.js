// Shared mutable state for tests to control useSelect return value.
const state = {
	useSelectReturn: { members: [], media: {}, isLoading: false },
};

module.exports = {
	useSelect: () => state.useSelectReturn,
	__setMockUseSelectReturn: ( value ) => {
		state.useSelectReturn = value;
	},
};
