<?php



class ISSSCR_Cache_Manager {

	// The option name in the database
	public static $record_id = false;

	// The record saved in the database
	public static $memory_record = array();

	// The copy of the record saved in the database, which is used to
	// determine, which values have already been read
	public static $current_record = array();

	// The new record that will be saved to the database, after the content
	// is constructed
	public static $new_record = array();

	public function __construct() {
//		self::$record_id      = false;
		// The record saved in the database
//		self::$memory_record  = array();
		// The copy of the record saved in the database, which is used to
		// determine, which values have already been read
//		self::$current_record = array();
		// The new record that will be saved to the database, after the content
		// is constructed
//		self::$new_record     = array();
	}

	public function load_record( $post_id = false ) {
		if ( ! $this->is_cache_enabled() ) {
			return false;
		}

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$current_record = self::$current_record;
		if ( empty( $current_record ) ) {
			$current_record = $this->get_memory_record( $post_id );
		}
		self::$record_id      = $this->get_record_id( $post_id );
		self::$memory_record  = $this->get_memory_record();
		self::$current_record = $current_record;
//			self::$new_record = self::$new_record;

		return self::$memory_record;
	}

	public function get_record_id( $post_id = false ) {
		if ( $post_id ) {
			return "issscr_lp_record_{$post_id}";
		}

		return self::$record_id;
	}

	public function has_record() {
		if ( $this->get_memory_record() ) {
			return true;
		}
		return false;
	}

	public function get_record_expiration_timestamp() {
		$record_id = self::$record_id;
		return get_option( "_transient_timeout_{$record_id}" );
	}

	public function get_memory_record( $memory_record_id = false ) {
		$record_id = $this->get_record_id( $memory_record_id );
		$memory_record = get_transient( $record_id );
		if ( $memory_record === false ) {
			return self::$memory_record;
		}
		return $memory_record;
	}

	public function add_new_record_entry_value( $new_record_entry_id, $new_record_entry_value ) {
		self::$new_record[$new_record_entry_id][] = $new_record_entry_value;
	}

	public function get_current_record_entry( $current_record_entry_id ) {
		if ( ! isset( self::$current_record[$current_record_entry_id] ) ) {
			return null;
		}
		return self::$current_record[$current_record_entry_id];
	}

	public function get_current_record_entry_value( $current_record_entry_id ) {
		$current_record_entry_value = $this->read_current_record_entry_value( $current_record_entry_id );
		$this->delete_current_record_entry_value( $current_record_entry_id );
		return $current_record_entry_value;
	}

	public function read_current_record_entry_value( $current_record_entry_id ) {
		$current_record_entry = $this->get_current_record_entry( $current_record_entry_id );
		if ( ! isset( $current_record_entry[0] ) ) {
			return null;
		}
		return $current_record_entry[0];
	}

	public function get_memory_record_entry( $memory_record_entry_id ) {
		if ( ! isset( self::$memory_record[$memory_record_entry_id] ) ) {
			return null;
		}
		return self::$memory_record[$memory_record_entry_id];
	}

	public function read_memory_record_entry_value( $memory_record_entry_id ) {
		$memory_record_entry = $this->get_memory_record_entry( $memory_record_entry_id );
		if ( ! isset( $memory_record_entry[0] ) ) {
			return null;
		}
		return $memory_record_entry[0];
	}

	public function get_cache_expiration_time() {
		$cache_expiration_setting = ISSSCR_Options::get_setting( 'cache_expiration', 7, 'issscr_cache_settings' );
		return $cache_expiration_setting * 86400; // Convert days to seconds
	}

	public function delete_current_record_entry_value( $current_record_entry_id ) {
		if ( ! isset( self::$current_record[$current_record_entry_id][0] ) ) {
			return false;
		}
		// Remove array element
		unset( self::$current_record[$current_record_entry_id][0] );
		// Reset array index
		self::$current_record[$current_record_entry_id] = array_values( self::$current_record[$current_record_entry_id] );
	}

	public function is_cache_enabled() {
		$cache_setting = ISSSCR_Options::get_setting( 'cache', true, 'issscr_cache_settings' );
		$cache_expiration_setting = ISSSCR_Options::get_setting( 'cache_expiration', 7, 'issscr_cache_settings' );
		return ( $cache_setting && $cache_expiration_setting );
	}

	public function delete_record( $record_id = false ) {
		$record_id = $this->get_record_id( $record_id );
//		error_log( "DELETE RECORD: $record_id" );
		return delete_transient( $record_id );
	}

	public function save_new_record() {
		if ( ! $this->is_cache_enabled() || ! empty( self::$memory_record ) ) {
			return false;
		}

		$cache_expiration_time = $this->get_cache_expiration_time();
		return set_transient( self::$record_id, self::$new_record, $cache_expiration_time );
	}

	public function save_to_existing_record() {
		if ( ! $this->is_cache_enabled() ) {
			return false;
		}
//		error_log('SAVE TO EXISTING RECORD');
		$new_record = array_merge( self::$new_record, self::$memory_record );

		$cache_expiration_time = $this->get_cache_expiration_time();
		return set_transient( self::$record_id, $new_record, $cache_expiration_time );
	}

}