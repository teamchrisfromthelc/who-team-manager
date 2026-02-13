<?php
/**
 * Team Grid block registration and server-side rendering.
 *
 * @package WhoTeamManager
 */

namespace WhoTeamManager;

defined( 'ABSPATH' ) || exit;

/**
 * Registers the Team Grid block and handles server-side rendering.
 */
class Team_Grid_Block {

	/**
	 * Register the block.
	 */
	public function register(): void {
		$block_path = WHO_TEAM_MANAGER_DIR . 'build';

		if ( ! file_exists( $block_path . '/block.json' ) ) {
			return;
		}

		register_block_type(
			$block_path,
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Server-side render callback for the block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string Rendered HTML.
	 */
	public function render( array $attributes ): string {
		$columns    = isset( $attributes['columns'] ) ? absint( $attributes['columns'] ) : 3;
		$columns    = max( 1, min( 6, $columns ) );
		$member_ids = ! empty( $attributes['memberIds'] ) ? array_map( 'absint', $attributes['memberIds'] ) : array();

		if ( empty( $member_ids ) ) {
			$query = new \WP_Query(
				array(
					'post_type'      => Team_Member_CPT::POST_TYPE,
					'posts_per_page' => 100,
					'post_status'    => 'publish',
					'orderby'        => 'title',
					'order'          => 'ASC',
					'fields'         => 'ids',
				)
			);

			$member_ids = $query->posts;
		}

		if ( empty( $member_ids ) ) {
			return '';
		}

		$wrapper_attributes = get_block_wrapper_attributes(
			array(
				'class' => 'who-team-grid',
				'style' => sprintf(
					'--who-team-columns: %d;',
					$columns
				),
			)
		);

		$output = sprintf( '<div %s>', $wrapper_attributes );

		foreach ( $member_ids as $member_id ) {
			$member_id = absint( $member_id );
			$post      = get_post( $member_id );

			if ( ! $post ) {
				continue;
			}

			if ( Team_Member_CPT::POST_TYPE !== $post->post_type ) {
				continue;
			}

			if ( 'publish' !== $post->post_status ) {
				continue;
			}

			$output .= $this->render_member_card( $post );
		}

		return $output . '</div>';
	}

	/**
	 * Render a single team member card.
	 *
	 * @param \WP_Post $post The team member post.
	 * @return string Card HTML.
	 */
	private function render_member_card( \WP_Post $post ): string {
		$name      = esc_html( get_the_title( $post ) );
		$job_title = esc_html( get_post_meta( $post->ID, Team_Member_CPT::META_JOB_TITLE, true ) );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.
		$description = wp_kses_post( apply_filters( 'the_content', $post->post_content ) );
		$photo       = get_the_post_thumbnail(
			$post,
			'medium',
			array( 'class' => 'who-team-member__photo' )
		);

		$card  = '<div class="who-team-member">';
		$card .= '<div class="who-team-member__image">' . $photo . '</div>';
		$card .= '<div class="who-team-member__info">';
		$card .= '<h3 class="who-team-member__name">' . $name . '</h3>';

		if ( $job_title ) {
			$card .= '<p class="who-team-member__title">' . $job_title . '</p>';
		}

		if ( $description ) {
			$card .= '<div class="who-team-member__description">' . $description . '</div>';
		}

		$card .= '</div>';

		return $card . '</div>';
	}
}
