<?php
/**
 * Wraps an array (meta data for now) and tells if there was any changes.
 *
 * The main idea behind this class is to avoid doing unneeded
 * SQL updates if nothing changed.
 *
 * @package RFD\Core
 */

namespace RFD\Core;

use JsonSerializable;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Meta data class.
 */
class Meta_Data implements JsonSerializable {

	/**
	 * Current data for metadata
	 *
	 * @var array
	 */
	protected $current_data = array();

	/**
	 * Metadata data
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Constructor.
	 *
	 * @param array $meta Data to wrap behind this function.
	 */
	public function __construct( $meta = array() ) {
		$this->current_data = $meta;
		$this->apply_changes();
	}

	/**
	 * When converted to JSON.
	 *
	 * @return array
	 */
	public function jsonSerialize(): array {
		return $this->get_data();
	}

	/**
	 * Merge changes with data and clear.
	 */
	public function apply_changes(): void {
		$this->data = $this->current_data;
	}

	/**
	 * Creates or updates a property in the metadata object.
	 *
	 * @param string $key Key to set.
	 * @param mixed $value Value to set.
	 */
	public function __set( $key, $value ): void {
		$this->current_data[ $key ] = $value;
	}

	/**
	 * Checks if a given key exists in our data. This is called internally
	 * by `empty` and `isset`.
	 *
	 * @param string $key Key to check if set.
	 *
	 * @return bool
	 */
	public function __isset( string $key ): bool {
		return array_key_exists( $key, $this->current_data );
	}

	/**
	 * Returns the value of any property.
	 *
	 * @param string $key Key to get.
	 *
	 * @return mixed Property value or NULL if it does not exists
	 */
	public function __get( string $key ) {
		if ( false === array_key_exists( $key, $this->current_data ) ) {
			return null;
		}

		return $this->current_data[ $key ];
	}

	/**
	 * Return data changes only.
	 *
	 * @return array
	 */
	public function get_changes(): array {
		$changes = array();
		foreach ( $this->current_data as $id => $value ) {
			if ( ! array_key_exists( $id, $this->data ) || $value !== $this->data[ $id ] ) {
				$changes[ $id ] = $value;
			}
		}

		return $changes;
	}

	/**
	 * Return all data as an array.
	 *
	 * @return array
	 */
	public function get_data(): array {
		return $this->data;
	}
}
