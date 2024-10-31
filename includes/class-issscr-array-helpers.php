<?php



class ISSSCR_Array_Helpers {

	static public function get_sanitized_json( $array ) {
		$array = self::remove_empty_values( $array );

		if ( empty( $array ) ) {
			return false;
		}

		return json_encode( $array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	// Source: https://www.heididev.com/recursively-remove-empty-elements-associative-array-php
	static public function remove_empty_values( &$array ) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = self::remove_empty_values( $value );
			}
			if ( empty( $value ) ) {
				unset( $array[$key] );
			}
		}

		return $array;
	}

}