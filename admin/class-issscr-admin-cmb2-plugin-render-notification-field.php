<?php

if ( ! class_exists( 'CMB2_Type_Base' ) ) {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/plugins/cmb2/includes/types/CMB2_Type_Base.php';
}

class ISSSLPG_Admin_CMB2_Plugin_Render_Notification_Field extends CMB2_Type_Base {

	static public function init() {
		add_filter( 'cmb2_render_class_notification', array( __CLASS__, 'class_name' ) );
	}

	public static function class_name() { return __CLASS__; }

	public function render() {
		if ( empty( $this->field->args['note'] ) ) {
			return;
		}

		echo $this->field->args['note'];
	}

}