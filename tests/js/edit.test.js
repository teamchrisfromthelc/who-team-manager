/**
 * Jest tests for the Team Grid block editor component.
 *
 * @package WhoTeamManager
 */

import { render, screen } from '@testing-library/react';
import '@testing-library/jest-dom';
import { __setMockUseSelectReturn } from '@wordpress/data';

import Edit from '../../src/edit';

describe( 'Team Grid Edit Component', () => {
	const defaultProps = {
		attributes: {
			columns: 3,
			memberIds: [],
		},
		setAttributes: jest.fn(),
	};

	beforeEach( () => {
		defaultProps.setAttributes.mockClear();
		__setMockUseSelectReturn( { members: [], media: {}, isLoading: false } );
	} );

	it( 'renders a placeholder when loading', () => {
		__setMockUseSelectReturn( { members: [], media: {}, isLoading: true } );

		render( <Edit { ...defaultProps } /> );

		expect( screen.getByTestId( 'spinner' ) ).toBeInTheDocument();
		expect( screen.getByText( 'Team Grid' ) ).toBeInTheDocument();
	} );

	it( 'renders a placeholder when no members exist', () => {
		__setMockUseSelectReturn( { members: [], media: {}, isLoading: false } );

		render( <Edit { ...defaultProps } /> );

		expect(
			screen.getByText( 'No team members found. Add some team members first.' )
		).toBeInTheDocument();
	} );

	it( 'renders team member cards', () => {
		const members = [
			{
				id: 1,
				title: { rendered: 'Jane Doe' },
				content: { rendered: '<p>A great leader.</p>' },
				meta: { _who_team_job_title: 'VP of Sales' },
				featured_media: 0,
			},
			{
				id: 2,
				title: { rendered: 'John Smith' },
				content: { rendered: '<p>Loves coding.</p>' },
				meta: { _who_team_job_title: 'Lead Developer' },
				featured_media: 0,
			},
		];

		__setMockUseSelectReturn( { members, media: {}, isLoading: false } );

		render(
			<Edit
				{ ...{
					...defaultProps,
					attributes: { columns: 3, memberIds: [ 1, 2 ] },
				} }
			/>
		);

		expect( screen.getByText( 'Jane Doe' ) ).toBeInTheDocument();
		expect( screen.getByText( 'VP of Sales' ) ).toBeInTheDocument();
		expect( screen.getByText( 'John Smith' ) ).toBeInTheDocument();
		expect( screen.getByText( 'Lead Developer' ) ).toBeInTheDocument();
	} );

	it( 'renders the columns range control in inspector', () => {
		const members = [
			{
				id: 1,
				title: { rendered: 'Test Member' },
				content: { rendered: '' },
				meta: {},
				featured_media: 0,
			},
		];

		__setMockUseSelectReturn( { members, media: {}, isLoading: false } );

		render(
			<Edit
				{ ...{
					...defaultProps,
					attributes: { columns: 4, memberIds: [ 1 ] },
				} }
			/>
		);

		expect( screen.getByTestId( 'inspector' ) ).toBeInTheDocument();
		expect( screen.getByLabelText( 'Columns' ) ).toHaveValue( '4' );
	} );

	it( 'shows photo placeholder when no image', () => {
		const members = [
			{
				id: 1,
				title: { rendered: 'No Photo Person' },
				content: { rendered: '' },
				meta: {},
				featured_media: 0,
			},
		];

		__setMockUseSelectReturn( { members, media: {}, isLoading: false } );

		render(
			<Edit
				{ ...{
					...defaultProps,
					attributes: { columns: 3, memberIds: [ 1 ] },
				} }
			/>
		);

		expect( screen.getByText( 'No photo' ) ).toBeInTheDocument();
	} );

	it( 'applies the correct CSS custom property for columns', () => {
		const members = [
			{
				id: 1,
				title: { rendered: 'Test' },
				content: { rendered: '' },
				meta: {},
				featured_media: 0,
			},
		];

		__setMockUseSelectReturn( { members, media: {}, isLoading: false } );

		render(
			<Edit
				{ ...{
					...defaultProps,
					attributes: { columns: 5, memberIds: [ 1 ] },
				} }
			/>
		);

		const wrapper = screen.getByTestId( 'block-wrapper' );
		expect( wrapper ).toHaveStyle( '--who-team-columns: 5' );
	} );
} );
