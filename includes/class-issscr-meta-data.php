<?php



class ISSSCR_Meta_Data {

	public static function get_landing_page_id( $use_local_content_page_id = true ) {
		$local_content_page_id = get_post_meta( get_the_ID(), '_issslpg_local_content_page', true );
		if ( $use_local_content_page_id && $local_content_page_id ) {
			return (int)$local_content_page_id;
		}
		return get_the_ID();
	}

	// If we're on a ISSSLPG landing page, get the ISSSLPG template page ID to
	// to get WP to use the meta data from the template page instead of the
	// landing page.
	public static function get_post_id() {
		if ( is_admin() ) {
			return get_the_ID();
		}
		$issslpg_template_page_id = get_post_meta( get_the_ID(), '_issslpg_template_page_id', true );
		return $issslpg_template_page_id ? $issslpg_template_page_id : get_the_ID();
	}

	public static function get_post_type() {
		return get_post_type( self::get_post_id() );
	}

	public static function get_processed_content( $group_id, $field_id, $entry_number, $post_id = false ) {
		$content = ISSSCR_Meta_Data::get_group_field( $group_id, $field_id, $entry_number, $post_id );
		return self::process_content( $content );
	}

	public static function get_group_field( $group_id, $field_id, $entry_number, $post_id = false ) {
		$group_fields = self::get_group_fields( $group_id, $field_id, $post_id );

		$entry_number = (int)$entry_number;
		$entry_number--;
		if ( isset( $group_fields[$entry_number] ) ) {
			$field = $group_fields[$entry_number];
			return $field;
		}

		return false;
	}

	public static function get_group_fields( $group_id, $field_id = false, $post_id = false ) {

		$post_id = empty( $post_id ) ? self::get_post_id() : $post_id;
		$field_array = array();

		$group = get_post_meta( $post_id, $group_id, true );

		if ( ! $field_id ) {
			return $group;
		}

		if ( ! empty( $group ) && is_array( $group ) ) {
			foreach( $group as $key => $field ) {
				$field_array[] = $field[$field_id];
			}
		}

		return $field_array;
	}

	public static function process_content( $content ) {
		global $wp_embed;
		$content = $wp_embed->autoembed( $content );
		$content = $wp_embed->run_shortcode( $content );
        $content = do_shortcode( $content );
		$content = wpautop( $content );
        $content = preg_replace( '#<p>\s*</p>#', '', $content ); // Remove empty p tags

		return $content;
	}

}