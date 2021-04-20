<?php

use RFD\Core\Data_Store_WP;
use RFD\Core\Contracts\Object_Data_Store;

class Catalog_Data_Store_Cpt extends Data_Store_WP implements Object_Data_Store {

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
		RFD_AUCTEENO_CATALOG_META_LOCATION_STATE_ISO2,
		RFD_AUCTEENO_CATALOG_META_LOCATION_COUNTRY_ISO2,
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
		RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT
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

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	public function create( &$object ) {
		if ( ! $object->get_date_created( 'edit' ) ) {
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
					'comment_status' => $object->get_reviews_allowed() ? 'open' : 'closed',
					'ping_status'    => 'closed',
					'menu_order'     => $object->get_menu_order(),
					'post_password'  => $object->get_post_password( 'edit' ),
					'post_date'      => gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->getOffsetTimestamp() ),
					'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->getTimestamp() ),
					'post_name'      => $object->get_slug( 'edit' ),
				)
			),
			true
		);

		if ( $id && false === is_wp_error( $id ) ) {
			$object->set_id( $id );

			$this->update_post_meta( $object, true );
			$this->update_terms( $object, true );
			$this->update_visibility( $object, true );
			$this->update_attributes( $object, true );
			$this->update_version_and_type( $object );
			$this->handle_updated_props( $object );
			$this->clear_caches( $object );

			$object->save_meta_data();
			$object->apply_changes();

			do_action( 'aucteeno_new_catalog', $id, $object );
		}
	}

	public function read( &$object ) {
		$object->set_defaults();
		$post_object = get_post( $object->get_id() );

		if ( true === empty( $object->get_id() ) || false === $post_object || 'catalog' !== $post_object->post_type ) {
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
				'reviews_allowed'   => 'open' === $post_object->comment_status,
			)
		);

		$this->read_attributes( $object );
		$this->read_downloads( $object );
		$this->read_visibility( $object );
		$this->read_product_data( $object );
		$this->read_extra_data( $object );
		$object->set_object_read( true );

		do_action( 'aucteeno_catalog_read', $object->get_id() );
	}

	public function update( &$object ) {
		$object->save_meta_data();
		$changes = $object->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect( array(
			'description',
			'short_description',
			'name',
			'parent_id',
			'reviews_allowed',
			'status',
			'menu_order',
			'date_created',
			'date_modified',
			'slug'
		), array_keys( $changes ) ) ) {
			$post_data = array(
				'post_content'   => $object->get_description( 'edit' ),
				'post_excerpt'   => $object->get_short_description( 'edit' ),
				'post_title'     => $object->get_name( 'edit' ),
				'post_parent'    => $object->get_parent_id( 'edit' ),
				'comment_status' => $object->get_reviews_allowed( 'edit' ) ? 'open' : 'closed',
				'post_status'    => $object->get_status( 'edit' ) ? $object->get_status( 'edit' ) : 'publish',
				'menu_order'     => $object->get_menu_order( 'edit' ),
				'post_password'  => $object->get_post_password( 'edit' ),
				'post_name'      => $object->get_slug( 'edit' ),
				'post_type'      => 'product',
			);
			if ( $object->get_date_created( 'edit' ) ) {
				$post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->getOffsetTimestamp() );
				$post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $object->get_date_created( 'edit' )->getTimestamp() );
			}
			if ( isset( $changes['date_modified'] ) && $object->get_date_modified( 'edit' ) ) {
				$post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $object->get_date_modified( 'edit' )->getOffsetTimestamp() );
				$post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $object->get_date_modified( 'edit' )->getTimestamp() );
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
		$this->update_terms( $object );
		$this->update_visibility( $object );
		$this->update_attributes( $object );
		$this->update_version_and_type( $object );
		$this->handle_updated_props( $object );
		$this->clear_caches( $object );

		$object->apply_changes();

		do_action( 'aucteeno_update_product', $object->get_id(), $object );
	}

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
}