<?php
/**
 * Aucteeno Catalog class.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno;

use RFD\Core\Abstracts\Data;
use RFD\Aucteeno\Data_Stores\Data_Store;

/**
 * Class Catalog
 */
class Catalog extends Data {
	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'catalog';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'catalogs';

	/**
	 * Stores product data.
	 *
	 * @var array
	 */
	protected $data = array(
		'name'                => '',
		'slug'                => '',
		'date_created'        => null,
		'date_modified'       => null,
		'status'              => false,
		'featured'            => false,
		'date_promoted'       => '',
		'date_start_gmt'      => '',
		'date_start'          => '',
		'date_start_timezone' => '',
		'date_end_gmt'        => '',
		'date_end'            => '',
		'date_end_timezone'   => '',
		'image_id'            => '',
		'gallery_image_ids'   => array(),
		'category_ids'        => array(),
		'tag_ids'             => array(),
		'attributes'          => array(),
	);

	/**
	 * Get the catalog if ID is passed, otherwise the catalog is new and empty.
	 * This class should NOT be instantiated, but the acn_get_catalogs() function
	 * should be used. It is possible, but the wc_get_product() is preferred.
	 *
	 * @param int|Catalog|object $catalog Catalog to init.
	 */
	public function __construct( $catalog = 0 ) {
		if ( is_numeric( $catalog ) && $catalog > 0 ) {
			$this->set_id( $catalog );
		} elseif ( $catalog instanceof self ) {
			$this->set_id( absint( $catalog->get_id() ) );
		} elseif ( false === empty( $product->ID ) ) {
			$this->set_id( absint( $product->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = Data_Store::load( 'catalog' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/**
	 * Get product slug.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_slug( $context = 'view' ) {
		return $this->get_prop( 'slug', $context );
	}

	/**
	 * Get product created date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get product modified date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ) {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Get product status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_status( $context = 'view' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * If the product is featured.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return boolean
	 */
	public function get_featured( $context = 'view' ) {
		return $this->get_prop( 'featured', $context );
	}

	/**
	 * Get category ids.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array
	 */
	public function get_category_ids( $context = 'view' ) {
		return $this->get_prop( 'category_ids', $context );
	}

	/**
	 * Get tag ids.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array
	 */
	public function get_tag_ids( $context = 'view' ) {
		return $this->get_prop( 'tag_ids', $context );
	}

	/**
	 * Returns the gallery attachment ids.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array
	 */
	public function get_gallery_image_ids( $context = 'view' ) {
		return $this->get_prop( 'gallery_image_ids', $context );
	}

	/**
	 * Get main image ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since  3.0.0
	 */
	public function get_image_id( $context = 'view' ) {
		return $this->get_prop( 'image_id', $context );
	}
}
