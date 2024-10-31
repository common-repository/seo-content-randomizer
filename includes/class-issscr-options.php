<?php



class ISSSCR_Options {

	static public function get_setting( $id, $default = false, $option_key = 'issscr_settings' ) {
		$settings = get_option( $option_key, $default );
		if ( isset( $settings[ $id ] ) ) {
			$setting = $settings[ $id ];
			$setting = ( $setting === 'on'  ) ? true  : $setting; // In case we're dealing with a checkbox or switch
			$setting = ( $setting === 'off' ) ? false : $setting; // In case we're dealing with a checkbox or switch
			return $setting;
		}

		return $default;
	}

	static public function set_setting( $id, $value, $option_key = 'issscr_settings' ) {
		$settings = get_option( $option_key );
		if ( $settings ) {
			$value = ( $value === true  ) ? 'on'  : $value; // In case we're dealing with a checkbox or switch
			$value = ( $value === false ) ? 'off' : $value; // In case we're dealing with a checkbox or switch
			$settings[ $id ] = $value;
			update_option( $option_key, $settings );
		}
	}

	static public function get_panels( $id ) {
		$panels_atts = array();
		$panels = ISSSCR_Options::get_setting( $id );
		$panels = explode( "\n", $panels );
		$i = 0;
		foreach ( $panels as $panel ) {
			$title  = trim( $panel );
			if ( ! empty( $title ) ) {
				$handle = sanitize_title( $panel );
				$handle = str_replace( '-', '_', $handle );
				$panels_atts[$i]['title']  = $title;
				$panels_atts[$i]['handle'] = $handle;
				$i++;
			}
		}

		return $panels_atts;
	}

}