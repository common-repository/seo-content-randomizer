<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://intellasoftplugins.com/
 * @since             1.0.0
 * @package           ISSSCR
 *
 * @wordpress-plugin
 * Plugin Name:       IntellaSoft SEO Content Randomizer
 * Plugin URI:        https://intellasoftplugins.com/
 * Description:       Write multiple versions of a page’s content that will be randomly selected each time the page is loaded. This also works with images and keywords.
 * Version:           3.29.1
 * Author:            IntellaSoft Solutions
 * Author URI:        https://intellasoftplugins.com/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       issscr
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Freemius SDK access.
 */
require_once plugin_dir_path( __FILE__ ) . 'freemius.php';
issscr_fs()->add_action( 'after_uninstall', 'issscr_fs_uninstall_cleanup' );

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ISSSCR_VERSION', '3.29.1' );
define( 'ISSSCR_BASENAME', plugin_basename(__FILE__) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-issscr-activator.php
 */
function activate_issscr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-issscr-activator.php';
	ISSSCR_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_issscr' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-issscr-deactivator.php
 */
function deactivate_issscr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-issscr-deactivator.php';
	ISSSCR_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_issscr' );

/**
 * The code that runs after plugin update.
 * This action is documented in includes/class-issslpg-updater.php
 */
function update_issscr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-issscr-updater.php';
	new ISSSCR_Updater;
}

add_action( 'init', 'update_issscr', 100, 0 );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-issscr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_issscr() {
	$plugin = new ISSSCR();
	$plugin->run();
}
run_issscr();
