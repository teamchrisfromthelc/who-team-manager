<?php
/**
 * Team Member custom post type registration.
 *
 * @package WhoTeamManager
 */

namespace WhoTeamManager;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and manages the Team Member custom post type.
 */
class Team_Member_CPT {

	/**
	 * Post type slug.
	 */
	public const POST_TYPE = 'who_team_member';

	/**
	 * Job title meta key.
	 */
	public const META_JOB_TITLE = '_who_team_job_title';

	/**
	 * Register the custom post type, meta, and editor assets.
	 */
	public function register(): void {
		$this->register_post_type();
		$this->register_meta();
		$this->register_editor_assets();
	}

	/**
	 * Register the team_member post type.
	 */
	private function register_post_type(): void {
		$labels = array(
			'name'                  => _x( 'Team Members', 'Post type general name', 'who-team-manager' ),
			'singular_name'         => _x( 'Team Member', 'Post type singular name', 'who-team-manager' ),
			'menu_name'             => _x( 'Team Members', 'Admin Menu text', 'who-team-manager' ),
			'name_admin_bar'        => _x( 'Team Member', 'Add New on Toolbar', 'who-team-manager' ),
			'add_new'               => __( 'Add New', 'who-team-manager' ),
			'add_new_item'          => __( 'Add New Team Member', 'who-team-manager' ),
			'new_item'              => __( 'New Team Member', 'who-team-manager' ),
			'edit_item'             => __( 'Edit Team Member', 'who-team-manager' ),
			'view_item'             => __( 'View Team Member', 'who-team-manager' ),
			'all_items'             => __( 'All Team Members', 'who-team-manager' ),
			'search_items'          => __( 'Search Team Members', 'who-team-manager' ),
			'not_found'             => __( 'No team members found.', 'who-team-manager' ),
			'not_found_in_trash'    => __( 'No team members found in Trash.', 'who-team-manager' ),
			'featured_image'        => _x( 'Team Member Photo', 'Overrides the "Featured Image" phrase', 'who-team-manager' ),
			'set_featured_image'    => _x( 'Set team member photo', 'Overrides the "Set featured image" phrase', 'who-team-manager' ),
			'remove_featured_image' => _x( 'Remove team member photo', 'Overrides the "Remove featured image" phrase', 'who-team-manager' ),
			'use_featured_image'    => _x( 'Use as team member photo', 'Overrides the "Use as featured image" phrase', 'who-team-manager' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-groups',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'has_archive'        => false,
			'rewrite'            => false,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Register the editor sidebar script for the Job Title panel.
	 */
	private function register_editor_assets(): void {
		$asset_file = WHO_TEAM_MANAGER_DIR . 'build/plugins/job-title-panel.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_register_script(
			'who-team-job-title-panel',
			WHO_TEAM_MANAGER_URL . 'build/plugins/job-title-panel.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		add_action(
			'enqueue_block_editor_assets',
			function () {
				$screen = get_current_screen();

				if ( $screen && self::POST_TYPE === $screen->post_type ) {
					wp_enqueue_script( 'who-team-job-title-panel' );
				}
			}
		);
	}

	/**
	 * Register post meta for the team member.
	 */
	private function register_meta(): void {
		register_post_meta(
			self::POST_TYPE,
			self::META_JOB_TITLE,
			array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
