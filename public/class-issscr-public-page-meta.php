<?php



class ISSSCR_Public_Page_Meta {

	public function meta_description() {
		if ( ! ISSSCR_Helpers::is_randomizer_page() ) {
			return;
		}

		$post_id   = ISSSCR_Meta_Data::get_post_id();
		$post_type = ISSSCR_Meta_Data::get_post_type();

		if ( ! ISSSCR_Options::get_setting( "show_{$post_type}_page_meta_panel", true ) ) {
			return;
		}

//		$meta_description = get_post_meta( $post_id, "_issscr_{$post_type}_meta_description", true );
		$randomization = new ISSSCR_Public_Randomization();
		$meta_description = $randomization ->get_random_content( "_issscr_{$post_type}_page_meta_boxes", 'meta_description' );
		if ( empty( $meta_description ) ) {
			return;
		}

		$meta_description = do_shortcode( $meta_description );
		$meta_description = wp_strip_all_tags( $meta_description );

		echo "<meta name='description' content='{$meta_description}' />";
	}

}