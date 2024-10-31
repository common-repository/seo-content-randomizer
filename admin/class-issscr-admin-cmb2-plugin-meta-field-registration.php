<?php



class ISSSCR_Admin_CMB2_Plugin_Meta_Field_Registration {

//	public $limited_repeater_metaboxes = array();

	public function __construct() {
//		$post_type = get_post_type();

		// Register Toggler Panel
		$this->register_randomizer_page_toggler_panel();

		// Register Panels by Post Type
		$post_types = ISSSCR_Helpers::get_active_post_types();
		foreach ( $post_types as $post_type ) {
			// Register Default Panels
			if ( ISSSCR_Options::get_setting( "show_{$post_type}_content_panel", true ) ) {
				$this->register_content_panel( $post_type );
			}
			if ( ISSSCR_Options::get_setting( "show_{$post_type}_static_content_panel", true ) ) {
				$this->register_static_content_panel( $post_type );
			}
			// Register Dynamic Panels
			$this->register_dynamic_content_panels( $post_type );
			$this->register_dynamic_image_panels( $post_type );
			$this->register_dynamic_keyword_panels( $post_type );
			$this->register_dynamic_phrase_panels( $post_type );
			$this->register_dynamic_list_panels( $post_type );
			$this->register_dynamic_definition_list_panels( $post_type );
			// Register Page Meta Panel
			if ( ISSSCR_Options::get_setting( "show_{$post_type}_page_meta_panel", true ) ) {
//				$this->register_page_meta_box( $post_type );
				$this->register_page_meta_panel( $post_type );
			}
		}

	}

	public function show_box( $cmb ) {
		return ISSSCR_Helpers::is_randomizer_page( $cmb->object_id() );
	}

	public function register_randomizer_page_toggler_panel() {

		$object_types = ISSSCR_Helpers::get_active_post_types();
		$context      = 'side';
		$priority     = 'default';

		$cmb = new_cmb2_box( array(
			'id'           => 'issscr_randomizer_page_toggler_panel',
			'title'        => __( 'SEO Content Randomizer', 'issscr' ),
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'closed'       => false,
		) );

		$cmb->add_field( array(
			'name' => 'Randomizer Page',
			'desc' => 'Activate and hit <b>Publish/Update</b> button to turn this page into a randomizer page.',
			'id'   => '_issscr_randomizer_toggler',
			'type' => 'switch',
		) );
	}

	public function register_content_panel( $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSCR_Helpers::get_repeater_box_rows_limit();

//		$this->limited_repeater_metaboxes[] = 'issscr_content_panel';
		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_content_panel",
				'title'        => __( 'SEO Content Randomizer: Content', 'issscr' ),
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
				'id'          => "_issscr_{$post_type}_content",
				'type'        => 'group',
				'desc'        => 'A Content Block will be randomly selected to serve as content for your page.<br>You can also use the shortcode <code>[iss_content]</code> to display a randomly selected content block anywhere on the page.',
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => 'Content Block {#}',
						'add_button'    => 'Add Another Content Block',
						'remove_button' => 'Remove Content Block',
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
				'show_on_cb'   => array( $this, 'show_box' ),
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => 'Content',
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
				'options' => array(
						'wpautop'       => true,
						'media_buttons' => true,
						'editor_height' => '450',
				),
		) );

		$cmb->add_field( array(
				'name' => 'Pin Content Block',
				'desc' => "This field lets you turn off the randomization temporarily, if you want to pin and review a specific content block. Simply enter the number of the block you want to pin and the post content will only display the content with this block number. Empty the field to turn randomization back on.",
				'id'   => "_issscr_{$post_type}_pinned_content_block",
				'type' => 'text',
		) );

	}

	public function register_static_content_panel( $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSCR_Helpers::get_repeater_box_rows_limit();

//		$this->limited_repeater_metaboxes[] = 'issscr_static_content_panel';
		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_static_content_panel",
				'title'        => __( 'SEO Content Randomizer: Static Content', 'issscr' ),
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
				'id'          => "_issscr_{$post_type}_static_content",
				'type'        => 'group',
				'desc'        => 'Display a static content block wherever you want by using the shortcode <code>[iss_static_content block="1"]</code>. Pick the number of the block by entering it into the <code>block</code> parameter. You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar.',
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => 'Static Content Block {#}',
						'add_button'    => 'Add Another Static Content Block',
						'remove_button' => 'Remove Static Content Block',
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
				'show_on_cb'   => array( $this, 'show_box' ),
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => 'Static Content',
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
				'options' => array(
						'wpautop'       => true,
						'media_buttons' => true,
						'editor_height' => '450',
				),
		) );

	}

	public function register_dynamic_content_panels( $post_type ) {
		$content_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_content_panels" );
		$content_panels = ISSSCR_Helpers::reduce_array_by_dynamic_panel_limit( $content_panels );
		foreach ( $content_panels as $content_panel ) {
			$title  = $content_panel['title'];
			$handle = $content_panel['handle'];
			$this->register_dynamic_content_panel( $title, $handle, $post_type );
		}
	}

	public function register_dynamic_image_panels( $post_type ) {
		$image_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_image_panels" );
		$image_panels = ISSSCR_Helpers::reduce_array_by_dynamic_panel_limit( $image_panels );
		foreach ( $image_panels as $images_panel ) {
			$title  = $images_panel['title'];
			$handle = $images_panel['handle'];
			$this->register_dynamic_image_panel( $title, $handle, $post_type );
		}
	}

	public function register_dynamic_keyword_panels( $post_type ) {
		$keyword_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_keyword_panels" );
		$keyword_panels = ISSSCR_Helpers::reduce_array_by_dynamic_panel_limit( $keyword_panels );
		foreach ( $keyword_panels as $keyword_panel ) {
			$title  = $keyword_panel['title'];
			$handle = $keyword_panel['handle'];
			$this->register_dynamic_keyword_panel( $title, $handle, $post_type );
		}
	}

	public function register_dynamic_phrase_panels( $post_type ) {
		$phrase_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_phrase_panels" );
		$phrase_panels = ISSSCR_Helpers::reduce_array_by_dynamic_panel_limit( $phrase_panels );
		foreach ( $phrase_panels as $phrase_panel ) {
			$title  = $phrase_panel['title'];
			$handle = $phrase_panel['handle'];
			$this->register_dynamic_phrase_panel( $title, $handle, $post_type );
		}
	}

	public function register_dynamic_list_panels( $post_type ) {
		$list_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_list_panels" );
		$list_panels = ISSSCR_Helpers::reduce_array_by_dynamic_panel_limit( $list_panels );
		foreach ( $list_panels as $list_panel ) {
			$title  = $list_panel['title'];
			$handle = $list_panel['handle'];
			$this->register_dynamic_list_panel( $title, $handle, $post_type );
		}
	}

	public function register_dynamic_definition_list_panels( $post_type ) {
		$content_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_definition_list_panels" );
		$content_panels = ISSSCR_Helpers::reduce_array_by_dynamic_panel_limit( $content_panels );
		foreach ( $content_panels as $content_panel ) {
			$title  = $content_panel['title'];
			$handle = $content_panel['handle'];
			$this->register_dynamic_definition_list_panel( $title, $handle, $post_type );
		}
	}

	public function register_dynamic_content_panel( $title, $handle, $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSCR_Helpers::get_repeater_box_rows_limit();

		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_{$handle}_content_panel",
				'title'        => "SEO Content Randomizer: {$title} Content",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
				'id'          => "_issscr_{$post_type}_{$handle}_content",
				'type'        => 'group',
				'desc'        => "A randomly selected {$title} Content Block will be displayed by using the shortcode <code>[iss_{$handle}_content]</code>.<br>You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar.",
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => "{$title} Content Block {#}",
						'add_button'    => "Add Another {$title} Content Block",
						'remove_button' => "Remove {$title} Content Block",
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => "{$title} Content",
				'desc' => '',
				'id'   => 'content',
				'type' => 'wysiwyg',
		) );

		$cmb->add_field( array(
				'name' => "Pin {$title} Content Block",
				'desc' => "This field lets you turn off the randomization temporarily, if you want to pin and review a specific content block. Simply enter the number of the block you want to pin and the shortcode will only display the content with this block number. Empty the field to turn randomization back on.",
				'id'   => "_issscr_{$post_type}_{$handle}_pinned_content_block",
				'type' => 'text',
		) );
	}

	public function register_dynamic_image_panel( $title, $handle, $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSCR_Helpers::get_repeater_box_rows_limit( 2 );

		$cmb = new_cmb2_box( array(
			'id'           => "issscr_{$post_type}_{$handle}_images_panel",
			'title'        => "SEO Content Randomizer: {$title} Images",
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'show_on_cb'   => array( $this, 'show_box' ),
			'closed'       => true,
			'rows_limit'   => $rows_limit,
		) );

		$desc = "A randomly selected <b>{$title} Image</b> will be displayed by using the shortcode <code>[iss_{$handle}_image]</code>."
		        ."<br>You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar."
		        ."<br><br>You can use the shortcode <code>[iss_{$handle}_image_slider]</code> to display a randomly sorted slideshow of all {$title} Images. Add the parameter <code>auto</code> to the shortcode to have the slideshow rotate automatically."
		        ."<br><br>You can use the parameter <code>size=\"medium|large|full\"</code> on both shortcodes to determine size of the image/slider."
		        ."<br><br>You can use the parameter <code>class</code> to apply any class to the image (e.g. <code>class=\"alignleft|alignright|aligncenter\"</code>)."
		        ."<br><br>Examples:"
		        ."<br><br><code>[iss_{$handle}_image size=\"large\"]</code> - displays a randomly selected <b>large</b> image."
		        ."<br><br><code>[iss_{$handle}_image_slider auto]</code> - displays a slider that <b>automatically</b> starts rotating."
		        ."<br><br><code>[iss_{$handle}_image_slider auto size=\"large\"]</code> - displays a slider consisting of <b>large</b> sized images that <b>automatically</b> start rotating."
		        ."<br><br><code>[iss_{$handle}_image_slider size=\"medium\"]</code> - displays a slider consisting of <b>medium</b> sized images that has to be clicked on to rotating.";
		$group_id = $cmb->add_field( array(
				'id'          => "_issscr_{$post_type}_{$handle}_images",
				'type'        => 'group',
				'desc'        => $desc,
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => "{$title} Image {#}",
						'add_button'    => "Add Another {$title} Image",
						'remove_button' => "Remove {$title} Image",
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
		) );

		$cmb->add_group_field( $group_id, array(
				'name'    => "{$title} Image",
				'desc'    => '',
				'id'      => 'image',
				'type'    => 'file',
			// Optional:
				'options' => array(
						'url' => false, // Hide the text input for the url
				),
				'text'    => array(
						'add_upload_file_text' => "Add {$title} Image" // Change upload button text. Default: "Add or Upload File"
				),
			// query_args are passed to wp.media's library query.
				'query_args' => array(
					// Or only allow gif, jpg, or png images
						'type' => array(
								'image/gif',
								'image/jpeg',
								'image/png',
						),
				),
				'preview_size' => 'large', // Image size to use when previewing in the admin.
		) );

		$cmb->add_field( array(
				'name'    => 'No Duplicate Outputs',
				'desc'    => 'Activate this option if you don\'t want to see the same image being displayed twice on the same page.<br>Please make sure not to add more image shortcodes in your content than images in this panel.',
				'id'      => "_issscr_{$post_type}_{$handle}_no_duplicate_images",
				'type'    => 'switch',
				'default' => 'off',
		) );
	}

	public function register_dynamic_keyword_panel( $title, $handle, $post_type ) {

		$object_types = $post_type;
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_{$handle}_keywords_panel",
				'title'        => "SEO Content Randomizer: {$title} Keywords",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
		) );

		$field_1 = $cmb->add_field( array(
				'name' => "Singular {$title} Keywords",
				'desc' => "Enter one keyword per line.<br>Use shortcode <code>[iss_singular_{$handle}]</code> to display a randomly selected singular keyword.",
				'id'   => "_issscr_{$post_type}_singular_{$handle}_keywords",
				'type' => 'textarea',
		) );

		$field_2 = $cmb->add_field( array(
				'name' => "Plural {$title} Keywords",
				'desc' => "Enter one keyword per line.<br>Use shortcode <code>[iss_plural_{$handle}]</code> to display a randomly selected plural keyword.",
				'id'   => "_issscr_{$post_type}_plural_{$handle}_keywords",
				'type' => 'textarea',
		) );

		$field_3 = $cmb->add_field( array(
				'name' => 'No Duplicate Outputs',
				'desc' => 'Activate this option if you don\'t want to see the same keyword being displayed twice on the same page.<br>Please make sure not to add more keyword shortcodes in your content than keywords in this panel.',
				'id'   => "_issscr_{$post_type}_{$handle}_no_duplicate_keywords",
				'type' => 'switch',
		) );

		// Put fields into columns
		$cmb2Grid = new \Cmb2Grid\Grid\Cmb2Grid( $cmb );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_1, $field_2 ) );
		$row = $cmb2Grid->addRow();
		$row->addColumns( array( $field_3 ) );
	}

	public function register_dynamic_phrase_panel( $title, $handle, $post_type ) {

		$object_types = $post_type;
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_{$handle}_phrase_panel",
				'title'        => "SEO Content Randomizer: {$title} Phrases",
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
		) );

		$cmb->add_field( array(
				'name' => "{$title} Phrases",
				'desc' => "Enter one phrase per line.<br>Use shortcode <code>[iss_{$handle}_phrase]</code> to display a randomly selected phrase.",
				'id'   => "_issscr_{$post_type}_{$handle}_phrases",
				'type' => 'textarea',
		) );

		$cmb->add_field( array(
				'name' => 'No Duplicate Outputs',
				'desc' => 'Activate this option if you don\'t want to see the same phrase being displayed twice on the same page.<br>Please make sure not to add more phrase shortcodes in your content than phases in this panel.',
				'id'   => "_issscr_{$post_type}_{$handle}_no_duplicate_phrases",
				'type' => 'switch',
		) );
	}

	public function register_dynamic_list_panel( $title, $handle, $post_type ) {

		$object_types = $post_type;
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
			'id'           => "issscr_{$post_type}_{$handle}_list_panel",
			'title'        => "SEO Content Randomizer: {$title} List",
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'show_on_cb'   => array( $this, 'show_box' ),
			'closed'       => true,
		) );

		$cmb->add_field( array(
			'name' => "{$title} List",
			'desc' => "Enter one list item per line.<br>Use shortcode <code>[iss_{$handle}_list limit=\"5\"]</code> to display a list of randomly selected list items.<br>You can use the parameter <code>limit</code> to limit the amount of list items (e.g. <code>limit=\"5|10|20\"</code>).",
			'id'   => "_issscr_{$post_type}_{$handle}_list",
			'type' => 'textarea',
		) );
	}

	public function register_dynamic_definition_list_panel( $title, $handle, $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSCR_Helpers::get_repeater_box_rows_limit();

		$cmb = new_cmb2_box( array(
			'id'           => "issscr_{$post_type}_{$handle}_definition_list_panel",
			'title'        => "SEO Content Randomizer: {$title} Definition List",
			'object_types' => $object_types,
			'context'      => $context,
			'priority'     => $priority,
			'show_on_cb'   => array( $this, 'show_box' ),
			'closed'       => true,
			'rows_limit'   => $rows_limit,
		) );
		$desc = "The {$title} Definition List will be displayed by using the shortcode <code>[iss_{$handle}_definition_list]</code>. The order of the list items will be randomized."
		        ."<br>You can put the shortcode into a Content Block or into a Widget to be displayed in a sidebar."
		        ."<br><br>You can use the parameter <code>limit</code> to limit the amount of list items (e.g. <code>limit=\"5|10|20\"</code>)."
		        ."<br><br>You can use the parameter <code>accordion=\"on|off\"</code> to make the list items collapsable."
		        ."<br><br>You can use the parameter <code>htag</code> to define what heading tag should be used, if <code>accordion</code> is set to <code>off</code> (e.g. <code>htag=\"h3|h4|p\"</code>)."
		        ."<br><br>Examples:"
		        ."<br><br><code>[iss_{$handle}_definition_list limit=\"3\"]</code> - displays a definition list with <b>3</b> items."
		        ."<br><br><code>[iss_{$handle}_definition_list accordion=\"on\"]</code> - displays a collapsable definition list."
		        ."<br><br><code>[iss_{$handle}_definition_list htag=\"h3\" accordion=\"off\"]</code> - displays a definition list with <b>h3</b> headings, that doesn't collapse.";
		$group_id = $cmb->add_field( array(
			'id'          => "_issscr_{$post_type}_{$handle}_definition_list",
			'type'        => 'group',
			'desc'        => $desc,
			'repeatable'  => true,
			'options'     => array(
				'group_title'   => "{$title} Definition List Block {#}",
				'add_button'    => "Add Another {$title} Definition List Block",
				'remove_button' => "Remove {$title} Definition List Block",
				'closed'        => true,  // Repeater fields closed by default - neat & compact.
				'sortable'      => true,  // Allow changing the order of repeated groups.
			),
		) );

		$cmb->add_group_field( $group_id, array(
			'name' => __( 'Heading', 'issscr' ),
			'desc' => '',
			'id'   => 'heading',
			'type' => 'text',
		) );

		$cmb->add_group_field( $group_id, array(
			'name' => __( 'Content', 'issscr' ),
			'desc' => '',
			'id'   => 'content',
			'type' => 'wysiwyg',
		) );
	}

	public function register_page_meta_box( $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';

		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_page_meta_panel",
				'title'        => __( 'SEO Content Randomizer: Page Meta', 'issscr' ),
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
		) );

		$cmb->add_field( array(
				'name' => 'Meta Description',
				'desc' => 'Please keep the text length between 50–300 characters.',
				'id'   => "_issscr_{$post_type}_meta_description",
				'type' => 'textarea_small',
		) );
	}

	public function register_page_meta_panel( $post_type ) {

		$object_types = array( $post_type );
		$context      = 'normal';
		$priority     = 'high';
		$rows_limit   = ISSSCR_Helpers::get_repeater_box_rows_limit();

//		$this->limited_repeater_metaboxes[] = 'issscr_content_panel';
		$cmb = new_cmb2_box( array(
				'id'           => "issscr_{$post_type}_page_meta_panel",
				'title'        => __( 'SEO Content Randomizer: Meta Descriptions', 'issscr' ),
				'object_types' => $object_types,
				'context'      => $context,
				'priority'     => $priority,
				'show_on_cb'   => array( $this, 'show_box' ),
				'closed'       => true,
				'rows_limit'   => $rows_limit,
		) );

		$group_id = $cmb->add_field( array(
				'id'          => "_issscr_{$post_type}_page_meta_boxes",
				'type'        => 'group',
				'desc'        => 'A Meta Description will be randomly selected to serve as meta description for your page.',
				'repeatable'  => true,
				'options'     => array(
						'group_title'   => 'Meta Description {#}',
						'add_button'    => 'Add Another Meta Description',
						'remove_button' => 'Remove Meta Description',
						'closed'        => true,  // Repeater fields closed by default - neat & compact.
						'sortable'      => true,  // Allow changing the order of repeated groups.
				),
				'show_on_cb'   => array( $this, 'show_box' ),
		) );

		$cmb->add_group_field( $group_id, array(
				'name' => 'Meta Description',
				'desc' => '',
				'id'   => 'meta_description',
				'type' => 'textarea_small',
		) );

		$cmb->add_field( array(
				'name' => 'Pin Meta Description',
				'desc' => "This field lets you turn off the randomization temporarily, if you want to pin and review a specific content block. Simply enter the number of the block you want to pin and the post content will only display the content with this block number. Empty the field to turn randomization back on.",
				'id'   => "_issscr_{$post_type}_pinned_content_block",
				'type' => 'text',
		) );

	}

}