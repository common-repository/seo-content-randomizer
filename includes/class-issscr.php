<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ISSSCR
 * @subpackage ISSSCR/includes
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSCR {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ISSSCR_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * An instance of the plugins public class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ISSSCR_Public    $public_plugin    The public-facing functionality of the plugin.
	 */
	protected $public_plugin;

	/**
	 * An instance of the plugins admin class.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ISSSCR_Admin    $admin_plugin    The admin-specific functionality of the plugin.
	 */
	protected $admin_plugin;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'ISSSCR_VERSION' ) ) {
			$this->version = ISSSCR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'issscr';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ISSSCR_Loader. Orchestrates the hooks of the plugin.
	 * - ISSSCR_i18n. Defines internationalization functionality.
	 * - ISSSCR_Admin. Defines all hooks for the admin area.
	 * - ISSSCR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of
		 * the core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the
		 * admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the
		 * public-facing side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public.php';

		/**
		 * The class responsible for defining all helper methods.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-helpers.php';

		/**
		 * The class responsible for defining all Options API helper methods.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-options.php';

		/**
		 * This class holds methods used for handling meta data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-meta-data.php';

		/**
		 * The class responsible for defining all functions that are
		 * responsible for the cache manager.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-cache-manager.php';

		/**
		 * The class responsible for defining Array helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-array-helpers.php';

		/**
		 * The class responsible for defining String helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issscr-string-helpers.php';


		$this->loader        = new ISSSCR_Loader();
		$this->public_plugin = new ISSSCR_Public( $this->plugin_name, $this->version );
		$this->admin_plugin  = new ISSSCR_Admin( $this->plugin_name, $this->version );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ISSSCR_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new ISSSCR_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_basename = ISSSCR_BASENAME;
		$this->loader->add_action( 'init', $this->admin_plugin, 'maybe_delete_cache', 10, 3 );
		$this->loader->add_action( 'issslpg_template_page_args', $this->admin_plugin, 'add_template_page_block_editor_support', 20, 1 );
		$this->loader->add_action( 'post_updated', $this->admin_plugin, 'on_post_updated',  9999, 3 );
		$this->loader->add_action( 'wp_insert_post', $this->admin_plugin, 'on_post_created',  9999, 3 );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_plugin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin_plugin, 'enqueue_scripts' );
		$this->loader->add_action( 'cmb2_admin_init', $this->admin_plugin, 'register_cmb2_meta_fields', 1 );
		$this->loader->add_action( 'cmb2_admin_init', $this->admin_plugin, 'register_cmb2_settings_page', 9998 );
		$this->loader->add_action( 'cmb2_admin_init', $this->admin_plugin, 'register_cmb2_custom_fields' );
		$this->loader->add_action( 'admin_init', $this->admin_plugin, 'register_tinymce_shortcode_button' );
		$this->loader->add_action( 'admin_head', $this->admin_plugin, 'hide_content_editor' );
		$this->loader->add_action( 'admin_notices', $this->admin_plugin, 'register_admin_notices' );
		$this->loader->add_action( 'admin_body_class', $this->admin_plugin, 'add_body_classes' );
//		$this->loader->add_filter( 'plugin_action_links', $this->admin_plugin, 'add_action_links' );
		$this->loader->add_filter( 'tiny_mce_before_init', $this->admin_plugin, 'tinymce_strip_html_tags' );
		$this->loader->add_filter( "plugin_action_links_{$plugin_basename}", $this->admin_plugin, 'add_action_links' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$this->loader->add_action( 'init', $this->public_plugin, 'register_shortcodes', 9999 );
		$this->loader->add_action( 'wp', $this->public_plugin, 'maybe_delete_post_cache' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public_plugin, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->public_plugin, 'enqueue_scripts' );
		$this->loader->add_filter( 'the_content', $this->public_plugin, 'modify_content', 10, 1 );
		$this->loader->add_filter( 'wp_head', $this->public_plugin, 'add_meta_description' );
		$this->loader->add_filter( 'wp_head', $this->public_plugin, 'maybe_add_schema_data', 100, 0 );
		$this->loader->add_filter( 'wp_footer', $this->public_plugin, 'save_cache_record', 9999 );
		add_filter( 'widget_text', 'do_shortcode' ); // Activate shortcodes in widgets
		add_filter( 'the_excerpt', 'do_shortcode' ); // Activate shortcodes in excerpts
		add_filter( 'get_the_excerpt', 'do_shortcode' ); // Activate shortcodes in excerpts
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ISSSCR_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
