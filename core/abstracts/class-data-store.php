<?php

namespace RFD\Core\Abstracts;

use Exception;
use RFD\Core\Contracts\Object_Data_Store_Interface;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Data_Store
 */
abstract class Data_Store {
	/**
	 * Contains an instance of the data store class that we are working with.
	 *
	 * @var Data_Store
	 */
	protected $instance = null;

	/**
	 * Contains an array of default supported data stores.
	 * Format of object name => class name.
	 * Example: 'catalog' => 'Catalog_Data_Store_CPT'
	 * Ran through `rfd_data_stores`.
	 *
	 * @var array
	 */
	protected $stores = array();

	/**
	 * Contains the name of the current data store's class name.
	 *
	 * @var string
	 */
	protected $current_class_name = '';

	/**
	 * The object type this store works with.
	 *
	 * @var string
	 */
	protected $object_type = '';

	/**
	 * Tells Data_Store which object store we want to work with.
	 *
	 * @param string $object_type Name of object.
	 *
	 * @throws Exception When validation fails.
	 */
	public function __construct( string $object_type ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		$this->object_type = $object_type;
		$this->stores      = apply_filters( 'rfd_data_stores', $this->stores );

		// If this object type can't be found, check to see if we can load one
		// level up (so if product-type isn't found, we try product).
		if ( false === array_key_exists( $object_type, $this->stores ) ) {
			$pieces      = explode( '-', $object_type );
			$object_type = $pieces[0];
		}

		if ( array_key_exists( $object_type, $this->stores ) ) {
			$store = apply_filters( 'rfd_' . $object_type . '_data_store', $this->stores[ $object_type ] );
			if ( true === is_object( $store ) ) {
				if ( false === $store instanceof Object_Data_Store_Interface ) {
					throw new Exception( __( 'Invalid data store.', 'rfd-core' ) );
				}
				$this->current_class_name = get_class( $store );
				$this->instance           = $store;
			} else {
				if ( false === class_exists( $store ) ) {
					throw new Exception( __( 'Invalid data store.', 'rfd-core' ) );
				}
				$this->current_class_name = $store;
				$this->instance           = new $store();
			}
		} else {
			throw new Exception( __( 'Invalid data store.', 'rfd-core' ) );
		}
	}

	/**
	 * Only store the object type to avoid serializing the data store instance.
	 *
	 * @return array
	 */
	public function __sleep() {
		return array( 'object_type' );
	}

	/**
	 * Re-run the constructor with the object type.
	 *
	 * @throws Exception When validation fails.
	 */
	public function __wakeup() {
		$this->__construct( $this->object_type );
	}

	/**
	 * Loads a data store.
	 *
	 * @param string $object_type Name of object.
	 *
	 * @return Data_Store
	 * @throws Exception When validation fails.
	 */
	public static function load( string $object_type ): Data_Store {
		return new static( $object_type );
	}

	/**
	 * Returns the class name of the current data store.
	 *
	 * @return string
	 */
	public function get_current_class_name(): string {
		return $this->current_class_name;
	}

	/**
	 * Reads an object from the data store.
	 *
	 * @param Data $object Data instance.
	 *
	 * @since 3.0.0
	 */
	public function read( Data &$object ) {
		$this->instance->read( $object );
	}

	/**
	 * Create an object in the data store.
	 *
	 * @param Data $object Data instance.
	 *
	 * @since 3.0.0
	 */
	public function create( Data &$object ) {
		$this->instance->create( $object );
	}

	/**
	 * Update an object in the data store.
	 *
	 * @param Data $object Data instance.
	 */
	public function update( Data &$object ) {
		$this->instance->update( $object );
	}

	/**
	 * Delete an object from the data store.
	 *
	 * @param Data $object Data instance.
	 * @param array $args Array of args to pass to the delete method.
	 */
	public function delete( Data &$object, $args = array() ) {
		$this->instance->delete( $object, $args );
	}

	/**
	 * Data stores can define additional functions
	 * This passes through to the instance if that function exists.
	 *
	 * @param string $method Method.
	 * @param mixed $parameters Parameters.
	 *
	 * @return mixed|void
	 */
	public function __call( string $method, $parameters ) {
		if ( false === is_callable( array( $this->instance, $method ) ) ) {
			return;
		}
		if ( is_callable( array( $this->instance, $method ) ) ) {
			$object     = array_shift( $parameters );
			$parameters = array_merge( array( &$object ), $parameters );

			return $this->instance->$method( ...$parameters );
		}
	}
}
