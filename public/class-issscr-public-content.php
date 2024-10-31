<?php



class ISSSCR_Public_Content {

	static public function get_content( $default_content = '', $process_content = true ) {
		$post_type = ISSSCR_Meta_Data::get_post_type();
		$is_randomizer_page = ISSSCR_Helpers::is_randomizer_page();
		$auto_content_replace = ISSSCR_Options::get_setting( "{$post_type}_auto_content_replace", 'on' );

		if ( ! $is_randomizer_page || ! $auto_content_replace ) {
			return $default_content;
		}

		$randomization     = new ISSSCR_Public_Randomization();
		$cache_manager     = new ISSSCR_Cache_Manager();
		$landing_page_id   = get_the_ID();
		$cache_manager->load_record( $landing_page_id );
		$cached_content_id = $cache_manager->get_current_record_entry_value( 'content' );

		if ( ! is_null( $cached_content_id ) ) {
			$random_content_id = $cached_content_id;
		} else {
			$template_page_id = ISSSCR_Meta_Data::get_post_id();
			// Only auto-replace content if we're on a randomizer page and setting 'Auto Content Replacement' is 'on'.
			$pinned_content_block_number = get_post_meta( $template_page_id, "_issscr_{$post_type}_pinned_content_block", true );
			if ( ! empty ( $pinned_content_block_number ) ) {
				$random_content_id = $pinned_content_block_number;
			} else {
				$random_content_id = $randomization->get_random_content_id( "_issscr_{$post_type}_content", 'content' );
			}
			$cache_manager->add_new_record_entry_value( 'content', $random_content_id );
		}

//		error_log( "NEW RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$new_record, true ) );
//		error_log( "MEMORY RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$memory_record, true ) );
//		error_log( "CURRENT RECORD" );
//		error_log( print_r( ISSSCR_Cache_Manager::$current_record, true ) );

		if ( $process_content ) {
			return ISSSCR_Meta_Data::get_processed_content( "_issscr_{$post_type}_content", 'content', $random_content_id );
		}

		return ISSSCR_Meta_Data::get_group_field( "_issscr_{$post_type}_content", 'content', $random_content_id );
	}

	static public function get_definition_list( $atts, $handle = 'faq', $cache_id = 'iss_faq_definition_list' ) {
		$cache_manager = new ISSSCR_Cache_Manager();
		$atts = wp_parse_args( $atts, array(
			'accordion' => '',
			'htag'      => '',
			'limit'     => '5',
		) );

		$landing_page_id   = ISSSCR_Meta_Data::get_landing_page_id();
		$post_type         = ISSSCR_Meta_Data::get_post_type();
		$cache_manager->load_record( $landing_page_id );
		$cached_block_ids = $cache_manager->get_current_record_entry_value( $cache_id );

		// Get blocks
		$blocks = ISSSCR_Meta_Data::get_group_fields( "_issscr_{$post_type}_{$handle}_definition_list" );
		if ( ! $blocks ) {
			return false;
		}

		$random_blocks = array();

		if ( ! is_null( $cached_block_ids ) ) {
			foreach ( $cached_block_ids as $cached_block_id ) {
				$random_blocks[] = $blocks[$cached_block_id];
			}
		} else {
			// Numerate blocks
			$numerated_blocks = array();
			for ( $i = 0; $i < count( $blocks ); $i++ ) {
				$numerated_blocks[][$i] = $blocks[$i];
			}

			// Randomize blocks
			shuffle( $numerated_blocks );
			// Limit blocks
			$numerated_blocks = array_slice( $numerated_blocks, 0, $atts['limit'] );

			// Get block IDs
			$block_ids = array();
			foreach ( $numerated_blocks as $key => $value ) {
				$array_keys = array_keys($value);
				$block_ids[] = $array_keys[0];
			}

			// Cache block IDs
			$cache_manager->add_new_record_entry_value( $cache_id, $block_ids );

			// Get array of random blocks
			foreach ( $block_ids as $block_id ) {
				$random_blocks[] = $blocks[$block_id];
			}
		}

		return $random_blocks;
	}

}