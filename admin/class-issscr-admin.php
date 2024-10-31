<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/admin
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSCR_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();
	}

	/**
	 * Load the required dependencies for this class.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		/**
		 * CMB2 Plugin: The class responsible for generating custom meta fields, boxes,
		 * and posts.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2/init.php';

		/**
		 * CMB2 Plugin: The class responsible for registering CMB2's custom meta
		 * fields, boxes, and posts.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin-cmb2-plugin-meta-field-registration.php';

		/**
		 * CMB2 Plugin: The class responsible for registering CMB2's limited
		 * meta fields.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin-cmb2-plugin-limited-meta-field-registration.php';

		/**
		 * CMB2 Plugin: The class responsible for registering CMB2's settings
		 * page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin-cmb2-plugin-settings-page-registration.php';

		/**
		 * CMB2 Plugin: The class responsible for registering a custom CMB2
		 * notification field.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin-cmb2-plugin-render-notification-field.php';

		/**
		 * CMB2 Plugin: The class responsible for registering a CMB2 extension
		 * to display a switch button.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2-switch-button/cmb2-switch-button.php';

		/**
		 * CMB2 Plugin: The class responsible for registering a CMB2 extension
		 * to display meta fields in columns.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2-grid-master/Cmb2GridPlugin.php';

		/**
		 * The class responsible for displaying admin notices.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin-notices.php';

		/**
		 * The class responsible for registering the TinyMCE shortcode button.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issscr-admin-register-tinymce-shortcode-button.php';
	}

	/**
	 * On post created.
	 *
	 * @wp-hook wp_insert_post
	 */
	public function on_post_created( $post_id, $post, $update ) {
		if ( 'issslpg-template' == get_post_type( $post_id ) ) {
			add_post_meta( $post_id, '_issscr_randomizer_toggler', 'on' );
		}
	}

	/**
	 * On post updated.
	 *
	 * @wp-hook post_updated
	 */
	public function on_post_updated( $page_id, $page_after, $page_before ) {
		$cache_manager = new ISSSCR_Cache_Manager();
		$cache_manager->delete_record( $page_id );
	}

	/**
	 * Maybe Delete Cache
	 *
	 * @since    3.12.0
	 * @wp-hook    init
	 */
	public function maybe_delete_cache() {
		if ( ISSSCR_Options::get_setting( 'delete_cache', false, 'issscr_cache_settings' ) ) {
			// Set time of last cache deletion
			update_option( 'issscr_cache_deletion_time', time() );
			// Reset settings switch
			ISSSCR_Options::set_setting( 'delete_cache', false, 'issscr_cache_settings' );
		}
	}

	/**
	 * Add body classes
	 *
	 * @since    2.2.0
	 */
	public function add_body_classes( $classes ) {
		if ( ISSSCR_Helpers::is_simulated_plan() ) {
			$classes.= ' issscr-simulated-plan ';
			$classes.= ' js-issscr-simulated-plan ';
		}
		if ( ISSSCR_Helpers::is_white_labeled() ) {
			$classes.= ' issscr-white-labeled ';
			$classes.= ' js-issscr-white-labeled ';
		}
		if ( ISSSCR_Helpers::is_randomizer_page() ) {
			$classes.= ' issscr-randomizer-page ';
		}

		return $classes;
	}

	/**
	 * Add Template Page Block Editor Support.
	 *
	 * @wp-hook issslpg_template_page_args
	 */
	public function add_template_page_block_editor_support( $args ) {
		if ( ISSSCR_Options::get_setting( 'support_issslpg_template_block_editor', false ) ) {
			return array_merge( $args, [
				'show_in_rest' => true,
				'supports' => array('editor')
			] );
		}

		return $args;
	}

	/**
	 * Register CMB2 custom fields.
	 *
	 * @wp-hook cmb2_admin_init
	 */
	public function register_cmb2_custom_fields() {
		ISSSLPG_Admin_CMB2_Plugin_Render_Notification_Field::init();
	}

	/**
	 * Register admin notices
	 *
	 * @since    1.0.0
	 */
	public function register_admin_notices() {
		ISSSCR_Admin_Notices::edit_page_update_notice();
		ISSSCR_Admin_Notices::edit_page_plan_upgrade_notice();
		ISSSCR_Admin_Notices::settings_plan_upgrade_notice();
		ISSSCR_Admin_Notices::documentation_notice();
	}

	/**
	 * Register CMB2 Meta Fields
	 *
	 * @since    1.0.0
	 */
	public function register_cmb2_meta_fields() {
		new ISSSCR_Admin_CMB2_Plugin_Limited_Meta_Field_Registration();
		new ISSSCR_Admin_CMB2_Plugin_Meta_Field_Registration();
	}

	/**
	 * Register CMB2 Settings Page
	 *
	 * @since    1.0.0
	 */
	public function register_cmb2_settings_page() {
		new ISSSCR_Admin_CMB2_Plugin_Settings_Page_Registration();
	}

	/**
	 * Hide default WP content editor on pages where randomization isn't active.
	 *
	 * @since    1.0.0
	 */
	public function hide_content_editor() {
		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );
		$is_randomizer_page  = ISSSCR_Helpers::is_randomizer_page( $post_id );
		$hide_default_editor = ISSSCR_Options::get_setting( "hide_{$post_type}_default_editor", true );
		if ( $is_randomizer_page && $hide_default_editor ) {
			remove_post_type_support( get_post_type( $post_id ), 'editor' );
		}
	}

	/**
	 * Add links to the plugin description.
	 *
	 * @wp-hook plugin_action_links
	 */
	public function add_action_links( $links ) {
		if ( ! ISSSCR_Helpers::is_white_labeled() && ! ISSSCR_Helpers::is_simulated_plan() && ISSSCR_Helpers::is_plan( 'basic' ) ) {
			$url = admin_url( 'admin.php?page=issscr_settings-pricing' );
			$link = "<a href='{$url}'><b>" . __( 'Buy License' ) . '</b></a>';
			array_unshift( $links, $link );
		}
		return $links;
	}

	/**
	 * Register TinyMCE Shortcode Button
	 *
	 * @since    3.8.4
	 */
	public function register_tinymce_shortcode_button() {
		new ISSSCR_Admin_Register_TinyMCE_Shortcode_Button();
	}

	/**
	 * Clean up pasted text in TinyMCE Editor on Randomizer pages.
	 * Source: https://jonathannicol.com/blog/2015/02/19/clean-pasted-text-in-wordpress/
	 *
	 * @since    1.0.0
	 */
	public function tinymce_strip_html_tags( $in ) {
		if ( ISSSCR_Helpers::is_randomizer_page() ) {
			$in['paste_preprocess'] = "function(plugin, args){
			    // Strip all HTML tags except those we have whitelisted
			    var whitelist = 'p,span,b,strong,i,em,h3,h4,h5,h6,ul,li,ol';
			    var stripped = jQuery('<div>' + args.content + '</div>');
			    var els = stripped.find('*').not(whitelist);
			    for (var i = els.length - 1; i >= 0; i--) {
			        var e = els[i];
			        jQuery(e).replaceWith(e.innerHTML);
			    }
			    // Strip all class and id attributes
			    stripped.find('*').removeAttr('id').removeAttr('class');
			    // Return the clean HTML
			    args.content = stripped.html();
			}";
		}
		return $in;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ISSSCR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ISSSCR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/issscr-admin.css',
			array(),
			$this->version,
			'all'
		);

		// Enqueue LightGallery plugin
		wp_enqueue_style(
			'iss_lightgallery',
			plugin_dir_url( __FILE__ ) . 'plugins/lightgallery/dist/css/lightgallery.min.css',
			array(),
			$this->version,
			'all'
		);

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ISSSCR_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ISSSCR_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/issscr-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);

		// Enqueue LightGallery plugin
		wp_enqueue_script(
			'iss_lightgallery',
			plugin_dir_url( __FILE__ ) . 'plugins/lightgallery/dist/js/lightgallery.min.js',
			array( 'jquery', $this->plugin_name ),
			$this->version,
			false
		);

		// Enqueue LightGallery video plugin
		wp_enqueue_script(
			'iss_lightgallery-all',
			plugin_dir_url( __FILE__ ) . 'plugins/lightgallery/dist/js/lightgallery-all.min.js',
			array( 'jquery', $this->plugin_name, 'iss_lightgallery' ),
			$this->version,
			false
		);

	}

}
