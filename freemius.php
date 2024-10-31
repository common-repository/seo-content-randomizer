<?php

if ( ! function_exists( 'issscr_fs' ) ) {
	// Create a helper function for easy SDK access.
	function issscr_fs() {
		global $issscr_fs;

		if ( ! isset( $issscr_fs ) ) {
			// Activate multisite network integration.
			if ( ! defined( 'WP_FS__PRODUCT_2386_MULTISITE' ) ) {
				define( 'WP_FS__PRODUCT_2386_MULTISITE', true );
			}

			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$issscr_fs = fs_dynamic_init( array(
				'id'                  => '2386',
				'slug'                => 'seo-content-randomizer',
				'type'                => 'plugin',
				'public_key'          => 'pk_01f914f8b8c0ed1284ca918710c42',
				'is_premium'          => true,
				// If your plugin is a serviceware, set this option to false.
				'has_premium_version' => true,
				'has_addons'          => true,
				'has_paid_plans'      => true,
				'trial'               => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'has_affiliation'     => 'all',
				'menu'                => array(
					'slug'           => 'issscr_settings',
					'support'        => false,
				),
			) );
		}

		return $issscr_fs;
	}

	// Init Freemius.
	issscr_fs();
	// Signal that SDK was initiated.
	do_action( 'issscr_fs_loaded' );
}