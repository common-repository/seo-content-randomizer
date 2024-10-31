<?php



class ISSSCR_Admin_Register_TinyMCE_Shortcode_Button {

	function __construct() {

		// Check user permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Check if we can get post ID
		if ( ! isset( $_GET['post'] ) || ! isset( $_GET['action'] ) ) {
			return;
		}

		// Check if we're in the admin panel and randomization is enabled
		$is_randomizer_page = get_post_meta( $_GET['post'], '_issscr_randomizer_toggler', true );
		if ( ! is_admin() || $_GET['action'] != 'edit' ||  ! $is_randomizer_page ) {
			return;
		}

		add_action( 'admin_head', array( &$this, 'add_mce_button' ) );
		add_action( 'admin_head', array( &$this, 'localize_script' ) );
	}

	function add_mce_button() {

		// Check if WYSIWYG editor is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( &$this, 'add_mce_plugin' ) );
			add_filter( 'mce_buttons', array( &$this, 'register_mce_button' ) );
		}
	}

	function add_mce_plugin( $plugin_array ) {
		$plugin_array['issscr_tinymce_shortcode_button'] = plugins_url( 'js/issscr-admin-tinymce.js', __FILE__ );
		return $plugin_array;
	}

	function register_mce_button( $buttons ) {
		array_push( $buttons, 'issscr_tinymce_shortcode_button' );
		return $buttons;
	}

	function localize_script() {
		$post_id   = get_the_ID();
		$post_type = get_post_type( $post_id );
		$company_name_exists         = 'false';
		$content_panel_active        = 'false';
		$static_content_panel_active = 'false';

		if ( ISSSCR_Options::get_setting( 'company_name', false, 'iss_company_info_settings' ) ) {
			$company_name_exists = 'true';
		}
		if ( ISSSCR_Options::get_setting( "show_{$post_type}_content_panel", true ) ) {
			$content_panel_active = 'true';
		}
		if ( ISSSCR_Options::get_setting( "show_{$post_type}_static_content_panel", true ) ) {
			$static_content_panel_active = 'true';
		}
		?>
			<script type="text/javascript">
				var issscr_shortcode_data = '<?php echo json_encode( ISSSCR_Public_Shortcode_Helpers::get_dynamic_shortcode_data( false ) ); ?>';
				var issscr_active_shortcodes = {
					company_name: <?php echo $company_name_exists; ?>,
					content: <?php echo $content_panel_active; ?>,
					static_content: <?php echo $static_content_panel_active; ?>,
				};
			</script>
		<?php
	}

}