<?php



class ISSSCR_Public_Schema_Data {

	static public function get_definition_list_faq_schema() {
		$cache_manager = new ISSSCR_Cache_Manager();
		if ( ! $cache_manager->is_cache_enabled() ) {
			return false;
		}

		$landing_page_id   = ISSSCR_Meta_Data::get_landing_page_id();
		$post_type         = ISSSCR_Meta_Data::get_post_type();
		$cache_manager->load_record( $landing_page_id );
		$cached_faq_block_ids = $cache_manager->read_current_record_entry_value( 'iss_faq_definition_list' );
		$faq_blocks = ISSSCR_Meta_Data::get_group_fields( "_issscr_{$post_type}_faq_definition_list" );
		$faq_items = array();

		if ( ! is_null( $cached_faq_block_ids ) ) {
			foreach ( $cached_faq_block_ids as $cached_faq_block_id ) {
				$faq_items[] = $faq_blocks[ $cached_faq_block_id ];
			}
		}

		if ( ! $faq_items ) {
			return false;
		}

		$faq_schema_entries = array();
		foreach ( $faq_items as $faq_item ) {
			$faq_schema_entries[]= array(
				'@type' => 'Question',
				'name' => do_shortcode( $faq_item['heading'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text' => do_shortcode( $faq_item['content'] ),
				),

			);
		}

		$schema_data = array(
			'@context' => 'http://schema.org',
			'@type' => 'FAQPage',
			'mainEntity' => $faq_schema_entries,
		);

		$json_schema = ISSSCR_Array_Helpers::get_sanitized_json( $schema_data );

		return self::enclose_in_script_tags( $json_schema, 'FAQ Schema' );
	}

	static private function enclose_in_script_tags( $content, $comment = '' ) {
		$output = '';
		if ( $comment ) {
			$output.= "\n<!-- {$comment} -->\n";
		}
		$output.= '<script type="application/ld+json">';
		$output.= $content;
		$output.= '</script>';
		$output.= "\n";

		return $output;
	}

}
