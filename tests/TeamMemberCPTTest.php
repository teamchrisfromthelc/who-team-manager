<?php
/**
 * Team Member CPT tests.
 *
 * @package WhoTeamManager
 */

use WhoTeamManager\Team_Member_CPT;

/**
 * Test the Team_Member_CPT class.
 */
class TeamMemberCPTTest extends WP_UnitTestCase {

	/**
	 * Test that the custom post type is registered.
	 */
	public function test_post_type_registered(): void {
		$this->assertTrue( post_type_exists( Team_Member_CPT::POST_TYPE ) );
	}

	/**
	 * Test post type supports the correct features.
	 */
	public function test_post_type_supports(): void {
		$this->assertTrue( post_type_supports( Team_Member_CPT::POST_TYPE, 'title' ) );
		$this->assertTrue( post_type_supports( Team_Member_CPT::POST_TYPE, 'editor' ) );
		$this->assertTrue( post_type_supports( Team_Member_CPT::POST_TYPE, 'thumbnail' ) );
		$this->assertTrue( post_type_supports( Team_Member_CPT::POST_TYPE, 'custom-fields' ) );
	}

	/**
	 * Test post type is shown in REST API.
	 */
	public function test_post_type_show_in_rest(): void {
		$post_type = get_post_type_object( Team_Member_CPT::POST_TYPE );
		$this->assertNotNull( $post_type );
		$this->assertTrue( $post_type->show_in_rest );
	}

	/**
	 * Test post type is not publicly queryable.
	 */
	public function test_post_type_not_publicly_queryable(): void {
		$post_type = get_post_type_object( Team_Member_CPT::POST_TYPE );
		$this->assertNotNull( $post_type );
		$this->assertFalse( $post_type->publicly_queryable );
	}

	/**
	 * Test post type has correct menu icon.
	 */
	public function test_post_type_menu_icon(): void {
		$post_type = get_post_type_object( Team_Member_CPT::POST_TYPE );
		$this->assertNotNull( $post_type );
		$this->assertSame( 'dashicons-groups', $post_type->menu_icon );
	}

	/**
	 * Test that a team member can be created.
	 */
	public function test_create_team_member(): void {
		$post_id = $this->factory->post->create(
			array(
				'post_type'  => Team_Member_CPT::POST_TYPE,
				'post_title' => 'Jane Doe',
				'post_status' => 'publish',
			)
		);

		$this->assertIsInt( $post_id );
		$this->assertGreaterThan( 0, $post_id );
		$this->assertSame( 'Jane Doe', get_the_title( $post_id ) );
	}

	/**
	 * Test job title meta can be saved and retrieved.
	 */
	public function test_job_title_meta(): void {
		$post_id = $this->factory->post->create(
			array(
				'post_type' => Team_Member_CPT::POST_TYPE,
				'post_status' => 'publish',
			)
		);

		update_post_meta( $post_id, Team_Member_CPT::META_JOB_TITLE, 'VP of Sales' );
		$this->assertSame( 'VP of Sales', get_post_meta( $post_id, Team_Member_CPT::META_JOB_TITLE, true ) );
	}

	/**
	 * Test the meta key constant value.
	 */
	public function test_meta_key_constant(): void {
		$this->assertSame( '_who_team_job_title', Team_Member_CPT::META_JOB_TITLE );
	}

	/**
	 * Test the post type constant value.
	 */
	public function test_post_type_constant(): void {
		$this->assertSame( 'who_team_member', Team_Member_CPT::POST_TYPE );
	}
}
