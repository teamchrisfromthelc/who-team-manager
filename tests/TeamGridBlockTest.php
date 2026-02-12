<?php
/**
 * Team Grid Block tests.
 *
 * @package WhoTeamManager
 */

use WhoTeamManager\Team_Grid_Block;
use WhoTeamManager\Team_Member_CPT;

/**
 * Test the Team_Grid_Block class.
 */
class TeamGridBlockTest extends WP_UnitTestCase {

	/**
	 * Block instance.
	 *
	 * @var Team_Grid_Block
	 */
	private $block;

	/**
	 * Set up test fixtures.
	 */
	public function set_up(): void {
		parent::set_up();
		$this->block = new Team_Grid_Block();
	}

	/**
	 * Test render returns empty string when no members exist.
	 */
	public function test_render_empty_when_no_members(): void {
		$output = $this->block->render( array( 'columns' => 3, 'memberIds' => array() ) );
		$this->assertSame( '', $output );
	}

	/**
	 * Test render shows team members.
	 */
	public function test_render_shows_members(): void {
		$post_id = $this->factory->post->create(
			array(
				'post_type'    => Team_Member_CPT::POST_TYPE,
				'post_title'   => 'John Smith',
				'post_content' => 'A great developer.',
				'post_status'  => 'publish',
			)
		);

		update_post_meta( $post_id, Team_Member_CPT::META_JOB_TITLE, 'Lead Developer' );

		$output = $this->block->render( array( 'columns' => 3, 'memberIds' => array() ) );

		$this->assertStringContainsString( 'who-team-grid', $output );
		$this->assertStringContainsString( 'John Smith', $output );
		$this->assertStringContainsString( 'Lead Developer', $output );
		$this->assertStringContainsString( 'A great developer.', $output );
		$this->assertStringContainsString( '--who-team-columns: 3', $output );
	}

	/**
	 * Test render respects memberIds order.
	 */
	public function test_render_respects_member_order(): void {
		$alice_id = $this->factory->post->create(
			array(
				'post_type'   => Team_Member_CPT::POST_TYPE,
				'post_title'  => 'Alice',
				'post_status' => 'publish',
			)
		);

		$bob_id = $this->factory->post->create(
			array(
				'post_type'   => Team_Member_CPT::POST_TYPE,
				'post_title'  => 'Bob',
				'post_status' => 'publish',
			)
		);

		// Bob before Alice.
		$output = $this->block->render(
			array(
				'columns'   => 2,
				'memberIds' => array( $bob_id, $alice_id ),
			)
		);

		$bob_pos   = strpos( $output, 'Bob' );
		$alice_pos = strpos( $output, 'Alice' );

		$this->assertNotFalse( $bob_pos );
		$this->assertNotFalse( $alice_pos );
		$this->assertLessThan( $alice_pos, $bob_pos );
	}

	/**
	 * Test render clamps columns to valid range.
	 */
	public function test_render_clamps_columns(): void {
		$post_id = $this->factory->post->create(
			array(
				'post_type'   => Team_Member_CPT::POST_TYPE,
				'post_title'  => 'Test',
				'post_status' => 'publish',
			)
		);

		// Too high.
		$output = $this->block->render( array( 'columns' => 10, 'memberIds' => array( $post_id ) ) );
		$this->assertStringContainsString( '--who-team-columns: 6', $output );

		// Too low.
		$output = $this->block->render( array( 'columns' => 0, 'memberIds' => array( $post_id ) ) );
		$this->assertStringContainsString( '--who-team-columns: 1', $output );
	}

	/**
	 * Test render skips draft posts in memberIds.
	 */
	public function test_render_skips_draft_members(): void {
		$draft_id = $this->factory->post->create(
			array(
				'post_type'   => Team_Member_CPT::POST_TYPE,
				'post_title'  => 'Draft Member',
				'post_status' => 'draft',
			)
		);

		$published_id = $this->factory->post->create(
			array(
				'post_type'   => Team_Member_CPT::POST_TYPE,
				'post_title'  => 'Published Member',
				'post_status' => 'publish',
			)
		);

		$output = $this->block->render(
			array(
				'columns'   => 3,
				'memberIds' => array( $draft_id, $published_id ),
			)
		);

		$this->assertStringNotContainsString( 'Draft Member', $output );
		$this->assertStringContainsString( 'Published Member', $output );
	}

	/**
	 * Test render skips IDs for non-team-member post types.
	 */
	public function test_render_skips_non_team_member_posts(): void {
		$page_id = $this->factory->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => 'A Regular Page',
				'post_status' => 'publish',
			)
		);

		$member_id = $this->factory->post->create(
			array(
				'post_type'   => Team_Member_CPT::POST_TYPE,
				'post_title'  => 'Team Person',
				'post_status' => 'publish',
			)
		);

		$output = $this->block->render(
			array(
				'columns'   => 3,
				'memberIds' => array( $page_id, $member_id ),
			)
		);

		$this->assertStringNotContainsString( 'A Regular Page', $output );
		$this->assertStringContainsString( 'Team Person', $output );
	}
}
