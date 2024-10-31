<?php



class ISSSCR_Admin_Notices {

	static public function create_notice( $text, $status_class = 'notice-info', $dismissible = true ) {
		$dismissible_class = $dismissible ? 'is-dismissible' : '';

		$output = "<div class='notice {$status_class} {$dismissible_class}'>";
		$output.= "<p>{$text}</p>";
		if ( $dismissible ) {
			$output.= "<button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button>";
		}
		$output.= "</div>";

		return $output;
	}

	static public function documentation_notice() {
		if ( ISSSCR_Helpers::is_white_labeled() ) {
			return;
		}
		$data = self::get_doc_video_data();
		if ( ! $data ) {
			return;
		}
		?>
		<style>
            .issscr-documentation-notice {
                padding: 1.5rem 1.5rem;
                display: flex;
            }
            .issscr-documentation-notice-heading {
                margin-top: .7rem;
            }
            .issscr-documentation-notice img {
                max-width: 200px;
                transition: all .2s ease-in-out;
            }
            .issscr-documentation-notice a {
                font-weight: 600;
                text-decoration: none;
            }
            .issscr-documentation-notice a:hover {
                text-decoration: underline;
            }
            .issscr-documentation-notice img:hover {
                transform: scale(1.04);
            }
            .issscr-documentation-notice-col-2 {
                padding-left: 1.5rem;
            }
            .issscr-documentation-notice-text {
                font-size: .95rem;
                line-height: 1.6;
            }
            .issscr-documentation-notice-new-tab-link {
                display: block;
                text-align: center;
            }
		</style>
		<div class='notice issscr-documentation-notice'>
			<div class="issscr-documentation-notice-col-1 js-issscr-lightgallery">
				<?php for ( $i = 0; $i < count( $data['videos'] ); $i++ ) : ?>
					<?php if ( $i === 0 ) : ?>
						<div>
							<a class="js-issscr-lightgallery-item" href="<?php echo $data['videos'][$i]; ?>">
								<img src="<?php echo plugin_dir_url( __DIR__ ) . 'admin/images/how-to-video-preview.jpg' ; ?>" alt="">
							</a>
						</div>
						<a class="issscr-documentation-notice-new-tab-link" href="<?php echo $data['videos'][$i]; ?>" target="_blank">
							Open video in new tab
						</a>
					<?php else : ?>
						<a class="js-issscr-lightgallery-item" style="display: none;" href="<?php echo $data['videos'][$i]; ?>">Video</a>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
			<div class="issscr-documentation-notice-col-2">
				<h2 class="issscr-documentation-notice-heading">
					<?php echo $data['title']; ?>
				</h2>
				<div class="issscr-documentation-notice-text">
					To get started with the SEO Content Randomizer, please watch our
					<a href="https://intellasoftplugins.com/how-to-videos/" target="_blank">training videos</a>.
					<br>
					If you have further questions, you can open a support ticket by sending an email to <a href="mailto:support@intellasoftplugins.com" target="_blank">support@intellasoftplugins.com</a> or call us directly at
					<a href="tel:877-764-6366">877-764-6366</a>.
				</div>
			</div>
		</div>
		<?php
	}

	static public function get_doc_video_data() {
		$lpg_active = ISSSCR_Helpers::is_lpg_plugin_active();
		$current_screen = get_current_screen();
//		var_dump( $current_screen->id);
		switch ( $current_screen->id ) {
			case 'toplevel_page_issscr_settings' :
			case 'seo-content-randomizer_page_iss_company_info_settings' :
			case 'seo-content-randomizer_page_issscr_cache_settings' :
				return [
					'title'  => 'SEO Content Randomizer: Setup',
					'videos' => [
						'https://youtu.be/Cd8QlxrrB28',
						'https://youtu.be/g8Kpy64rplI',
					],
				];
		}

		return false;
	}

//	static public function documentation_notice() {
//		if ( ! isset( $_GET['page'] ) || ISSSCR_Helpers::is_white_labeled() ) {
//			return;
//		}
//
//		$on_settings_screen = ( $_GET['page'] === 'issscr_settings' );
//		if ( $on_settings_screen ) {
//			$text = __( 'If you need help with the setup or configuration of the SEO Landing Page Generator, take a look at our <a href="https://intellasoftplugins.com/how-to-videos/" target="_blank">How-To videos</a>.', 'issscr' );
//			echo self::create_notice( $text, 'notice-info', false );
//		}
//	}

	static public function edit_page_update_notice() {
		$on_edit_screen     = ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' );
		$on_randomizer_page = ISSSCR_Helpers::is_randomizer_page();
		if ( $on_edit_screen && $on_randomizer_page ) {
			$text = __( 'Please make sure to hit the <b>Update</b> button before leaving the edit screen.', 'issscr' );
			echo self::create_notice( $text, 'notice-info', false );
		}
	}

	static public function edit_page_plan_upgrade_notice() {
		if ( ISSSCR_Helpers::is_white_labeled() ) {
			return;
		}

		$on_free_plan       = ISSSCR_Helpers::is_plan( 'free' );
		$on_randomizer_page = ISSSCR_Helpers::is_randomizer_page();
		$on_edit_screen     = ( isset( $_GET['action'] ) && $_GET['action'] === 'edit' );
		if ( $on_free_plan && $on_edit_screen && $on_randomizer_page ) {
			$rows_limit = ISSSCR_Helpers::get_repeater_box_rows_limit();
			$text       = "You're using the <b>Free</b> version of the SEO Content Randomizer and can only create up to {$rows_limit} content blocks.";
			if ( ! ISSSCR_Helpers::is_simulated_plan() ) {
				$upgrade_url = issscr_fs()->get_upgrade_url();
				$text .= " Consider <a href='{$upgrade_url}'>upgrading your plan</a> to be able to add more.";
			}
			echo self::create_notice( $text, 'notice-info' );
		}
	}

	static public function settings_plan_upgrade_notice() {
		if ( ISSSCR_Helpers::is_white_labeled() ) {
			return;
		}

		$on_free_plan       = ISSSCR_Helpers::is_plan( 'free' );
		$on_settings_screen = ( isset( $_GET['page'] ) && $_GET['page'] === 'issscr_settings' );

		if ( $on_free_plan && $on_settings_screen ) {
			$panel_limit = ISSSCR_Helpers::get_dynamic_panel_limit();
			$text = "You're using the <b>Free</b> version of the SEO Content Ranzomizer and are only allowed to create <b>{$panel_limit}</b> custom panels.";
			if ( ! ISSSCR_Helpers::is_simulated_plan() ) {
				$upgrade_url = issscr_fs()->get_upgrade_url();
				$text.= " Consider <a href='{$upgrade_url}'>upgrading your plan</a> to be able to add more.";
			}
			echo self::create_notice( $text, 'notice-info' );
		}
	}

}