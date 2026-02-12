<?php
/**
 * Main plugin orchestrator.
 *
 * @package WhoTeamManager
 */

namespace WhoTeamManager;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin class.
 *
 * Handles initialization and hooking into WordPress.
 */
class Plugin {

	/**
	 * Singleton instance.
	 */
	private static ?\WhoTeamManager\Plugin $instance = null;

	/**
	 * Team member CPT instance.
	 */
	private \WhoTeamManager\Team_Member_CPT $team_member_cpt;

	/**
	 * Team grid block instance.
	 */
	private \WhoTeamManager\Team_Grid_Block $team_grid_block;

	/**
	 * GitHub updater instance.
	 *
	 * Stored to prevent garbage collection; hooks are registered in the constructor.
	 *
	 * @phpstan-ignore property.onlyWritten
	 */
	private \WhoTeamManager\GitHub_Updater $github_updater;

	/**
	 * Get the singleton instance.
	 */
	public static function get_instance(): \WhoTeamManager\Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Prevent cloning of the singleton.
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization of the singleton.
	 *
	 * @throws \RuntimeException Always.
	 */
	public function __wakeup() {
		throw new \RuntimeException( 'Cannot unserialize singleton.' );
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->team_member_cpt = new Team_Member_CPT();
		$this->team_grid_block = new Team_Grid_Block();
		$this->github_updater  = new GitHub_Updater( WHO_TEAM_MANAGER_FILE );

		$this->register_hooks();
	}

	/**
	 * Register WordPress hooks.
	 */
	private function register_hooks(): void {
		add_action( 'init', array( $this->team_member_cpt, 'register' ) );
		add_action( 'init', array( $this->team_grid_block, 'register' ) );
	}
}
