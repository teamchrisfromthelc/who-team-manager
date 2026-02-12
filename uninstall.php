<?php
/**
 * Uninstall handler for WHO Team Manager.
 *
 * Cleans up plugin data when the plugin is deleted through the WordPress admin.
 *
 * @package WhoTeamManager
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete transients.
delete_transient( 'wtm_github_update' );
delete_transient( 'wtm_github_update_failed' );

// Delete all team member posts and their meta.
$posts = get_posts(
	array(
		'post_type'      => 'who_team_member',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	)
);

foreach ( $posts as $post_id ) {
	wp_delete_post( $post_id, true );
}
