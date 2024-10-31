<?php



class ISSSCR_Helpers {

	static public function is_lpg_plugin_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( 'seo-landing-page-generator/seo-landing-page-generator.php' )
		       || is_plugin_active( 'seo-landing-page-generator-premium/seo-landing-page-generator.php' );
//		return is_plugin_active( 'seo-landing-page-generator/seo-landing-page-generator.php' );
	}

	static public function is_lists_usage_allowed() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if ( self::are_simulated_addon_features_active() ) {
			return true;
		}

		return is_plugin_active( 'seo-content-randomizer-lists-premium/seo-content-randomizer-lists.php' )
		       || is_plugin_active( 'seo-content-randomizer-lists/seo-content-randomizer-lists.php' );
	}

	static public function is_plan( $plan_handle ) {
		if ( defined( 'ISSSCR_PLAN' ) ) {
			return ( ISSSCR_PLAN === $plan_handle );
		}

		return issscr_fs()->is_plan_or_trial( $plan_handle, true );
	}

	static public function is_simulated_plan() {
		return defined( 'ISSSCR_PLAN' );
	}

	static public function is_white_labeled() {
		return defined( 'ISSSCR_WHITELABEL' ) && ISSSCR_WHITELABEL;
	}

	static protected function are_simulated_addon_features_active() {
		return defined( 'ISSSCR_ACTIVATE_ADDON_FEATURES' ) && ISSSCR_ACTIVATE_ADDON_FEATURES;
	}

	static public function is_randomizer_page( $page_id = false ) {
		$page_id = $page_id ? $page_id : ISSSCR_Meta_Data::get_post_id();
		$post_type = ISSSCR_Meta_Data::get_post_type();
		$active_post_type = in_array( $post_type, self::get_active_post_types() );
		if ( ISSSCR_Options::get_setting( "support_{$post_type}_post_type", $active_post_type ) ) {
			return get_post_meta( $page_id, '_issscr_randomizer_toggler', true );
		}

		return false;
	}

	static public function get_dynamic_panel_limit( $multiplier = 1 ) {
		$row_limit = 10;

		if ( self::is_plan( 'starter' ) ) {
			$row_limit = 15;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$row_limit = 20;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$row_limit = 9999;
		}

		return $row_limit * $multiplier;
	}

	static public function get_repeater_box_rows_limit( $multiplier = 1 ) {
		$row_limit = 3;

		if ( self::is_plan( 'starter' ) ) {
			$row_limit = 10;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$row_limit = 20;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$row_limit = 9999;
		}

		return $row_limit * $multiplier;
	}

	static public function get_keyword_limit( $multiplier = 1 ) {
		$keyword_limit = 3;

		if ( self::is_plan( 'starter' ) ) {
			$keyword_limit = 10;
		}
		elseif ( self::is_plan( 'pro' ) ) {
			$keyword_limit = 30;
		}
		elseif ( self::is_plan( 'enterprise' ) ) {
			$keyword_limit = 9999;
		}

		return $keyword_limit * $multiplier;
	}

	static public function reduce_array_by_dynamic_panel_limit( array $array, $multiplier = 1 ) {
		$panel_limit = ISSSCR_Helpers::get_dynamic_panel_limit( $multiplier );
		return array_slice( $array, 0, $panel_limit );
	}

	static public function reduce_array_by_rows_limit( array $array, $multiplier = 1 ) {
		$rows_limit = ISSSCR_Helpers::get_repeater_box_rows_limit( $multiplier );
		return array_slice( $array, 0, $rows_limit );
	}

	static public function reduce_array_by_keyword_limit( array $array, $multiplier = 1 ) {
		$keyword_limit = self::get_keyword_limit( $multiplier );
		return array_slice( $array, 0, $keyword_limit );
	}

	static public function get_supported_post_types() {
		$all_post_types = get_post_types( array(
				'public' => true,
		), 'object' );

		$unsupported_post_types       = apply_filters( 'issscr_unsupported_post_types', array( 'product', 'attachment', 'issslpg-landing-page', 'issslpg-local' ) );
		$default_activated_post_types = apply_filters( 'issscr_default_activated_post_types', array( 'page', 'product', 'issslpg-template' ) );

		$i = 0;
		$supported_post_types = array();
		foreach ( $all_post_types as $post_type ) {
			if ( ! in_array( $post_type->name, $unsupported_post_types ) ) {
				$supported_post_types[$i]['name']           = $post_type->name;
				$supported_post_types[$i]['default_active'] = false;
				$supported_post_types[$i]['object']         = $post_type;
				if ( in_array( $post_type->name, $default_activated_post_types ) ) {
					$supported_post_types[$i]['default_active'] = true;
				}
				$i++;
			}
		}

		return $supported_post_types;
	}

	static public function get_active_post_types() {
		$post_types        = self::get_supported_post_types();
		$active_post_types = array();
		foreach ( $post_types as $post_type ) {
			$post_type_name = $post_type['name'];
			$post_type_default_active = $post_type['default_active'];
			if ( ISSSCR_Options::get_setting( "support_{$post_type_name}_post_type", $post_type_default_active ) ) {
				$active_post_types[] = $post_type_name;
			}
		}

		return $active_post_types;
	}

	static public function get_all_dynamic_panels( $exclude_panel_type = array() ) {
		$active_panels = array();
		$post_types = ISSSCR_Helpers::get_active_post_types();

		foreach ( $post_types as $post_type ) {

			// Content Panels
			if ( ! in_array( 'content', $exclude_panel_type ) ) {
				$content_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_content_panels" );
				foreach ( $content_panels as $content_panel ) {
					$handle = $content_panel['handle'];
					$handle = "issscr_{$post_type}_{$handle}_content_panel";
					if ( ! in_array( $handle, $active_panels ) ) {
						$active_panels[] = $handle;
					}
				}
			}

			// Image Panels
			if ( ! in_array( 'images', $exclude_panel_type ) ) {
				$image_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_image_panels" );
				foreach ( $image_panels as $images_panel ) {
					$handle = $images_panel['handle'];
					$handle = "issscr_{$post_type}_{$handle}_images_panel";
					if ( ! in_array( $handle, $active_panels ) ) {
						$active_panels[] = $handle;
					}
				}
			}

			// Keyword Panels
			if ( ! in_array( 'keywords', $exclude_panel_type ) ) {
				$keyword_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_keyword_panels" );
				foreach ( $keyword_panels as $keyword_panel ) {
					$handle = $keyword_panel['handle'];
					$handle = "issscr_{$post_type}_{$handle}_keywords_panel";
					if ( ! in_array( $handle, $active_panels ) ) {
						$active_panels[] = $handle;
					}
				}
			}

			// Phrase Panels
			if ( ! in_array( 'phrases', $exclude_panel_type ) ) {
				$phrase_panels = ISSSCR_Options::get_panels( "post_type_{$post_type}_phrase_panels" );
				foreach ( $phrase_panels as $phrase_panel ) {
					$handle = $phrase_panel['handle'];
					$handle = "issscr_{$post_type}_{$handle}_phrases_panel";
					if ( ! in_array( $handle, $active_panels ) ) {
						$active_panels[] = $handle;
					}
				}
			}

		}

		return $active_panels;
	}

}