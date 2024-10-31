<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://intellasoftplugins.com
 * @since      1.0.0
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/public
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSCR_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
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
		 * The class responsible for providing randomized content.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public-content.php';

		/**
		 * The class responsible for defining filters to modify page meta
		 * information like title and meta-description.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public-page-meta.php';

		/**
		 * The class responsible for defining all functions that are
		 * responsible for randomizing data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public-randomization.php';

		/**
		 * The class responsible for defining all shortcodes.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public-shortcodes.php';

		/**
		 * The class responsible for defining shortcode helpers.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public-shortcode-helpers.php';

		/**
		 * The class responsible for schema data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issscr-public-schema.php';

	}

	/**
	 * Register the shortcodes.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function register_shortcodes() {
		$shortcodes = new ISSSCR_Public_Shortcodes();

		// Register Default Shortcodes
		add_shortcode( 'iss_content',        array( $shortcodes, 'content' ) );
		add_shortcode( 'iss_static_content', array( $shortcodes, 'static_content' ) );
		add_shortcode( 'iss_company',        array( $shortcodes, 'company' ) );
		add_shortcode( 'iss_company_name',   array( $shortcodes, 'company' ) );

		// Register Dynamic Panel Shortcodes
		$dynamic_shortcode_data = ISSSCR_Public_Shortcode_Helpers::get_dynamic_shortcode_data();
		foreach ( $dynamic_shortcode_data as $shortcode_types ) {
			foreach ( $shortcode_types as $shortcode_tag => $shortcode_data ) {
				add_shortcode( $shortcode_tag, array( $shortcodes, $shortcode_data['callback'] ) );
			}
		}
	}

	/**
	 * Add meta description to randomizer pages.
	 *
	 * @wp-hook    wp_head
	 * @access   public
	 */
	public function add_meta_description() {
		$page_meta = new ISSSCR_Public_Page_Meta();
		$page_meta->meta_description();
	}

	/**
	 * Modify the content. This function can be called by using the the_content
	 * hook.
	 *
	 * @param string $content Content of the current post.
	 * @return string Modified content of the current post.
	 */
	public function modify_content( $content ) {
        return ISSSCR_Public_Content::get_content( $content );
	}

	/**
	 * Maybe Add Schema Data.
	 *
	 * @wp-hook wp_head
	 */
	public function maybe_add_schema_data() {
		$content = ISSSCR_Public_Content::get_content( false, false );
		if ( strpos( $content, '[iss_faq_definition_list' ) !== false ) {
			echo ISSSCR_Public_Schema_Data::get_definition_list_faq_schema();
		}
	}

	/**
	 * Maybe delete post cache.
	 *
	 * @since    3.12.0
	 * @wp-hook    init
	 * @access   public
	 */
	public function maybe_delete_post_cache() {
		$post_id = get_the_ID();
		$cache_manager = new ISSSCR_Cache_Manager();
		$cache_manager->load_record( $post_id );
		$cache_deletion_timestamp = get_option( 'issscr_cache_deletion_time' );

		if ( is_admin()
		     || ! $cache_manager->is_cache_enabled()
		     || ! $cache_manager->has_record()
		     || ! $cache_deletion_timestamp
		) {
			return;
		}

		$cache_deletion_timestamp = (int)$cache_deletion_timestamp;

		$cache_record_expiration_timestamp = (int)$cache_manager->get_record_expiration_timestamp();
		$cache_expiration_time = (int)$cache_manager->get_cache_expiration_time();

//		$result = $cache_record_expiration_timestamp - $cache_expiration_time;
//		$bool_result = $result < $cache_deletion_timestamp;
//		$bool_result = $bool_result ? 'true' : 'false';
//		error_log( "cache_deletion_timestamp - cache_record_expiration_timestamp = $result < cache_deletion_timestamp = {$bool_result}" );
//		error_log( "$cache_record_expiration_timestamp - $cache_expiration_time = $result < $cache_deletion_timestamp = {$bool_result}" );

		if ( ( $cache_record_expiration_timestamp - $cache_expiration_time ) < $cache_deletion_timestamp ) {
			$cache_manager->delete_record();
		}
	}

	/**
	 * Save cache record.
	 *
	 * @since    3.11.1
	 * @wp-hook    wp_footer
	 */
	public function save_cache_record() {
		$cache_manager = new ISSSCR_Cache_Manager();
		$cache_manager->load_record();

//		error_log( "FOOTER --------------" );
//		error_log( "NEW RECORD $post_id" );
//		error_log( print_r( ISSSCR_Cache_Manager::$new_record, true ) );
//		error_log( "MEMORY RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$memory_record, true ) );
//		error_log( "CURRENT RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$current_record, true ) );

		$cache_manager->save_new_record();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		if ( ISSSCR_Helpers::is_randomizer_page() ) {

			// Add Dashicons for definition list accordions
			wp_enqueue_style( 'dashicons' );

			wp_enqueue_style(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'css/issscr-public.css',
				array(),
				$this->version,
				'all'
			);

			// Enqueue Flexslider
			wp_enqueue_style(
				"iss_flexslider",
				plugin_dir_url( __FILE__ ) . 'plugins/flexslider/flexslider.css',
				array(),
				false
			);

		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		if ( ISSSCR_Helpers::is_randomizer_page() ) {
			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'js/issscr-public.js',
				array( 'jquery' ),
				$this->version,
				false
			);

			// Enqueue Flexslider
			wp_enqueue_script(
				"iss_flexslider",
				plugin_dir_url( __FILE__ ) . 'plugins/flexslider/jquery.flexslider-min.js',
				array( 'jquery', $this->plugin_name ),
				$this->version,
				false
			);
		}
	}

}
