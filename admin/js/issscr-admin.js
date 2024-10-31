(function( $ ) {
	'use strict';

	$(function() {
		hide_freemius_menu_items_on_simulated_plan();
		hide_freemius_menu_items_when_plugin_is_white_labeled();
		increase_cmb2_wysiwyg_panel_size();
		confirm_confirmation_dialog_before_removing_cmb2_block();
		move_randomizer_toggler_panel();
		setup_lightgallery();

		// After site is loaded...
		$(window).load(function () {
		});
	});

	/* LightGallery
	 ========================================================================= */

	function setup_lightgallery() {
		jQuery('.js-issscr-lightgallery').lightGallery({
			videoMaxWidth: '1200px',
			selector: '.js-issscr-lightgallery-item',
		});
	}

	/* Always display Randomizer Toggler panel below Publish panel on
	 * edit screens
	 ========================================================================= */

	function move_randomizer_toggler_panel() {
		const $publishPanel             = $('#submitdiv');
		const $randomizer_toggler_panel = $('#issscr_randomizer_page_toggler_panel');

		if ( $publishPanel.length && $randomizer_toggler_panel.length ) {
			$publishPanel.after($randomizer_toggler_panel);
		}
	}

	/* Display a confirmation dialog when a user clicks the button to remove
	 * a CMB2 block
	 ========================================================================= */

	function confirm_confirmation_dialog_before_removing_cmb2_block() {
		$('.cmb-remove-group-row-button', '.issscr-randomizer-page').click(function(e) {

			var ok = confirm('Are you sure you want to remove this item?');

			if ( ! ok ) {
				e.preventDefault();
				return false;
			}

		});
	}

	/* Hide Freemius Menu Items when Plugin is White Labeled
	 * We have to do this through JS, because Freemius doesn't use the WP API to
	 * register sub menu pages
	 ========================================================================= */

	function hide_freemius_menu_items_on_simulated_plan() {
		if ( $('body').hasClass('js-issscr-simulated-plan') ) {
			$( '.fs-submenu-item', '#toplevel_page_issscr_settings').parent().parent().remove();
		}
	}

	/* Hide Freemius Menu Items when Plugin is White Labeled
	 * We have to do this through JS, because Freemius doesn't use the WP API to
	 * register sub menu pages
	 ========================================================================= */

	function hide_freemius_menu_items_when_plugin_is_white_labeled() {
		if ( $('body').hasClass('js-issscr-white-labeled') ) {
			$( '.fs-submenu-item', '#toplevel_page_issscr_settings').parent().parent().remove();
		}
	}

	/* Increase CMB2 WYSIWYG Panel size
	 ========================================================================= */

	function increase_cmb2_wysiwyg_panel_size() {
		$( '.cmbhandle-title', '.issscr-randomizer-page' ).on( "click", function() {
			$('.mce-edit-area iframe').height(400);
		});
	}

})( jQuery );
