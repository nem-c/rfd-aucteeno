<?php

/**
 * Class for parameter-based Catalog querying
 *
 * Args and usage: https://github.com/woocommerce/woocommerce/wiki/wc_get_products-and-WC_Product_Query
 *
 * @package  RFD\Aucteeno
 */

namespace RFD\Aucteeno\Queries;

use Exception;
use RFD\Core\Object_Query;
use RFD\Aucteeno\Data_Stores\Data_Store;

defined( 'ABSPATH' ) || exit;

/**
 * Class Catalog_Query
 */
class Catalog_Query extends Object_Query {
	/**
	 * Valid query vars for products.
	 *
	 * @return array
	 */
	protected function get_default_query_vars(): array {
		return array_merge(
			parent::get_default_query_vars(),
			array(
				'status'        => array( 'draft', 'pending', 'private', 'publish' ),
				'limit'         => get_option( 'posts_per_page' ),
				'include'       => array(),
				'date_created'  => '',
				'date_modified' => '',
				'featured'      => '',
				'category'      => array(),
				'tag'           => array(),
			)
		);
	}

	/**
	 * Get products matching the current query vars.
	 *
	 * @return array|object of WC_Product objects
	 * @throws Exception Exception.
	 */
	public function get_catalogs(): array {
		$args    = apply_filters( 'aucteeno_catalog_object_query_args', $this->get_query_vars() );
		$results = Data_Store::load( 'catalog' )->query( $args );

		return apply_filters( 'aucteeno_product_object_query', $results, $args );
	}
}