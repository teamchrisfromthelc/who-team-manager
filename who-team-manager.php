<?php
/**
 * Plugin Name: WHO Team Manager
 * Plugin URI:  https://github.com/chrisfromthelc/who-team-manager
 * Description: A simple custom post type for team members with a configurable grid block.
 * Version:     1.0.0
 * Author:      Christopher Smith
 * Author URI:  https://chriswsmith.info
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: who-team-manager
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Update URI:  https://github.com/chrisfromthelc/who-team-manager
 *
 * @package WhoTeamManager
 */

defined( 'ABSPATH' ) || exit;

define( 'WHO_TEAM_MANAGER_VERSION', '1.0.0' );
define( 'WHO_TEAM_MANAGER_FILE', __FILE__ );
define( 'WHO_TEAM_MANAGER_DIR', plugin_dir_path( __FILE__ ) );
define( 'WHO_TEAM_MANAGER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoloader for WhoTeamManager classes.
 *
 * Maps WhoTeamManager namespace to the includes/ directory using WordPress
 * file naming conventions (class-{name}.php, interface-{name}.php).
 *
 * @param string $class_name The fully qualified class name.
 */
function who_team_manager_autoloader( $class_name ): void {
	$namespace = 'WhoTeamManager\\';

	if ( 0 !== strpos( $class_name, $namespace ) ) {
		return;
	}

	$relative_class = substr( $class_name, strlen( $namespace ) );
	$parts          = explode( '\\', $relative_class );
	$class_file     = array_pop( $parts );

	// Build directory path from namespace parts.
	$path = WHO_TEAM_MANAGER_DIR . 'includes/';
	foreach ( $parts as $part ) {
		$path .= strtolower( str_replace( '_', '-', $part ) ) . '/';
	}

	// Determine file prefix based on type.
	$reflection_name = strtolower( str_replace( '_', '-', $class_file ) );

	// Check for interface first, then class.
	$interface_file = $path . 'interface-' . $reflection_name . '.php';
	$class_file     = $path . 'class-' . $reflection_name . '.php';

	if ( file_exists( $interface_file ) ) {
		require_once $interface_file;
	} elseif ( file_exists( $class_file ) ) {
		require_once $class_file;
	}
}
spl_autoload_register( 'who_team_manager_autoloader' );

/**
 * Get the main plugin instance.
 */
function who_team_manager(): \WhoTeamManager\Plugin {
	return \WhoTeamManager\Plugin::get_instance();
}

// Initialize the plugin.
who_team_manager();
