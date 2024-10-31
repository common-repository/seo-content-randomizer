<?php



class ISSSCR_String_Helpers {

	static public function explode_string_by_new_line( $string ) {
		$output = array();
		$lines = preg_split( '/\r\n|\r|\n/', $string );

		// Clean out empty lines
		foreach ( $lines as $line ) {
			// Sanitize line
			$replace = array( "\n", "\r", "<br>", "<br />", "<br/>" );
			$line = str_replace( $replace, '', $line );
			$line = trim( $line );

			if ( ! empty( $line ) ) {
				$output[] = $line;
			}
		}

		return $output;
	}

}