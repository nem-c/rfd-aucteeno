<?php
/**
 * Catalog Data Store
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Data_Stores
 */

namespace RFD\Aucteeno\Data_Stores;

use Exception;
use RFD\Aucteeno\Catalog;
use RFD\Core\Data_Store_WP;
use RFD\Core\Contracts\Object_Data_Store_Interface;
use RFD\Aucteeno\Queries\Catalog_Query;
use RFD\Core\DateTime;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Catalog_Data_Store_Cpt
 */
class Catalog_Data_Store_Cpt extends Data_Store_WP implements Object_Data_Store_Interface {

	/**
	 * Data stored in meta keys, but not considered "meta".
	 *
	 * @var array
	 */
	protected $internal_meta_keys = array(
		RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED,
		RFD_AUCTEENO_CATALOG_META_DATETIME_START,
		RFD_AUCTEENO_CATALOG_META_DATETIME_START_TIMEZONE,
		RFD_AUCTEENO_CATALOG_META_DATETIME_START_GMT,
		RFD_AUCTEENO_CATALOG_META_DATETIME_END,
		RFD_AUCTEENO_CATALOG_META_DATETIME_END_TIMEZONE,
		RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT,
		RFD_AUCTEENO_CATALOG_META_LOCATION_ADDRESS,
		RFD_AUCTEENO_CATALOG_META_LOCATION_ADDRESS_2,
		RFD_AUCTEENO_CATALOG_META_LOCATION_CITY,
		RFD_AUCTEENO_CATALOG_META_LOCATION_POSTAL_CODE,
		RFD_AUCTEENO_CATALOG_META_LOCATION_STATE,
		RFD_AUCTEENO_CATALOG_META_LOCATION_COUNTRY_ISO2,
		RFD_AUCTEENO_CATALOG_META_LOCATION_LONGITUDE,
		RFD_AUCTEENO_CATALOG_META_LOCATION_LATITUDE,
		RFD_AUCTEENO_CATALOG_META_IS_ONLINE,
		RFD_AUCTEENO_CATALOG_META_ONLINE_URL,
	);

	/**
	 * Meta data which should exist in the DB, even if empty.
	 *
	 * @var array
	 */
	protected $must_exist_meta_keys = array(
		RFD_AUCTEENO_CATALOG_META_IS_ONLINE,
		RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED,
		RFD_AUCTEENO_CATALOG_META_DATETIME_START_GMT,
		RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT,
	);

	/**
	 * If we have already saved our extra data, don't do automatic / default handling.
	 *
	 * @var bool
	 */
	protected $extra_data_saved = false;

	/**
	 * Stores updated props.
	 *
	 * @var array
	 */
	protected $updated_props = array();

	/**
	 * |--------------------------------------------------------------------------
	 * | CRUD Methods
	 * |--------------------------------------------------------------------------
	 */

	/**
	 * Create Catalog.
	 *
	 * @param Catalog $object Catalog object.
	 */
	public function create( &$object ): void {
		if ( true === empty( $object->get_date_created( 'edit' ) ) ) {
			$object->set_date_created( time() );
		}

		$id = wp_insert_post(
			apply_filters(
				'aucteeno_new_catalog_data',
				array(
					'post_type'      => 'catalog',
					'post_status'    => $object->get_status() ? $object->get_status() : 'publish',
					'post_author'    => get_current_user_id(),
					'post_title'     => $object->get_name() ? $object->get_name() : __( 'Catalog', 'rfd-aucteeno' ),
					'post_content'   => $object->get_description(),
					'post_excerpt'   => $object->get_short_description(),
					'post_parent'    => $object->get_parent_id(),
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'menu_order'     => 0,
					'post_password'  => $object->get_post_password( 'edit' ),
					'post_date'      => gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->get_offset_timestamp() ),
					'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->get_timestamp() ),
					'post_name'      => $object->get_slug( 'edit' ),
				)
			),
			true
		);

		if ( $id && false === is_wp_error( $id ) ) {
			$object->set_id( $id );

			$this->update_post_meta( $object, true );
			$this->clear_caches( $object );

			$object->save_meta_data();
			$object->apply_changes();

			do_action( 'aucteeno_new_catalog', $id, $object );
		}
	}

	/**
	 * Read Catalog.
	 *
	 * @param Catalog $object Catalog object.
	 *
	 * @throws Exception Exception.
	 */
	public function read( &$object ): void {
		$object->set_defaults();
		$post_object = get_post( $object->get_id() );

		if ( true === empty( $object->get_id() ) || true === empty( $post_object ) ) {
			throw new Exception( __( 'Invalid product.', 'rfd-aucteeno' ) );
		}

		if ( 'catalog' !== $post_object->post_type ) {
			throw new Exception( __( 'Invalid product.', 'rfd-aucteeno' ) );
		}

		$object->set_props(
			array(
				'name'              => $post_object->post_title,
				'slug'              => $post_object->post_name,
				'date_created'      => $this->string_to_timestamp( $post_object->post_date_gmt ),
				'date_modified'     => $this->string_to_timestamp( $post_object->post_modified_gmt ),
				'status'            => $post_object->post_status,
				'description'       => $post_object->post_content,
				'short_description' => $post_object->post_excerpt,
				'parent_id'         => $post_object->post_parent,
				'menu_order'        => $post_object->menu_order,
				'post_password'     => $post_object->post_password,
			)
		);

		$this->read_catalog_data( $object );
		$this->read_extra_data( $object );
		$object->set_object_read( true );

		do_action( 'aucteeno_catalog_read', $object->get_id() );
	}

	/**
	 * Update Catalog.
	 *
	 * @param Catalog $object Catalog Object.
	 */
	public function update( &$object ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		$object->save_meta_data();
		$changes = $object->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect(
			array(
				'description',
				'short_description',
				'name',
				'parent_id',
				'reviews_allowed',
				'status',
				'menu_order',
				'date_created',
				'date_modified',
				'slug',
			),
			array_keys( $changes )
		)
		) {
			$post_data = array(
				'post_content'   => $object->get_description( 'edit' ),
				'post_excerpt'   => $object->get_short_description( 'edit' ),
				'post_title'     => $object->get_name( 'edit' ),
				'post_parent'    => 0,
				'comment_status' => 'closed',
				'post_status'    => $object->get_status( 'edit' ) ? $object->get_status( 'edit' ) : 'publish',
				'menu_order'     => 0,
				'post_password'  => $object->get_post_password( 'edit' ),
				'post_name'      => $object->get_slug( 'edit' ),
				'post_type'      => 'product',
			);
			if ( false === empty( $object->get_date_created( 'edit' ) ) ) {
				$post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->get_offset_timestamp() );
				$post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->get_timestamp() );
			}
			if ( isset( $changes['date_modified'] ) && false === empty( $object->get_date_modified( 'edit' ) ) ) {
				$post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $object->get_date_modified( 'edit' )->get_offset_timestamp() );
				$post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $object->get_date_modified( 'edit' )->get_timestamp() );
			} else {
				$post_data['post_modified']     = current_time( 'mysql' );
				$post_data['post_modified_gmt'] = current_time( 'mysql', 1 );
			}

			/**
			 * When updating this object, to prevent infinite loops, use $wpdb
			 * to update data, since wp_update_post spawns more calls to the
			 * save_post action.
			 *
			 * This ensures hooks are fired by either WP itself (admin screen save),
			 * or an update purely from CRUD.
			 */
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $object->get_id() ) );
				clean_post_cache( $object->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $object->get_id() ), $post_data ) );
			}
			$object->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

		} else { // Only update post modified time to record this save event.
			$GLOBALS['wpdb']->update(
				$GLOBALS['wpdb']->posts,
				array(
					'post_modified'     => current_time( 'mysql' ),
					'post_modified_gmt' => current_time( 'mysql', 1 ),
				),
				array(
					'ID' => $object->get_id(),
				)
			);
			clean_post_cache( $object->get_id() );
		}

		$this->update_post_meta( $object );
		$this->clear_caches( $object );

		$object->apply_changes();

		do_action( 'aucteeno_update_catalog', $object->get_id(), $object );
	}

	/**
	 * Delete Catalog.
	 *
	 * @param Catalog $object Catalog object.
	 * @param array $args Arguments.
	 *
	 * @return bool
	 */
	public function delete( &$object, $args = array() ): bool {
		$id = $object->get_id();

		$args = wp_parse_args(
			$args,
			array(
				'force_delete' => false,
			)
		);

		if ( true === empty( $id ) ) {
			return false;
		}

		if ( $args['force_delete'] ) {
			do_action( 'aucteeno_before_delete_catalog', $id );
			wp_delete_post( $id );
			$object->set_id( 0 );
			do_action( 'aucteeno_delete_catalog', $id );
		} else {
			wp_trash_post( $id );
			$object->set_status( 'trash' );
			do_action( 'aucteeno_trash_catalog', $id );
		}

		return true;
	}

	/**
	 * Returns an array of catalogs.
	 *
	 * @param array $args Args to pass to WC_Catalog_Query().
	 *
	 * @return array|object
	 * @throws Exception Exception.
	 * @see acn_get_catalogs
	 */
	public function get_catalogs( $args = array() ) {
		$query = new Catalog_Query( $args );

		return $query->get_catalogs();
	}

	/**
	 * Read catalog data. Can be overridden by child classes to load other props.
	 *
	 * @param Catalog $object Product object.
	 *
	 * @since 3.0.0
	 */
	protected function read_catalog_data( Catalog &$object ): void {
		$id               = $object->get_id();
		$post_meta_values = get_post_meta( $id );
		// @codingStandardsIgnoreStart
		$meta_key_to_props = array(
			RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED       => 'datetime_promoted',
			RFD_AUCTEENO_CATALOG_META_DATETIME_START          => 'datetime_start',
			RFD_AUCTEENO_CATALOG_META_DATETIME_START_GMT      => 'datetime_start_gmt',
			RFD_AUCTEENO_CATALOG_META_DATETIME_END            => 'datetime_end',
			RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT        => 'datetime_end_gmt',
			RFD_AUCTEENO_CATALOG_META_DATETIME_START_TIMEZONE => 'datetime_start_timezone',
			RFD_AUCTEENO_CATALOG_META_DATETIME_END_TIMEZONE   => 'datetime_end_timezone',
		);
		// @codingStandardsIgnoreEnd

		$set_props = array();

		foreach ( $meta_key_to_props as $meta_key => $prop ) {
			$meta_value         = $post_meta_values[ $meta_key ][0] ?? null;
			$set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only unserializes single values.
		}

		/**
		 * TODO:
		 * $set_props['category_ids']      = $this->get_term_ids( $product, 'product_cat' );
		 * $set_props['tag_ids']           = $this->get_term_ids( $product, 'product_tag' );
		 * $set_props['gallery_image_ids'] = array_filter( explode( ',', $set_props['gallery_image_ids'] ) );
		 */

		$object->set_props( $set_props );
	}


	/**
	 * Read extra data associated with the catalog.
	 *
	 * @param Catalog $object Product object.
	 */
	protected function read_extra_data( Catalog &$object ): void {
		foreach ( $object->get_extra_data_keys() as $key ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $object, $function ) ) ) {
				$object->{$function}( get_post_meta( $object->get_id(), '_' . $key, true ) );
			}
		}
	}

	/**
	 * Helper method that updates all the post meta for a catalog based on it's settings.
	 *
	 * @param Catalog $object Product object.
	 * @param bool $force Force update. Used during create.
	 */
	protected function update_post_meta( Catalog &$object, $force = false ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		// @codingStandardsIgnoreStart
		$meta_key_to_props = array(
			RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED       => 'datetime_promoted',
			RFD_AUCTEENO_CATALOG_META_DATETIME_START          => 'datetime_start',
			RFD_AUCTEENO_CATALOG_META_DATETIME_START_GMT      => 'datetime_start_gmt',
			RFD_AUCTEENO_CATALOG_META_DATETIME_END            => 'datetime_end',
			RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT        => 'datetime_end_gmt',
			RFD_AUCTEENO_CATALOG_META_DATETIME_START_TIMEZONE => 'datetime_start_timezone',
			RFD_AUCTEENO_CATALOG_META_DATETIME_END_TIMEZONE   => 'datetime_end_timezone',
		);
		// @codingStandardsIgnoreEnd

		// Make sure to take extra data (like product url or text for external products) into account.
		$extra_data_keys = $object->get_extra_data_keys();

		foreach ( $extra_data_keys as $key ) {
			$meta_key_to_props[ RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_' . $key ] = $key;
		}

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $object, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value = $object->{"get_$prop"}( 'edit' );
			if ( true === is_string( $value ) ) {
				/* @var string $value String value. */
				$value = wp_slash( $value );
			}

			switch ( $prop ) {
				case 'is_online':
					$value = rfd_bool_to_string( $value );
					break;
				case 'gallery_image_ids':
					/* @var array $value list of gallery image ids. */
					$value = implode( ',', $value );
					break;
				case 'datetime_promoted':
				case 'datetime_start':
				case 'datetime_start_gmt':
				case 'datetime_end':
				case 'datetime_end_gmt':
					/* @var DateTime $value DateTime object. */
					$value = $value ? $value->format( 'Y-m-d H:i:s' ) : '';
					break;
			}

			$updated = $this->update_or_delete_post_meta( $object, $meta_key, $value );

			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		// Update extra data associated with the product like button text or product URL for external products.
		if ( false === $this->extra_data_saved ) {
			foreach ( $extra_data_keys as $key ) {
				$meta_key = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_' . $key;
				$function = 'get_' . $key;
				if ( ! array_key_exists( $meta_key, $props_to_update ) ) {
					continue;
				}
				if ( is_callable( array( $object, $function ) ) ) {
					$value   = $object->{$function}( 'edit' );
					$value   = is_string( $value ) ? wp_slash( $value ) : $value;
					$updated = $this->update_or_delete_post_meta( $object, $meta_key, $value );

					if ( $updated ) {
						$this->updated_props[] = $key;
					}
				}
			}
		}
	}

	/**
	 * Clear any caches.
	 *
	 * @param Catalog $catalog Product object.
	 */
	protected function clear_caches( Catalog &$catalog ): void {
		/* TODO */
	}
}
