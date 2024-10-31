<?php



class ISSSCR_Admin_CMB2_Plugin_Settings_Page_Registration {

	public function __construct() {
		$this->register_settings_page();
		$this->register_company_info_settings_page();
		$this->register_cache_settings_page();
	}

	public function save_negative_switch_value( $value, $field_args, $field ) {

		if ( $value == false || $value == 'off' ) {
			return 'off';
		}

		return $value;
	}

	public function save_int( $value, $field_args, $field ) {
		return (int)$value;
	}

	/**
	 * Registers options page menu item and form.
	 */
	public function register_settings_page() {

		$cmb = new_cmb2_box( array(
			'id'           => 'issscr_settings_page',
			'tab_title'    => esc_html__( 'Main', 'issscr' ),
			'title'        => esc_html__( 'SEO Content Randomizer Settings', 'issscr' ),
			'object_types' => array( 'options-page' ),

			/*
			 * The following parameters are specific to the options-page box
			 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
			 */

			'option_key'  => 'issscr_settings',
			'tab_group'   => 'issscr_settings',
			// The option key and admin menu page slug.
			// 'icon_url'        => '', // Menu icon. Only applicable if 'parent_slug' is left empty.
			'menu_title'      => esc_html__( 'SEO Content Randomizer', 'issscr' ), // Falls back to 'title' (above).
//			'parent_slug'  => 'issscr_settings',
			// 'parent_slug' => 'options-general.php',
			// 'capability'      => 'manage_options', // Cap required to view options-page.
			// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
			// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
			// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
			// 'save_button'     => esc_html__( 'Save Theme Options', 'issscr' ), // The text for the options-page save button. Defaults to 'Save'.
			// 'disable_settings_errors' => true, // On settings pages (not options-general.php sub-pages), allows disabling.
			// 'message_cb'      => 'yourprefix_options_page_message_callback',
		) );

//		$cmb->add_field( array(
//			'name'    => esc_html__( 'Strip HTML from Pasted Text', 'issscr' ),
//			'desc'    => esc_html__( 'Activate to hide the default content editor on edit screen on Randomizer pages.', 'issscr' ),
//			'id'      => 'hide_default_editor',
//			'type'    => 'switch',
//			'default' => 'on',
//			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
//		) );

//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Global Settings', 'issscr' ),
//				'id'      => 'global_settings_title',
//				'type'    => 'title',
//		) );

//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Company Name', 'issscr' ),
//				'desc'    => __( 'Utilize the Company Name with the <code>[iss_company_name]</code> shortcode.', 'issscr' ),
//				'id'      => 'company_name',
//				'type'    => 'text',
//		) );

//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Caching Settings', 'issscr' ),
//				'id'      => 'caching_settings_title',
//				'type'    => 'title',
//		) );
//
//		$cmb->add_field( array(
//				'name'    => "Enable Page Cache",
//				'desc'    => "When enabled, randomizer shortcode output will be cached.",
//				'id'      => "cache",
//				'type'    => 'switch',
//				'default' => 'on',
//				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
//		) );
//
//		$cmb->add_field( array(
//				'name'    => "Delete Cache",
//				'desc'    => "Activating this button and hitting \"Save Changes\" will delete the cache. The button will turn back to \"off\" afterwards.",
//				'id'      => "delete_cache",
//				'type'    => 'switch',
//				'default' => 'off',
//				'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
//		) );
//
//		$cmb->add_field( array(
//				'name'    => esc_html__( 'Cache Expiration (in days)', 'issscr' ),
//				'desc'    => __( 'The number of days randomizer shortcode output should be cached.', 'issscr' ),
//				'id'      => 'cache_expiration',
//				'type'    => 'text',
//				'default' => '7',
//				'sanitization_cb' => array( $this, 'save_int' ),
//		) );


		$post_types = ISSSCR_Helpers::get_supported_post_types();
		foreach ( $post_types as $post_type ) :
			$post_type_object         = $post_type['object'];
			$post_type_default_active = $post_type['default_active'];
			$default                  = $post_type_default_active ? 'on' : 'off';

			$cmb->add_field( array(
					'name'    => "Post Type: {$post_type_object->labels->singular_name}",
					'id'      => "post_type_{$post_type_object->name}_settings_title",
					'type'    => 'title',
			) );

			$cmb->add_field( array(
					'name'    => "Enable {$post_type_object->labels->singular_name} Randomization",
					'desc'    => "Activate to be able to randomize the <b>{$post_type_object->labels->singular_name}</b> post type.",
					'id'      => "support_{$post_type_object->name}_post_type",
					'type'    => 'switch',
					'default' => $default,
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );

			if ( 'issslpg-template' == $post_type['name'] ) {
				$cmb->add_field( array(
					'name'    => "Enable Block Editor",
					'desc'    => "Activate to be enable the Block Editor. This will disable Auto Content Replacement.",
					'id'      => "support_issslpg_template_block_editor",
					'type'    => 'switch',
					'default' => 'off',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
				) );
			}

			$cmb->add_field( array(
					'name'    => esc_html__( 'Auto Content Replacement', 'issscr' ),
					'desc'    => esc_html__( 'Activate to automatically replace the content from the default content editor with a randomly selected Content Block.', 'issscr' ),
					'id'      => "{$post_type_object->name}_auto_content_replace",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Hide Default Content Editor', 'issscr' ),
					'desc'    => esc_html__( 'Activate to hide the default content editor on the edit screen on Randomizer pages.', 'issscr' ),
					'id'      => "hide_{$post_type_object->name}_default_editor",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Show Content Panel', 'issscr' ),
					'desc'    => esc_html__( 'Activate to show the content panel on the edit screen of Randomizer pages.', 'issscr' ),
					'id'      => "show_{$post_type_object->name}_content_panel",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Show Static Content Panel', 'issscr' ),
					'desc'    => esc_html__( 'Activate to show the static content panel on the edit screen of Randomizer pages.', 'issscr' ),
					'id'      => "show_{$post_type_object->name}_static_content_panel",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Show Page Meta Panel', 'issscr' ),
					'desc'    => esc_html__( 'Activate to show the page meta panel on the edit screen of Randomizer pages.', 'issscr' ),
					'id'      => "show_{$post_type_object->name}_page_meta_panel",
					'type'    => 'switch',
					'default' => 'on',
					'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Content Panels', 'issscr' ),
					'desc'    => esc_html__( 'Enter one panel name per line to create custom content panels (e.g. "Service").', 'issscr' ),
					'id'      => "post_type_{$post_type_object->name}_content_panels",
					'type'    => 'textarea_small',
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Image Panels', 'issscr' ),
					'desc'    => esc_html__( 'Enter one panel name per line to create custom image panels (e.g. "Service").', 'issscr' ),
					'id'      => "post_type_{$post_type_object->name}_image_panels",
					'type'    => 'textarea_small',
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Keyword Panels', 'issscr' ),
					'desc'    => esc_html__( 'Enter one panel name per line to create custom keyword panels (e.g. "Service").', 'issscr' ),
					'id'      => "post_type_{$post_type_object->name}_keyword_panels",
					'type'    => 'textarea_small',
			) );

			$cmb->add_field( array(
					'name'    => esc_html__( 'Phrase Panels', 'issscr' ),
					'desc'    => esc_html__( 'Enter one panel name per line to create custom phrase panels (e.g. "CTA").', 'issscr' ),
					'id'      => "post_type_{$post_type_object->name}_phrase_panels",
					'type'    => 'textarea_small',
			) );

			$definition_list_class = '';
			$definition_list_readonly = false;
			if ( ! ISSSCR_Helpers::is_lists_usage_allowed() ) {
				$note = 'To enable the <b>List</b> and <b>Definition List</b> features, please download the <b><a href="'. admin_url( 'admin.php?page=issscr_settings-addons' ) .'">Lists Add-on</a></b>.';
				if ( ISSSCR_Helpers::is_white_labeled() ) {
					$note = 'To enable the <b>List</b> and <b>Definition List</b> features, please download the <b>Lists Add-on</b>.';
				}
				$definition_list_class = 'issscr-cmb-disabled-field';
				$definition_list_readonly = true;

				$cmb->add_field( array(
					'note'    => $note,
					'id'      => "post_type_{$post_type_object->name}_enable_lists_feature_note",
					'type'    => 'notification',
				) );
			}

			$cmb->add_field( array(
				'name'    => esc_html__( 'List Panels', 'issscr' ),
				'desc'    => esc_html__( 'Enter one panel name per line to create custom list panels (e.g. "Specs").', 'issscr' ),
				'id'      => "post_type_{$post_type_object->name}_list_panels",
				'type'    => 'textarea_small',
				'classes' => $definition_list_class,
				'attributes' => array(
					'readonly' => $definition_list_readonly,
				),
			) );

			$cmb->add_field( array(
				'name'    => esc_html__( 'Definition List Panels', 'issscr' ),
				'desc'    => esc_html__( 'Enter one panel name per line to create custom definition list panels (e.g. "FAQ").', 'issscr' ),
				'id'      => "post_type_{$post_type_object->name}_definition_list_panels",
				'type'    => 'textarea_small',
				'classes' => $definition_list_class,
				'attributes' => array(
					'readonly' => $definition_list_readonly,
				),
			) );

		endforeach;

	}

	public function register_cache_settings_page() {
		$cmb = new_cmb2_box( array(
			'id'           => 'issscr_cache_settings_page',
			'title'        => esc_html__( 'SEO Content Randomizer Settings', 'issscr' ),
			'menu_title'   => esc_html__( 'Cache', 'issscr' ),
			'object_types' => array( 'options-page' ),
			'tab_title'    => 'Cache',
			'tab_group'    => 'issscr_settings',
			'option_key'   => 'issscr_cache_settings',
			'parent_slug'  => 'issscr_settings',
		) );

		$cmb->add_field( array(
			'name'    => "Enable Page Cache",
			'desc'    => "When enabled, randomizer shortcode output will be cached.",
			'id'      => "cache",
			'type'    => 'switch',
			'default' => 'on',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name'    => "Delete Cache",
			'desc'    => "Activating this button and hitting \"Save Changes\" will delete the cache. The button will turn back to \"off\" afterwards.",
			'id'      => "delete_cache",
			'type'    => 'switch',
			'default' => 'off',
			'sanitization_cb' => array( $this, 'save_negative_switch_value' ),
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Cache Expiration (in days)', 'issscr' ),
			'desc'    => __( 'The number of days randomizer shortcode output should be cached.', 'issscr' ),
			'id'      => 'cache_expiration',
			'type'    => 'text',
			'default' => '7',
			'sanitization_cb' => array( $this, 'save_int' ),
		) );
	}

	public function register_company_info_settings_page() {

		$cmb = new_cmb2_box( array(
			'id'           => 'issscr_company_info_settings_page',
			'title'        => esc_html__( 'SEO Content Randomizer Settings', 'issscr' ),
			'menu_title'   => esc_html__( 'Company Info', 'issscr' ),
			'object_types' => array( 'options-page' ),
			'tab_title'    => 'Company Info',
			'tab_group'    => 'issscr_settings',
			'option_key'   => 'iss_company_info_settings',
			'parent_slug'  => 'issscr_settings',
		) );

		$cmb->add_field( array(
			'name'    => esc_html__( 'Company Name', 'issscr' ),
			'desc'    => __( 'Utilize the Company Name with the <code>[iss_company_name]</code> shortcode.', 'issscr' ),
			'id'      => 'company_name',
			'type'    => 'text',
		) );

	}

}