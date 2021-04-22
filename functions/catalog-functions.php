<?php
/**
 * Aucteeno Catalog Functions
 *
 * Functions for catalog specific things.
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Functions
 */

use RFD\Aucteeno\Data_Stores\Data_Store;
use RFD\Aucteeno\Catalog;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Standard way of retrieving catalogs based on certain parameters.
 *
 * This function should be used for catalog retrieval so that we have a data agnostic
 * way to get a list of catalogs.
 *
 * @param array $args Array of args (above).
 *
 * @return array|stdClass Number of pages and an array of product objects if paginate is true, or just an array of values.
 * @throws Exception Exception.
 */
function acn_get_catalogs( array $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'status'   => array( 'draft', 'pending', 'private', 'publish' ),
			'parent'   => null,
			'category' => array(),
			'tag'      => array(),
			'limit'    => get_option( 'posts_per_page' ),
			'offset'   => null,
			'page'     => 1,
			'include'  => array(),
			'exclude'  => array(),
			'orderby'  => 'date',
			'order'    => 'DESC',
			'return'   => 'objects',
			'paginate' => false,
		)
	);

	// Handle some BW compatibility arg names where wp_query args differ in naming.
	$map_legacy = array(
		'numberposts'    => 'limit', // @codingStandardsIgnoreLine
		'post_status'    => 'status',
		'post_parent'    => 'parent',
		'posts_per_page' => 'limit', // @codingStandardsIgnoreLine
		'paged'          => 'page',
	);

	foreach ( $map_legacy as $from => $to ) {
		if ( isset( $args[ $from ] ) ) {
			$args[ $to ] = $args[ $from ];
		}
	}

	return Data_Store::load( 'catalog' )->get_catalogs( $args ); // @phpstan-ignore-line
}

/**
 * Main function for returning catalog, uses the Catalog class.
 *
 * This function should only be called after 'init' action is finished, as there might be taxonomies that are getting
 * registered during the init action.
 *
 * @param mixed $the_catalog Catalog object or post ID of the catalog.
 *
 * @return Catalog|false
 * @throws Exception Exception.
 */
function acn_get_catalog( $the_catalog = false ) {
	if ( 0 === did_action( 'aucteeno_init' ) || 0 === did_action( 'aucteeno_after_register_taxonomy' ) || 0 === did_action( 'aucteeno_after_register_post_type' ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			/* translators: 1: acn_get_catalog 2: aucteeno_init 3: aucteeno_after_register_taxonomy 4: aucteeno_after_register_post_type */
			sprintf( __( '%1$s should not be called before the %2$s, %3$s and %4$s actions have finished.', 'rfd-aucteeno' ), 'acn_get_product', 'aucteeno_init', 'aucteeno_after_register_taxonomy', 'aucteeno_after_register_post_type' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			RFD_AUCTEENO_VERSION // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);

		return false;
	}

	return new Catalog( $the_catalog );
}
