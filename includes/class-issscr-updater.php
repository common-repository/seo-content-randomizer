<?php
/**
 * Fired after plugin update
 *
 * @link       https://intellasoftplugins.com
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/includes
 */

/**
 * Fired after plugin update.
 *
 * This class defines all code necessary to run after a plugin update.
 *
 * @package    ISSSCR
 * @subpackage ISSSCR/includes
 * @author     Ruven Pelka <ruven.pelka@gmail.com>
 */
class ISSSCR_Updater {

	public function __construct() {
		$plugin_version = get_option( 'issscr_version', 0 );

		// Set original installation version
		$original_installation_version = $plugin_version ? $plugin_version : ISSSCR_VERSION;
		add_option( 'issscr_original_installation_version', $original_installation_version );

		if ( version_compare( $plugin_version, '3.15.0', '<' ) ) {
			$this->update_1();
		}

		if ( version_compare( $plugin_version, '3.16.0', '<' ) ) {
			$this->update_2();
		}

		if ( version_compare( $plugin_version, ISSSCR_VERSION, '<' ) ) {
			update_option( 'issscr_version', ISSSCR_VERSION );
		}
	}

	/**
	 * Update 1
	 *
	 * Meta Description used to be a single custom field, that we turned into a
	 * panel with repeatable fields. Here we make sure to put the old meta
	 * description into the first field of the repeatable fields, so the user
	 * won't lose his content after updating.
	 */
	public function update_1() {
		// Get post types.
		$post_types = get_post_types( array(
			'public' => true,
		), 'object' );
		// Go through all post types that have a meta description.
		foreach ( $post_types as $post_type ) :
			$wp_query = new WP_Query( array(
				'post_type'      => $post_type->name,
				'posts_per_page' => -1,
				'meta_query'  => array(
					array(
						'key'     => "_issscr_{$post_type->name}_meta_description",
						'compare' => 'EXISTS'
					),
				),
			) );
			// Put 'meta_description' field into 'meta_descriptions' field as first block.
			while ( $wp_query->have_posts() ) :
				$wp_query->the_post();
				$meta_description = get_post_meta( get_the_ID(), "_issscr_{$post_type->name}_meta_description", true );
				$meta_description = array( array( 'meta_description' => $meta_description ) );
				add_post_meta( get_the_ID(), "_issscr_{$post_type->name}_page_meta_boxes", $meta_description, true );
			endwhile;
			wp_reset_postdata();
		endforeach;
	}

	/**
	 * Update 2
	 */
	public function update_2() {
		$company_name = ISSSCR_Options::get_setting( 'company_name' );
		ISSSCR_Options::set_setting( 'company_name', $company_name, 'iss_company_info_settings' );
	}

}