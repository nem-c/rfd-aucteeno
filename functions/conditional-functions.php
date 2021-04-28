<?php
/**
 * Aucteeno Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     RFD\Aucteeno
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Returns true if on a page which uses Aucteeno templates.
 *
 * @return bool
 */
function is_aucteeno() {
	return apply_filters( 'is_aucteeno', is_catalogs_page() || is_catalog_taxonomy() || is_catalog() || is_listing() );
}

if ( false === function_exists( 'is_shop' ) ) {
	/**
	 * Returns true when viewing the catalog type archive.
	 *
	 * @return bool
	 */
	function is_catalogs_page(): bool {
		return ( is_post_type_archive( 'catalog' ) || is_page( acn_get_catalogs_page_id() ) );
	}
}

if ( false === function_exists( 'is_catalog_taxonomy' ) ) {

	/**
	 * Returns true when viewing a catalog taxonomy archive.
	 *
	 * @return bool
	 */
	function is_catalog_taxonomy(): bool {
		return is_tax( get_object_taxonomies( 'catalog' ) ); // @phpstan-ignore-line
	}
}

if ( false === function_exists( 'is_catalog_category' ) ) {

	/**
	 * Returns true when viewing a catalog category.
	 *
	 * @param string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 *
	 * @return bool
	 */
	function is_catalog_category( $term = '' ): bool {
		return is_tax( 'catalog_cat', $term );
	}
}

if ( false === function_exists( 'is_catalog_tag' ) ) {

	/**
	 * Returns true when viewing a catalog tag.
	 *
	 * @param string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 *
	 * @return bool
	 */
	function is_catalog_tag( $term = '' ): bool {
		return is_tax( 'catalog_tag', $term );
	}
}

if ( false === function_exists( 'is_catalog' ) ) {

	/**
	 * Returns true when viewing a single catalog.
	 *
	 * @return bool
	 */
	function is_catalog(): bool {
		return is_singular( array( 'catalog' ) );
	}
}

if ( false === function_exists( 'is_listing' ) ) {

	/**
	 * Returns true when viewing a single listing.
	 *
	 * @return bool
	 */
	function is_listing(): bool {
		return is_singular( array( 'listing' ) );
	}
}

if ( false === function_exists( 'is_ajax' ) ) {

	/**
	 * Is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @return bool
	 */
	function is_ajax(): bool {
		return function_exists( 'wp_doing_ajax' ) && wp_doing_ajax();
	}
}

if ( ! function_exists( 'is_filtered' ) ) {

	/**
	 * Is_filtered - Returns true when filtering products using layered nav or price sliders.
	 *
	 * @return bool
	 */
	function is_filtered(): bool {
		return apply_filters( 'aucteeno_is_filtered', false );
	}
}

if ( ! function_exists( 'is_woocommerce_activated' ) ) {

	/**
	 * Check if WooCommerce is activated
	 *
	 * @return bool
	 */
	function is_woocommerce_activated(): bool {
		return class_exists( 'woocommerce' );
	}
}
