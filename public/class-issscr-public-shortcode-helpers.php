<?php



class ISSSCR_Public_Shortcode_Helpers {

	public function get_prefix( $atts ) {

		if ( isset( $atts['prefix'] ) && ! empty( $atts['prefix'] ) ) {
			return $atts['prefix'] . ' ';
		}

		return '';
	}

	public function add_prefix( $content, $atts ) {

		if ( ! empty ( $content ) ) {
			$prefix = $this->get_prefix( $atts );

			return $prefix . $content;
		}

		return $content;
	}

	static public function normalize_empty_atts( $atts ) {

		if ( ! empty( $atts ) ) {
			foreach ( $atts as $attribute => $value ) {
				if ( is_int( $attribute ) ) {
					$atts[ strtolower( $value ) ] = true;
					unset( $atts[ $attribute ] );
				}
			}
		}

		return $atts;
	}

	static public function get_dynamic_shortcode_data( $all_post_types = true ) {

		$tags = array();
		$shortcode_data = array();

		if ( $all_post_types ) {
			$post_types = ISSSCR_Helpers::get_active_post_types();
		} else {
			global $current_screen;
			$post_type = $current_screen->post_type;
			$post_type = get_post_type_object( $post_type );
			$post_types = [ $post_type->name ];
		}

		foreach ( $post_types as $post_type ) :

			// Content Shortcodes
			$content_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_content_panels" );
			foreach ( $content_panels as $content_panel ) {
				$title = $content_panel['title'];
				$handle = $content_panel['handle'];
				$tag = "iss_{$handle}_content";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['content'][$tag]['title']    = "{$title} Content";
					$shortcode_data['content'][$tag]['tag']      = $tag;
					$shortcode_data['content'][$tag]['callback'] = 'dynamic_content';
				}
			}

			// Image Shortcodes
			$image_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_image_panels" );
			foreach ( $image_panels as $image_panel ) {
				$title = $image_panel['title'];
				$handle = $image_panel['handle'];
				// Dynamic Image Shortcode
				$tag = "iss_{$handle}_image";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['image'][$tag]['title']    = "{$title} Image";
					$shortcode_data['image'][$tag]['tag']      = $tag;
					$shortcode_data['image'][$tag]['callback'] = 'dynamic_image';
				}
				// Dynamic Image Slider Shortcode
				$tag = "iss_{$handle}_image_slider";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['image_slider'][$tag]['title']    = "{$title} Slider";
					$shortcode_data['image_slider'][$tag]['tag']      = $tag;
					$shortcode_data['image_slider'][$tag]['callback'] = 'dynamic_image_slider';
				}
			}

			// Keyword Shortcodes
			$keyword_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_keyword_panels" );
			foreach ( $keyword_panels as $keyword_panel ) {
				$title = $keyword_panel['title'];
				$handle = $keyword_panel['handle'];
				// Dynamic Singular Keyword Shortcode
				$tag = "iss_singular_{$handle}";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['singular_keyword'][$tag]['title']    = "Singular {$title} Keyword";
					$shortcode_data['singular_keyword'][$tag]['tag']      = $tag;
					$shortcode_data['singular_keyword'][$tag]['callback'] = 'dynamic_singular_keyword';
				}
				// Dynamic Plural Keyword Shortcode
				$tag = "iss_plural_{$handle}";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['plural_keyword'][$tag]['title']    = "Plural {$title} Keyword";
					$shortcode_data['plural_keyword'][$tag]['tag']      = $tag;
					$shortcode_data['plural_keyword'][$tag]['callback'] = 'dynamic_plural_keyword';
				}
			}

			// Phrase Shortcodes
			$phrase_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_phrase_panels" );
			foreach ( $phrase_panels as $phrase_panel ) {
				$title = $phrase_panel['title'];
				$handle = $phrase_panel['handle'];
				$tag = "iss_{$handle}_phrase";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['phrase'][$tag]['title']    = "{$title} Phrase";
					$shortcode_data['phrase'][$tag]['tag']      = $tag;
					$shortcode_data['phrase'][$tag]['callback'] = 'dynamic_phrase';
				}
			}

			// List Shortcodes
			$list_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_list_panels" );
			foreach ( $list_panels as $list_panel ) {
				$title = $list_panel['title'];
				$handle = $list_panel['handle'];
				$tag = "iss_{$handle}_list";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['list'][$tag]['title']    = "{$title} List";
					$shortcode_data['list'][$tag]['tag']      = $tag;
					$shortcode_data['list'][$tag]['callback'] = 'dynamic_list';
				}
			}

			// Definition List Shortcodes
			$definition_list_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_definition_list_panels" );
			foreach ( $definition_list_panels as $definition_list_panel ) {
				$title = $definition_list_panel['title'];
				$handle = $definition_list_panel['handle'];
				$tag = "iss_{$handle}_definition_list";
				if ( ! in_array( $tag, $tags ) ) {
					$tags[] = $tag;
					$shortcode_data['definition_list'][$tag]['title']    = "{$title} Definition List";
					$shortcode_data['definition_list'][$tag]['tag']      = $tag;
					$shortcode_data['definition_list'][$tag]['callback'] = 'dynamic_definition_list';
				}
			}

		endforeach;

		return $shortcode_data;
	}

}