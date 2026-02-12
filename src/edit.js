/**
 * Team Grid block editor component.
 *
 * @package WhoTeamManager
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, Spinner, Placeholder } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEffect, useCallback } from '@wordpress/element';
import { DndContext, closestCenter, KeyboardSensor, PointerSensor, useSensor, useSensors } from '@dnd-kit/core';
import { SortableContext, sortableKeyboardCoordinates, rectSortingStrategy, useSortable } from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';
import { restrictToParentElement } from '@dnd-kit/modifiers';

/**
 * A single sortable team member card for the editor.
 *
 * @param {Object} props           Component props.
 * @param {Object} props.member    The team member post object.
 * @param {string} props.jobTitle  The member's job title.
 * @param {string} props.thumbnail The member's thumbnail HTML.
 * @return {JSX.Element} The sortable card element.
 */
function SortableMemberCard( { member, jobTitle, thumbnail } ) {
	const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable( {
		id: member.id,
	} );

	const style = {
		transform: CSS.Transform.toString( transform ),
		transition,
		opacity: isDragging ? 0.5 : 1,
		cursor: 'grab',
	};

	return (
		<div
			ref={ setNodeRef }
			style={ style }
			className="who-team-member"
			{ ...attributes }
			{ ...listeners }
		>
			<div className="who-team-member__image">
				{ thumbnail ? (
					<img
						src={ thumbnail }
						alt={ member.title.rendered }
						className="who-team-member__photo"
					/>
				) : (
					<div className="who-team-member__photo-placeholder">
						{ __( 'No photo', 'who-team-manager' ) }
					</div>
				) }
			</div>
			<div className="who-team-member__info">
				<h3 className="who-team-member__name">
					{ member.title.rendered || __( '(No name)', 'who-team-manager' ) }
				</h3>
				{ jobTitle && (
					<p className="who-team-member__title">{ jobTitle }</p>
				) }
				{ member.content?.rendered && (
					<div
						className="who-team-member__description"
						dangerouslySetInnerHTML={ {
							__html: member.content.rendered,
						} }
					/>
				) }
			</div>
		</div>
	);
}

/**
 * Edit component for the Team Grid block.
 *
 * @param {Object}   props               Block edit props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update block attributes.
 * @return {JSX.Element} The block editor UI.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { columns, memberIds } = attributes;
	const blockProps = useBlockProps( {
		className: 'who-team-grid',
		style: { '--who-team-columns': columns },
	} );

	const { members, media, isLoading } = useSelect( ( select ) => {
		const { getEntityRecords, getMedia, isResolving } = select( 'core' );

		const teamMembers = getEntityRecords( 'postType', 'who_team_member', {
			per_page: 100,
			status: 'publish',
			_embed: false,
		} );

		const loading = isResolving( 'getEntityRecords', [
			'postType',
			'who_team_member',
			{ per_page: 100, status: 'publish', _embed: false },
		] );

		// Gather featured image IDs.
		const mediaMap = {};
		if ( teamMembers ) {
			teamMembers.forEach( ( member ) => {
				if ( member.featured_media ) {
					const img = getMedia( member.featured_media );
					if ( img ) {
						mediaMap[ member.id ] =
							img.media_details?.sizes?.medium?.source_url ||
							img.source_url;
					}
				}
			} );
		}

		return {
			members: teamMembers || [],
			media: mediaMap,
			isLoading: loading,
		};
	}, [] );

	// Initialize memberIds when members load if no order is saved.
	useEffect( () => {
		if ( members.length > 0 && memberIds.length === 0 ) {
			setAttributes( { memberIds: members.map( ( m ) => m.id ) } );
		}
	}, [ members, memberIds.length, setAttributes ] );

	// Sync: add new members not in saved order, remove deleted ones.
	useEffect( () => {
		if ( members.length === 0 || memberIds.length === 0 ) {
			return;
		}

		const publishedIds = members.map( ( m ) => m.id );
		const filtered = memberIds.filter( ( id ) => publishedIds.includes( id ) );
		const newIds = publishedIds.filter( ( id ) => ! memberIds.includes( id ) );

		if ( filtered.length !== memberIds.length || newIds.length > 0 ) {
			setAttributes( { memberIds: [ ...filtered, ...newIds ] } );
		}
	}, [ members, memberIds, setAttributes ] );

	const sensors = useSensors(
		useSensor( PointerSensor, {
			activationConstraint: { distance: 5 },
		} ),
		useSensor( KeyboardSensor, {
			coordinateGetter: sortableKeyboardCoordinates,
		} )
	);

	const handleDragEnd = useCallback(
		( event ) => {
			const { active, over } = event;
			if ( ! over || active.id === over.id ) {
				return;
			}

			const oldIndex = memberIds.indexOf( active.id );
			const newIndex = memberIds.indexOf( over.id );

			if ( oldIndex === -1 || newIndex === -1 ) {
				return;
			}

			const updated = [ ...memberIds ];
			updated.splice( oldIndex, 1 );
			updated.splice( newIndex, 0, active.id );
			setAttributes( { memberIds: updated } );
		},
		[ memberIds, setAttributes ]
	);

	// Sort members by the saved order.
	const orderedMembers = memberIds
		.map( ( id ) => members.find( ( m ) => m.id === id ) )
		.filter( Boolean );

	if ( isLoading ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="groups"
					label={ __( 'Team Grid', 'who-team-manager' ) }
				>
					<Spinner />
				</Placeholder>
			</div>
		);
	}

	if ( members.length === 0 ) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon="groups"
					label={ __( 'Team Grid', 'who-team-manager' ) }
					instructions={ __(
						'No team members found. Add some team members first.',
						'who-team-manager'
					) }
				/>
			</div>
		);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Grid Settings', 'who-team-manager' ) }>
					<RangeControl
						label={ __( 'Columns', 'who-team-manager' ) }
						value={ columns }
						onChange={ ( value ) =>
							setAttributes( { columns: value } )
						}
						min={ 1 }
						max={ 6 }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<DndContext
					sensors={ sensors }
					collisionDetection={ closestCenter }
					modifiers={ [ restrictToParentElement ] }
					onDragEnd={ handleDragEnd }
				>
					<SortableContext
						items={ orderedMembers.map( ( m ) => m.id ) }
						strategy={ rectSortingStrategy }
					>
						{ orderedMembers.map( ( member ) => (
							<SortableMemberCard
								key={ member.id }
								member={ member }
								jobTitle={
									member.meta?.[ '_who_team_job_title' ] || ''
								}
								thumbnail={ media[ member.id ] || '' }
							/>
						) ) }
					</SortableContext>
				</DndContext>
			</div>
		</>
	);
}
