<?php
/**
 * Aucteeno Catalog Functions
 *
 * Functions for catalog specific things.
 *
 * @package  RFD\Aucteeno
 */

function acn_get_catalogs( $args ) {
	$args = wp_parse_args( $args, array(
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
	) );

	// Handle some BW compatibility arg names where wp_query args differ in naming.
	$map_legacy = array(
		'numberposts'    => 'limit',
		'post_status'    => 'status',
		'post_parent'    => 'parent',
		'posts_per_page' => 'limit',
		'paged'          => 'page',
	);

	foreach ( $map_legacy as $from => $to ) {
		if ( isset( $args[ $from ] ) ) {
			$args[ $to ] = $args[ $from ];
		}
	}

	return WC_Data_Store::load( 'product' )->get_products( $args );
}