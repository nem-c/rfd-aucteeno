<?php
/**
 * Object Data Store Interface
 *
 * @package RFD\Core
 * @subpackage RFD\Core\Contracts
 */

namespace RFD\Core\Contracts;

use RFD\Core\Abstracts\Data;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * WC Data Store Interface
 *
 * @version  3.0.0
 */
interface Object_Data_Store_Interface {
	/**
	 * Method to create a new record of a Data based object.
	 *
	 * @param Data $object Data object.
	 */
	public function create( Data &$object );

	/**
	 * Method to read a record. Creates a new Data based object.
	 *
	 * @param Data $object Data object.
	 */
	public function read( Data &$object );

	/**
	 * Updates a record in the database.
	 *
	 * @param Data $object Data object.
	 */
	public function update( Data &$object );

	/**
	 * Deletes a record from the database.
	 *
	 * @param Data $object Data object.
	 * @param array $args Array of args to pass to the delete method.
	 *
	 * @return bool result
	 */
	public function delete( Data &$object, $args = array() ): bool;

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param Data $object Data object.
	 *
	 * @return array
	 */
	public function read_meta( Data &$object ): array;

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param Data $object Data object.
	 * @param object $meta Meta object (containing at least ->id).
	 */
	public function delete_meta( Data &$object, object $meta ): void;

	/**
	 * Add new piece of meta.
	 *
	 * @param Data $object Data object.
	 * @param object $meta Meta object (containing ->key and ->value).
	 *
	 * @return int|false meta ID
	 */
	public function add_meta( Data &$object, object $meta );

	/**
	 * Update meta.
	 *
	 * @param Data $object Data object.
	 * @param object $meta Meta object (containing ->id, ->key and ->value).
	 */
	public function update_meta( Data &$object, object $meta );
}
