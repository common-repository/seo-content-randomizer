<?php



/**
 * Source: https://github.com/CMB2/CMB2-Snippet-Library/blob/master/javascript/limit-number-of-multiple-repeat-groups.php
 */
class ISSSCR_Admin_CMB2_Plugin_Limited_Meta_Field_Registration {

	public function __construct() {
		$this->register_limited_repeater_meta_boxes();
	}

	public function register_limited_repeater_meta_boxes() {
		$limited_repeater_panels = array();
		$post_types = ISSSCR_Helpers::get_active_post_types();
		foreach( $post_types as $post_type ) {
			$limited_repeater_panels[] = "issscr_{$post_type}_content_panel";
			$limited_repeater_panels[] = "issscr_{$post_type}_static_content_panel";
		}
		$dynamic_panels = ISSSCR_Helpers::get_all_dynamic_panels( array( 'keywords' ) );
		$limited_repeater_panels = array_merge( $limited_repeater_panels, $dynamic_panels );

		foreach ( $limited_repeater_panels as $panel ) {
			add_action( "cmb2_after_post_form_{$panel}", array( $this, 'limit_group_repeat' ), 10, 2 );
		}
	}

	public function limit_group_repeat( $post_id, $cmb ) {
		// Grab the custom attribute to determine the limit
		$limit = absint( $cmb->prop( 'rows_limit' ) );
		$limit = $limit ? $limit : 0;
		$group = $cmb->prop( 'id' );
		?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
				// Only allow 3 groups
				var limit             = <?php echo $limit; ?>;
				var fieldGroupId      = '#<?php echo $group; ?>';
				var fieldGroupTableId = $('.cmb-repeatable-group', fieldGroupId).attr('id');
				var $fieldGroupTable  = $('.cmb-repeatable-group', fieldGroupId);
//				var $fieldGroupTable  = $( document.getElementById( fieldGroupTableId ) );
//				var $fieldGroupTable  = $( document.getElementById( fieldGroupId + '_repeat' ) );
//				console.log(fieldGroupId);
//				console.log($fieldGroupTable);
				var countRows = function() {
					return $fieldGroupTable.find( '> .cmb-row.cmb-repeatable-grouping' ).length;
				};
				var disableAdder = function() {
					$fieldGroupTable.find('.cmb-add-group-row.button-secondary').prop( {disabled: true} );
				};
				var enableAdder = function() {
					$fieldGroupTable.find('.cmb-add-group-row.button-secondary').prop( {disabled: false} );
				};
				$fieldGroupTable
						.ready( function() {
							if ( countRows() >= limit ) {
								disableAdder();
							}
							else if ( countRows() < limit ) {
								enableAdder();
							}
						})
						.on( 'cmb2_add_row', function() {
							if ( countRows() >= limit ) {
								disableAdder();
							}
						})
						.on( 'cmb2_remove_row', function() {
							if ( countRows() < limit ) {
								enableAdder();
							}
						});
//				$fieldGroupTable
//						.ready( function() {
//							if ( countRows() >= limit ) {
//								disableAdder();
//							}
//							else if ( countRows() < limit ) {
//								enableAdder();
//							}
//						});
			});
		</script>
		<?php
	}

}