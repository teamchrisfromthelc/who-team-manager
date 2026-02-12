/**
 * Job Title sidebar panel for Team Member editor.
 *
 * Registers a PluginDocumentSettingPanel that shows on the
 * who_team_member post type, allowing the user to set the job title meta.
 *
 * @package WhoTeamManager
 */

import { __ } from '@wordpress/i18n';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { TextControl } from '@wordpress/components';
import { useSelect, useDispatch } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Job Title panel component.
 *
 * @return {JSX.Element|null} The panel or null if not a team member.
 */
function JobTitlePanel() {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	if ( postType !== 'who_team_member' ) {
		return null;
	}

	const [ meta, setMeta ] = useEntityProp( 'postType', 'who_team_member', 'meta' );

	const jobTitle = meta?._who_team_job_title || '';

	return (
		<PluginDocumentSettingPanel
			name="who-team-job-title"
			title={ __( 'Job Title', 'who-team-manager' ) }
			className="who-team-job-title-panel"
		>
			<TextControl
				label={ __( 'Job Title', 'who-team-manager' ) }
				value={ jobTitle }
				onChange={ ( value ) =>
					setMeta( { ...meta, _who_team_job_title: value } )
				}
				placeholder={ __( 'e.g. VP of Sales', 'who-team-manager' ) }
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'who-team-job-title', {
	render: JobTitlePanel,
} );
