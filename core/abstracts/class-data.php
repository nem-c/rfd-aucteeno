<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by
 * the different data store classes.
 *
 * @package     RFD\Core
 */

namespace RFD\Core\Abstracts;

use Exception;
use RFD\Core\Meta_Data;
use WP_Error;
use RFD\Core\Data_Store_WP;
use RFD\Core\DateTime;
use DateTimeZone;
use RFD\Core\Exceptions\Data_Exception;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract Data Class
 */
abstract class Data {

	/**
	 * ID for this object.
	 *
	 * @since 2.3.0
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 2.3.0
	 * @var array
	 */
	protected $data = array();

	/**
	 * Core data changes for this object.
	 *
	 * @since 2.3.0
	 * @var array
	 */
	protected $changes = array();

	/**
	 * This is false until the object is read from the DB.
	 *
	 * @since 2.3.0
	 * @var bool
	 */
	protected $object_read = false;

	/**
	 * This is the name of this object type.
	 *
	 * @since 2.3.0
	 * @var string
	 */
	protected $object_type = 'data';

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 * Used as a standard way for sub classes (like product types) to add
	 * additional information to an inherited class.
	 *
	 * @since 2.3.0
	 * @var array
	 */
	protected $extra_data = array();

	/**
	 * Set to _data on construct so we can track and reset data if needed.
	 *
	 * @var array
	 */
	protected $default_data = array();

	/**
	 * Contains a reference to the data store for this class.
	 *
	 * @since 2.3.0
	 * @var Data_Store
	 */
	protected $data_store;

	/**
	 * Stores meta in cache for future reads.
	 * A group must be set to to enable caching.
	 *
	 * @since 2.3.0
	 * @var string
	 */
	protected $cache_group = '';

	/**
	 * Stores additional meta data.
	 *
	 * @since 2.3.0
	 * @var array
	 */
	protected $meta_data = null;

	/**
	 * Default constructor.
	 *
	 * @param int|object|array $read ID to load from the DB (optional) or already queried data.
	 */
	public function __construct( $read = 0 ) {
		$this->data         = array_merge( $this->data, $this->extra_data );
		$this->default_data = $this->data;

		if ( is_numeric( $read ) && $read > 0 ) {
			$this->set_id( $read );
		}
	}

	/**
	 * Only store the object ID to avoid serializing the data object instance.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'id' );
	}

	/**
	 * Re-run the constructor with the object ID.
	 *
	 * If the object no longer exists, remove the ID.
	 */
	public function __wakeup() {
		try {
			$this->__construct( absint( $this->id ) );
		} catch ( Exception $e ) {
			$this->set_id( 0 );
			$this->set_object_read();
		}
	}

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 */
	public function __clone() { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded
		$this->maybe_read_meta_data();
		if ( false === empty( $this->meta_data ) ) {
			foreach ( $this->meta_data as $array_key => $meta ) {
				$this->meta_data[ $array_key ] = clone $meta;
				if ( ! empty( $meta->id ) ) {
					$this->meta_data[ $array_key ]->id = null;
				}
			}
		}
	}

	/**
	 * Get the data store.
	 *
	 * @return object
	 */
	public function get_data_store(): object {
		return $this->data_store;
	}

	/**
	 * Returns the unique ID for this object.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Delete an object, set the ID to 0, and return result.
	 *
	 * @param bool $force_delete Should the date be deleted permanently.
	 *
	 * @return bool result
	 */
	public function delete( $force_delete = false ): bool {
		if ( true === empty( $this->data_store ) ) {
			return false;
		}

		$this->data_store->delete( $this, array( 'force_delete' => $force_delete ) );
		$this->set_id( 0 );

		return true;
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @return int
	 */
	public function save(): int {
		if ( false === empty( $this->data_store ) ) {
			return $this->get_id();
		}

		/**
		 * Trigger action before saving to the DB. Allows you to adjust object props before save.
		 *
		 * @param Data $this The object being saved.
		 * @param Data_Store_WP $data_store THe data store persisting the data.
		 */
		do_action( 'rfd_before_' . $this->object_type . '_object_save', $this, $this->data_store );

		if ( $this->get_id() ) {
			$this->data_store->update( $this );
		} else {
			$this->data_store->create( $this );
		}

		/**
		 * Trigger action after saving to the DB.
		 *
		 * @param Data $this The object being saved.
		 * @param Data_Store_WP $data_store THe data store persisting the data.
		 */
		do_action( 'rfd_after_' . $this->object_type . '_object_save', $this, $this->data_store );

		return $this->get_id();
	}

	/**
	 * Change data to JSON format.
	 *
	 * @return string Data in JSON format.
	 */
	public function __toString() {
		$stringified = wp_json_encode( $this->get_data() );
		if ( true === empty( $stringified ) ) {
			$stringified = '';
		}

		return $stringified;
	}

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 */
	public function get_data(): array {
		return array_merge( array( 'id' => $this->get_id() ), $this->data, array( 'meta_data' => $this->get_meta_data() ) );
	}

	/**
	 * Returns array of expected data keys for this object.
	 *
	 * @return array
	 */
	public function get_data_keys(): array {
		return array_keys( $this->data );
	}

	/**
	 * Returns all "extra" data keys for an object (for sub objects like product types).
	 *
	 * @return array
	 */
	public function get_extra_data_keys(): array {
		return array_keys( $this->extra_data );
	}

	/**
	 * Filter null meta values from array.
	 *
	 * @param mixed $meta Meta value to check.
	 *
	 * @return bool
	 */
	protected function filter_null_meta( $meta ): bool {
		return ! is_null( $meta->value );
	}

	/**
	 * Get All Meta Data.
	 *
	 * @return array of objects.
	 */
	public function get_meta_data(): array {
		$this->maybe_read_meta_data();

		return array_values( array_filter( $this->meta_data, array( $this, 'filter_null_meta' ) ) );
	}

	/**
	 * Check if the key is an internal one.
	 *
	 * @param string $key Key to check.
	 *
	 * @return bool   true if it's an internal key, false otherwise
	 */
	protected function is_internal_meta_key( string $key ): bool {
		// @phpstan-ignore-next-line.
		$internal_meta_key = ! empty( $key ) && $this->data_store && in_array( $key, $this->data_store->get_internal_meta_keys(), true );

		if ( ! $internal_meta_key ) {
			return false;
		}

		$has_setter_or_getter = is_callable( array( $this, 'set_' . $key ) ) ||
		                        // phpcs:ignore Generic.WhiteSpace.DisallowSpaceIndent.SpacesUsed
		                        is_callable( array( $this, 'get_' . $key ) );

		if ( false === $has_setter_or_getter ) {

			return false;
		}
		/* translators: %s: $key Key to check */
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'Generic add/update/get meta methods should not be used for internal meta data, including "%s". Use getters and setters.', 'rfd-core' ), $key ), '2.3.0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		return true;
	}

	/**
	 * Get Meta Data by Key.
	 *
	 * @param string $key Meta Key.
	 * @param bool $single return first found meta with key, or all with $key.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed
	 */
	public function get_meta( $key = '', $single = true, $context = 'view' ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		if ( $this->is_internal_meta_key( $key ) ) {
			$function = 'get_' . $key;

			if ( is_callable( array( $this, $function ) ) ) {
				return $this->{$function}();
			}
		}

		$this->maybe_read_meta_data();
		$meta_data  = $this->get_meta_data();
		$array_keys = array_keys( wp_list_pluck( $meta_data, 'key' ), $key, true );
		$value      = $single ? '' : array();

		if ( ! empty( $array_keys ) ) {
			// We don't use the $this->meta_data property directly here because we don't want meta with a null value (i.e. meta which has been deleted via $this->delete_meta_data()).
			if ( $single ) {
				$value = $meta_data[ current( $array_keys ) ]->value;
			} else {
				$value = array_intersect_key( $meta_data, array_flip( $array_keys ) );
			}
		}

		if ( 'view' === $context ) {
			$value = apply_filters( $this->get_hook_prefix() . $key, $value, $this ); // @phpstan-ignore-line
		}

		return $value;
	}

	/**
	 * See if meta data exists, since get_meta always returns a '' or array().
	 *
	 * @param string $key Meta Key.
	 *
	 * @return boolean
	 */
	public function meta_exists( $key = '' ): bool {
		$this->maybe_read_meta_data();
		$array_keys = wp_list_pluck( $this->get_meta_data(), 'key' );

		return in_array( $key, $array_keys, true );
	}

	/**
	 * Set all meta data from array.
	 *
	 * @param array $data Key/Value pairs.
	 */
	public function set_meta_data( array $data ): void { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded
		if ( false === empty( $data ) ) {
			$this->maybe_read_meta_data();
			foreach ( $data as $meta ) {
				$meta = (array) $meta;
				if ( isset( $meta['key'], $meta['value'], $meta['id'] ) ) {
					$this->meta_data[] = new Meta_Data(
						array(
							'id'    => $meta['id'],
							'key'   => $meta['key'],
							'value' => $meta['value'],
						)
					);
				}
			}
		}
	}

	/**
	 * Add meta data.
	 *
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param bool $unique Should this be a unique key?.
	 */
	public function add_meta_data( $key, $value, $unique = false ): void {
		if ( $this->is_internal_meta_key( $key ) ) {
			$function = 'set_' . $key;

			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}

		$this->maybe_read_meta_data();
		if ( $unique ) {
			$this->delete_meta_data( $key );
		}
		$this->meta_data[] = new Meta_Data(
			array(
				'key'   => $key,
				'value' => $value,
			)
		);
	}

	/**
	 * Update meta data by key or ID, if provided.
	 *
	 * @param string $key Meta key.
	 * @param string|array $value Meta value.
	 * @param int $meta_id Meta ID.
	 */
	public function update_meta_data( string $key, $value, $meta_id = 0 ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		if ( $this->is_internal_meta_key( $key ) ) {
			$function = 'set_' . $key;

			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}

		$this->maybe_read_meta_data();

		$array_key = false;

		if ( $meta_id ) {
			$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), $meta_id, true );
			$array_key  = $array_keys ? current( $array_keys ) : false;
		} else {
			// Find matches by key.
			$matches = array();
			foreach ( $this->meta_data as $meta_data_array_key => $meta ) {
				if ( $meta->key === $key ) {
					$matches[] = $meta_data_array_key;
				}
			}

			if ( ! empty( $matches ) ) {
				// Set matches to null so only one key gets the new value.
				foreach ( $matches as $meta_data_array_key ) {
					$this->meta_data[ $meta_data_array_key ]->value = null;
				}
				$array_key = current( $matches );
			}
		}

		if ( false !== $array_key ) {
			$meta        = $this->meta_data[ $array_key ];
			$meta->key   = $key;
			$meta->value = $value;
		} else {
			$this->add_meta_data( $key, $value, true );
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param string $key Meta key.
	 */
	public function delete_meta_data( string $key ): void {
		$this->maybe_read_meta_data();
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'key' ), $key, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}

	/**
	 * Delete meta data.
	 *
	 * @param int $mid Meta ID.
	 */
	public function delete_meta_data_by_mid( int $mid ): void {
		$this->maybe_read_meta_data();
		$array_keys = array_keys( wp_list_pluck( $this->meta_data, 'id' ), (int) $mid, true );

		if ( $array_keys ) {
			foreach ( $array_keys as $array_key ) {
				$this->meta_data[ $array_key ]->value = null;
			}
		}
	}

	/**
	 * Read meta data if null.
	 */
	protected function maybe_read_meta_data(): void {
		if ( null === $this->meta_data ) {
			$this->read_meta_data();
		}
	}

	/**
	 * Helper method to compute meta cache key. Different from WP Meta cache key in that meta data cached using this key also contains meta_id column.
	 *
	 * @return string
	 */
	public function get_meta_cache_key(): string {
		if ( true === empty( $this->get_id() ) ) {
			_doing_it_wrong( 'get_meta_cache_key', 'ID needs to be set before fetching a cache key.', '4.7.0' );

			return '';
		}

		return self::generate_meta_cache_key( $this->get_id(), $this->cache_group );
	}

	/**
	 * Generate cache key from id and group.
	 *
	 * @param int|string $id Object ID.
	 * @param string $cache_group Group name use to store cache. Whole group cache can be invalidated in one go.
	 *
	 * @return string Meta cache key.
	 */
	public static function generate_meta_cache_key( $id, string $cache_group ): string {
		/**
		 * TODO:
		 * WC_Cache_Helper::get_cache_prefix( $cache_group ) . WC_Cache_Helper::get_cache_prefix( 'object_' . $id ) . 'object_meta_' . $id;
		 */
		return '';
	}

	/**
	 * Prime caches for raw meta data. This includes meta_id column as well, which is not included by default in WP meta data.
	 *
	 * @param array $raw_meta_data_collection Array of objects of { object_id => array( meta_row_1, meta_row_2, ... }.
	 * @param string $cache_group Name of cache group.
	 */
	public static function prime_raw_meta_data_cache( array $raw_meta_data_collection, string $cache_group ): void {
		foreach ( $raw_meta_data_collection as $object_id => $raw_meta_data_array ) {
			$cache_key = self::generate_meta_cache_key( $object_id, $cache_group );
			wp_cache_set( $cache_key, $raw_meta_data_array, $cache_group );
		}
	}

	/**
	 * Read Meta Data from the database. Ignore any internal properties.
	 * Uses it's own caches because get_metadata does not provide meta_ids.
	 *
	 * @param bool $force_read True to force a new DB read (and update cache).
	 */
	public function read_meta_data( $force_read = false ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		$this->meta_data = array();
		$cache_loaded    = false;

		if ( true === empty( $this->get_id() ) ) {
			return;
		}

		if ( true === empty( $this->data_store ) ) {
			return;
		}

		if ( true === empty( $this->cache_group ) ) {
			return;
		}

		// Prefix by group allows invalidation by group until https://core.trac.wordpress.org/ticket/4476 is implemented.
		$cache_key = $this->get_meta_cache_key();

		if ( false === $force_read ) {
			if ( ! empty( $this->cache_group ) ) {
				$cached_meta  = wp_cache_get( $cache_key, $this->cache_group );
				$cache_loaded = ! empty( $cached_meta );
			}
		}

		// We filter the raw meta data again when loading from cache, in case we cached in an earlier version where filter conditions were different.
		// @phpstan-ignore-next-line.
		$raw_meta_data = $cache_loaded ? $this->data_store->filter_raw_meta_data( $this, $cached_meta ) : $this->data_store->read_meta( $this );

		if ( $raw_meta_data ) {
			foreach ( $raw_meta_data as $meta ) {
				$this->meta_data[] = new Meta_Data(
					array(
						'id'    => (int) $meta->meta_id,
						'key'   => $meta->meta_key,
						'value' => maybe_unserialize( $meta->meta_value ),
					)
				);
			}

			if ( ! $cache_loaded && ! empty( $this->cache_group ) ) {
				wp_cache_set( $cache_key, $raw_meta_data, $this->cache_group );
			}
		}
	}

	/**
	 * Update Meta Data in the database.
	 */
	public function save_meta_data(): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		if ( true === empty( $this->data_store ) || true === empty( $this->meta_data ) ) {
			return;
		}
		foreach ( $this->meta_data as $array_key => $meta ) {
			if ( is_null( $meta->value ) ) {
				if ( ! empty( $meta->id ) ) {
					$this->data_store->delete_meta( $this, $meta ); // @phpstan-ignore-line
					unset( $this->meta_data[ $array_key ] );
				}
			} elseif ( empty( $meta->id ) ) {
				$meta->id = $this->data_store->add_meta( $this, $meta );  // @phpstan-ignore-line
				$meta->apply_changes();
			} else {
				if ( $meta->get_changes() ) {  // @phpstan-ignore-line
					$this->data_store->update_meta( $this, $meta ); // @phpstan-ignore-line
					$meta->apply_changes(); // @phpstan-ignore-line
				}
			}
		}
		if ( false === empty( $this->cache_group ) ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
			/**
			 * TODO:
			 *
			 * $cache_key = WC_Cache_Helper::get_cache_prefix( $this->cache_group ) . WC_Cache_Helper::get_cache_prefix( 'object_' . $this->get_id() ) . 'object_meta_' . $this->get_id();
			 * wp_cache_delete( $cache_key, $this->cache_group );
			 */
		}
	}

	/**
	 * Set ID.
	 *
	 * @param int $id ID.
	 */
	public function set_id( int $id ): void {
		$this->id = absint( $id );
	}

	/**
	 * Set all props to default values.
	 */
	public function set_defaults(): void {
		$this->data    = $this->default_data;
		$this->changes = array();
		$this->set_object_read( false );
	}

	/**
	 * Set object read property.
	 *
	 * @param boolean $read Should read?.
	 */
	public function set_object_read( $read = true ): void {
		$this->object_read = (bool) $read;
	}

	/**
	 * Get object read property.
	 *
	 * @return boolean
	 */
	public function get_object_read(): bool {
		return $this->object_read;
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result.
	 * Only sets using public methods.
	 *
	 * @param array $props Key value pairs to set. Key is the prop and should map to a setter function name.
	 * @param string $context In what context to run this.
	 *
	 * @return bool|WP_Error
	 */
	public function set_props( array $props, $context = 'set' ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		$errors = false;

		foreach ( $props as $prop => $value ) {
			try {
				/**
				 * Checks if the prop being set is allowed, and the value is not null.
				 */
				if ( is_null( $value ) || in_array( $prop, array( 'prop', 'date_prop', 'meta_data' ), true ) ) {
					continue;
				}
				$setter = "set_$prop";

				if ( is_callable( array( $this, $setter ) ) ) {
					$this->{$setter}( $value );
				}
			} catch ( Exception $e ) {
				if ( ! $errors ) {
					$errors = new WP_Error();
				}
				$errors->add( $e->getErrorCode(), $e->getMessage() ); // @phpstan-ignore-line
			}
		}

		return $errors && count( $errors->get_error_codes() ) ? $errors : true;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * This stores changes in a special array so we can track what needs saving
	 * the the DB later.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed $value Value of the prop.
	 */
	protected function set_prop( string $prop, $value ): void { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded
		if ( array_key_exists( $prop, $this->data ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
					$this->changes[ $prop ] = $value;
				}
			} else {
				$this->data[ $prop ] = $value;
			}
		}
	}

	/**
	 * Return data changes only.
	 *
	 * @return array
	 */
	public function get_changes(): array {
		return $this->changes;
	}

	/**
	 * Merge changes with data and clear.
	 */
	public function apply_changes(): void {
		$this->data    = array_replace_recursive( $this->data, $this->changes ); // @codingStandardsIgnoreLine
		$this->changes = array();
	}

	/**
	 * Prefix for action and filter hooks on data.
	 *
	 * @return string
	 */
	protected function get_hook_prefix(): string {
		return 'rfd_' . $this->object_type . '_get_';
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * Gets the value from either current pending changes, or the data itself.
	 * Context controls what happens to the value before it's returned.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed
	 */
	protected function get_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data ) ) {
			$value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . $prop, $value, $this ); // @phpstan-ignore-line
			}
		}

		return $value;
	}

	/**
	 * Sets a date prop whilst handling formatting and datetime objects.
	 *
	 * @param string $prop Name of prop to set.
	 * @param int|string|null $value Value of the prop.
	 */
	protected function set_date_prop( string $prop, $value ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		try {
			if ( empty( $value ) ) {
				$this->set_prop( $prop, null );

				return;
			}

			if ( is_a( $value, 'DateTime' ) ) { // @phpstan-ignore-line
				$datetime = $value;
			} elseif ( is_numeric( $value ) ) {
				// Timestamps are handled as UTC timestamps in all cases.
				$datetime = new DateTime( "@{$value}", new DateTimeZone( 'UTC' ) );
			} else {
				// Strings are defined in local WP timezone. Convert to UTC.
				if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
					$offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : rfd_timezone_offset();
					$timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
				} else {
					$timestamp = rfd_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', rfd_string_to_timestamp( $value ) ) ) );
				}
				$datetime = new DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
			}

			// Set local timezone or offset.
			if ( get_option( 'timezone_string' ) ) {
				$datetime->set_timezone( new DateTimeZone( rfd_timezone_string() ) );
			} else {
				$datetime->set_utc_offset( rfd_timezone_offset() );
			}

			$this->set_prop( $prop, $datetime );
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		}
	}

	/**
	 * When invalid data is found, throw an exception unless reading from the DB.
	 *
	 * @param string $code Error code.
	 * @param string $message Error message.
	 * @param int $http_status_code HTTP status code.
	 * @param array $data Extra error data.
	 *
	 * @throws Data_Exception Data Exception.
	 */
	protected function error( string $code, string $message, $http_status_code = 400, $data = array() ): void {
		throw new Data_Exception( $code, $message, $http_status_code );
	}
}
