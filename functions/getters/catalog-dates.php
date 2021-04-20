<?php
/**
 * Catalog Dates Getter methods.
 *
 * @package RFD\Aucteeno
 */

if ( false === function_exists( 'acn_get_catalog_date_promoted' ) ) {
	/**
	 * Get catalog promoted date value.
	 *
	 * @param WP_Post|int $post Post object or post ID.
	 * @param array $meta Array of meta values.
	 *
	 * @return string
	 */
	function acn_get_catalog_date_promoted( $post, $meta = array() ): string {

		$post = get_post( $post );
		if ( false === empty( $meta ) ) {
			$meta_item = $meta[ RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED ] ?? array();
			$value     = current( $meta_item );
		} else {
			$value = \RFD\Aucteeno\Globals::get_catalog_meta( $post->ID, RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED, true );
		}

		return $value;
	}
}
if ( false === function_exists( 'acn_get_catalog_date_start' ) ) {
	/**
	 * Get catalog start date value.
	 *
	 * @param WP_Post|int $post Post object or post ID.
	 * @param array $meta Array of meta values.
	 *
	 * @return string
	 */
	function acn_get_catalog_date_start( $post, $meta = array() ): string {

		$post = get_post( $post );
		if ( false === empty( $meta ) ) {
			$meta_item = $meta[ RFD_AUCTEENO_CATALOG_META_DATETIME_START ] ?? array();
			$value     = current( $meta_item );
		} else {
			$value = \RFD\Aucteeno\Globals::get_catalog_meta( $post->ID, RFD_AUCTEENO_CATALOG_META_DATETIME_START, true );
		}

		return $value;
	}
}
if ( false === function_exists( 'acn_get_catalog_date_start_timezone' ) ) {
	/**
	 * Get catalog start date value.
	 *
	 * @param WP_Post|int $post Post object or post ID.
	 * @param array $meta Array of meta values.
	 *
	 * @return string
	 */
	function acn_get_catalog_date_start_timezone( $post, $meta = array() ): string {

		$post = get_post( $post );
		if ( false === empty( $meta ) ) {
			$meta_item = $meta[ RFD_AUCTEENO_CATALOG_META_DATETIME_START ] ?? array();
			$value     = current( $meta_item );
		} else {
			$value = \RFD\Aucteeno\Globals::get_catalog_meta( $post->ID, RFD_AUCTEENO_CATALOG_META_DATETIME_START, true );
		}

		return $value;
	}
}
