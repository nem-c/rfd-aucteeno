<?php
/**
 * Aucteeno Template
 *
 * Functions for templating system.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Functions
 */

use RFD\Core\View;
use RFD\Aucteeno\Catalog;

/**
 * Should the loop be displayed?
 *
 * @return bool
 */
function aucteeno_catalog_loop(): bool {
	return have_posts() || 'catalogs' !== aucteeno_get_loop_display_mode();
}

/**
 * Get a slug identifying the current theme.
 *
 * @return string
 */
function aucteeno_get_theme_slug_for_templates(): string {
	return apply_filters( 'aucteeno_theme_slug_for_templates', get_option( 'template' ) );
}

/**
 * Sets up the woocommerce_loop global from the passed args or from the main query.
 *
 * @param array $args Args to pass into the global.
 */
function aucteeno_setup_loop( $args = array() ): void {
	$default_args = array(
		'loop'         => 0,
		'columns'      => aucteeno_get_default_products_per_row(),
		'name'         => '',
		'is_shortcode' => false,
		'is_paginated' => true,
		'is_search'    => false,
		'is_filtered'  => false,
		'total'        => 0,
		'total_pages'  => 0,
		'per_page'     => 0,
		'current_page' => 1,
	);

	if ( $GLOBALS['wp_query']->is_main_query() ) {
		$default_args = array_merge(
			$default_args,
			array(
				'is_search'    => $GLOBALS['wp_query']->is_search(),
				'is_filtered'  => is_filtered(),
				'total'        => $GLOBALS['wp_query']->found_posts,
				'total_pages'  => $GLOBALS['wp_query']->max_num_pages,
				'per_page'     => $GLOBALS['wp_query']->get( 'posts_per_page' ),
				'current_page' => max( 1, $GLOBALS['wp_query']->get( 'paged', 1 ) ),
			)
		);
	}

	// Merge any existing values.
	if ( isset( $GLOBALS['aucteeno_loop'] ) ) {
		$default_args = array_merge( $default_args, $GLOBALS['aucteeno_loop'] );
	}

	$GLOBALS['aucteeno_loop'] = wp_parse_args( $args, $default_args );
}

/**
 * Resets the aucteeno_loop global.
 */
function aucteeno_reset_loop(): void {
	unset( $GLOBALS['aucteeno_loop'] );
}

/**
 * Gets a property from the aucteeno_loop global.
 *
 * @param string $prop Prop to get.
 * @param string $default Default if the prop does not exist.
 *
 * @return mixed
 */
function aucteeno_get_loop_prop( string $prop, $default = '' ) {
	aucteeno_setup_loop(); // Ensure shop loop is setup.

	return isset( $GLOBALS['aucteeno_loop'], $GLOBALS['aucteeno_loop'][ $prop ] ) ? $GLOBALS['aucteeno_loop'][ $prop ] : $default;
}

/**
 * Sets a property in the woocommerce_loop global.
 *
 * @param string $prop Prop to set.
 * @param string $value Value to set.
 */
function aucteeno_set_loop_prop( string $prop, $value = '' ): void {
	if ( ! isset( $GLOBALS['aucteeno_loop'] ) ) {
		aucteeno_setup_loop();
	}
	$GLOBALS['aucteeno_loop'][ $prop ] = $value;
}

/**
 * Page Title function.
 *
 * @param bool $echo Should echo title.
 *
 * @return string|void
 */
function aucteeno_page_title( $echo = true ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
	if ( true === is_search() ) {
		/* translators: %s: search query */
		$page_title = sprintf( __( 'Search results: &ldquo;%s&rdquo;', 'rfd-aucteeno' ), get_search_query() );

		if ( get_query_var( 'paged' ) ) {
			/* translators: %s: page number */
			$page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'rfd-aucteeno' ), get_query_var( 'paged' ) );
		}
	} elseif ( true === is_tax() ) {
		$page_title = single_term_title( '', false );
	} else {
		$page_title = get_the_title( acn_get_catalogs_page_id() );
	}

	$page_title = apply_filters( 'aucteeno_page_title', $page_title );

	if ( true === $echo ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $page_title;
	} else {
		return $page_title;
	}
}

/**
 * See what is going to display in the loop.
 *
 * @return string Either products, subcategories, or both, based on current page.
 */
function aucteeno_get_loop_display_mode(): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	// Only return products when filtering things.
	if ( aucteeno_get_loop_prop( 'is_search' ) || aucteeno_get_loop_prop( 'is_filtered' ) ) {
		return 'catalogs';
	}

	$parent_id    = 0;
	$display_type = '';

	if ( true === is_catalogs_page() ) {
		$display_type = get_option( '_acn_shop_page_display', '' );
	} elseif ( is_catalog_category() ) {
		$parent_id    = get_queried_object_id();
		$display_type = get_term_meta( $parent_id, 'display_type', true );
		$display_type = '' === $display_type ? get_option( '_acn_category_archive_display', '' ) : $display_type;
	}

	if ( ( false === is_catalogs_page() || 'subcategories' !== $display_type ) && 1 < aucteeno_get_loop_prop( 'current_page' ) ) {
		return 'catalogs';
	}

	// Ensure valid value.
	if ( '' === $display_type || ! in_array( $display_type, array( 'catalogs', 'subcategories', 'both' ), true ) ) {
		$display_type = 'catalogs';
	}

	// If we're showing categories, ensure we actually have something to show.
	if ( in_array( $display_type, array( 'subcategories', 'both' ), true ) ) {
		$subcategories = aucteeno_get_catalog_subcategories( $parent_id );

		if ( empty( $subcategories ) ) {
			$display_type = 'catalogs';
		}
	}

	return $display_type;
}

/**
 * Get the default columns setting - this is how many products will be shown per row in loops.
 *
 * @return int
 */
function aucteeno_get_default_products_per_row(): int {
	$columns = get_option( 'aucteeno_catalog_columns', 4 );

	$min_columns = 4;
	$max_columns = 6;

	if ( $columns < $min_columns ) {
		$columns = $min_columns;
		update_option( 'aucteeno_catalog_columns', $columns );
	} elseif ( $columns > $max_columns ) {
		$columns = $max_columns;
		update_option( 'aucteeno_catalog_columns', $columns );
	}

	$columns = absint( $columns );

	return max( 1, $columns );
}

/**
 * Add default product tabs to product pages.
 *
 * @param array $tabs Array of tabs.
 *
 * @return array
 */
function aucteeno_default_catalog_tabs( $tabs = array() ) {
	global $catalog, $post;

	// Description tab - shows product content.
	if ( $post->post_content ) {
		$tabs['description'] = array(
			'title'    => __( 'Description', 'rfd-aucteeno' ),
			'priority' => 10,
			'callback' => 'aucteeno_catalog_description_tab',
		);
	}

	return $tabs;
}

/**
 * Output the start of a product loop. By default this is a UL.
 *
 * @param bool $echo Should echo?.
 *
 * @return string|void
 */
function aucteeno_catalog_loop_start( $echo = true ) {
	aucteeno_set_loop_prop( 'loop', '0' );

	$loop_start = View::get_template_html(
		'loop/loop-start.php',
		array(),
		'',
		RFD_AUCTEENO_TEMPLATES_DIR
	);

	$loop_start = apply_filters( 'aucteeno_catalog_loop_start', $loop_start );

	if ( true === $echo ) {
		echo $loop_start; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		return $loop_start;
	}
}

/**
 * Output the end of a product loop. By default this is a UL.
 *
 * @param bool $echo Should echo?.
 *
 * @return string|void
 */
function aucteeno_catalog_loop_end( $echo = true ) {

	$loop_end = View::get_template_html(
		'loop/loop-end.php',
		array(),
		'',
		RFD_AUCTEENO_TEMPLATES_DIR
	);

	$loop_end = apply_filters( 'aucteeno_catalog_loop_end', $loop_end );

	if ( $echo ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $loop_end;
	} else {
		return $loop_end;
	}
}

/**
 * Display the classes for the product div.
 *
 * @param string|array $class One or more classes to add to the class list.
 * @param int|WP_Post|Catalog $catalog_id Catalog ID or catalog object.
 */
function aucteeno_catalog_class( $class = '', $catalog_id = null ): void {
	try {
		echo 'class="' . esc_attr( implode( ' ', aucteeno_get_catalog_class( $class, $catalog_id ) ) ) . '"';
	} catch ( Exception $exception ) {
		echo 'class="error"';
	}
}

/**
 * Retrieves the classes for the post div as an array.
 *
 * @param string|array $class Class string.
 * @param int|WP_Post|Catalog $catalog Product ID or product object.
 *
 * @return array
 * @throws Exception Exception.
 */
function aucteeno_get_catalog_class( $class = '', $catalog = null ): array { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
	if ( is_null( $catalog ) && ! empty( $GLOBALS['catalog'] ) ) {
		// Catalog was null so pull from global.
		$catalog = $GLOBALS['product'];
	}

	if ( $catalog && false === is_a( $catalog, 'RFD\Aucteeno\Catalog' ) ) {
		// Make sure we have a valid product, or set to false.
		try {
			$catalog = acn_get_catalog( $catalog );
		} catch ( Exception $exception ) {
			return array();
		}
	}

	if ( $class ) {
		if ( false === is_array( $class ) ) {
			$class = preg_split( '#\s+#', $class );
		}
	} else {
		$class = array();
	}

	if ( false === $class ) {
		$class = array();
	}

	$post_classes = array_map( 'esc_attr', $class );

	if ( true === empty( $catalog ) ) {
		return $post_classes;
	}

	$classes = array_merge(
		$post_classes,
		array(
			'catalog',
			'type-catalog',
			'post-' . $catalog->get_id(),
			'status-' . $catalog->get_status(),
		)
	);

	/**
	 * Post Class filter.
	 *
	 * @param array $classes Array of CSS classes.
	 * @param Catalog $catalog Product object.
	 *
	 * @since 3.6.2
	 */
	$classes = apply_filters( 'aucteeno_post_class', $classes, $catalog ); // @phpstan-ignore-line

	return array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
}

/**
 * When the_post is called, put product data into a global.
 *
 * @param mixed $post Post Object.
 *
 * @return Catalog|false
 */
function aucteeno_setup_catalog_data( $post ) {
	unset( $GLOBALS['catalog'] );

	if ( is_int( $post ) ) {
		$post = get_post( $post );
	}

	if (
		true === empty( $post->post_type ) ||
		false === in_array( $post->post_type, array( 'catalog' ), true )
	) {
		return false;
	}

	try {
		$GLOBALS['catalog'] = acn_get_catalog( $post );
	} catch ( Exception $exception ) {
		return false;
	}


	return $GLOBALS['catalog'];
}

/**
 * Output the related products.
 *
 * @param array $args Provided arguments.
 */
function aucteeno_related_catalogs( $args = array() ): void {
	global $catalog;

	if ( true === empty( $catalog ) ) {
		return;
	}

	$defaults = array(
		'posts_per_page' => 2,
		'columns'        => 2,
		'orderby'        => 'rand',
		'order'          => 'desc',
	);

	$args = wp_parse_args( $args, $defaults );

	/** TODO:
	 * // Get visible related products then sort them at random.
	 * $args['related_products'] = array_filter( array_map( 'aucteeno_get_product', aucteeno_get_related_products( $catalog->get_id(), $args['posts_per_page'] ) ), 'aucteeno_catalogs_array_filter_visible' );
	 *
	 * // Handle orderby.
	 * $args['related_products'] = aucteeno_catalogs_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );
	 */
	// Set global loop values.
	aucteeno_set_loop_prop( 'name', 'related' );
	aucteeno_set_loop_prop( 'columns', apply_filters( 'aucteeno_related_catalogs_columns', $args['columns'] ) );

	aucteeno_get_template( 'single-catalog/related.php', $args );
}

/**
 * Get the product thumbnail, or the placeholder if not set.
 *
 * @param string $size (default: 'aucteeno_thumbnail').
 *
 * @return string
 */
function aucteeno_get_catalog_thumbnail( $size = 'aucteeno_thumbnail' ): string {
	global $catalog;

	$image_size = apply_filters( 'single_catalog_archive_thumbnail_size', $size );

	return $catalog ? $catalog->get_image( $image_size ) : '';
}

/**
 * Insert the opening anchor tag for products in the loop.
 */
function aucteeno_template_loop_catalog_link_open(): void {
	global $catalog;

	$link = apply_filters( 'aucteeno_loop_product_link', get_the_permalink(), $catalog ); // @phpstan-ignore-line

	echo '<a href="' . esc_url( $link ) . '" class="aucteeno-LoopCatalog-link aucteeno-loop-catalog__link">';
}

/**
 * Insert the closing anchor tag for products in the loop.
 */
function aucteeno_template_loop_catalog_link_close(): void {
	echo '</a>';
}

/**
 * Get the product thumbnail for the loop.
 */
function aucteeno_template_loop_catalog_thumbnail(): void {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo aucteeno_get_catalog_thumbnail();
}

/**
 * Show the product title in the product loop. By default this is an H2.
 */
function aucteeno_template_loop_catalog_title(): void {
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<h2 class="' . esc_attr( apply_filters( 'aucteeno_catalog_loop_title_classes', 'aucteeno-loop-catalog__title' ) ) . '">' . get_the_title() . '</h2>';
}

/**
 * Get the view catalog template for the loop.
 *
 * @param array $args Arguments.
 */
function aucteeno_template_loop_view_catalog( array $args ): void {
	global $catalog;

	if ( true === empty( $catalog ) ) {
		return;
	}
	$defaults = array(
		'class'      => implode(
			' ',
			array_filter(
				array(
					'button',
				)
			)
		),
		'attributes' => array(
			'data-catalog_id' => $catalog->get_id(),
			'aria-label'      => $catalog->view_catalog_description(),
		),
	);

	$args = apply_filters( 'aucteeno_loop_view_catalog_args', wp_parse_args( $args, $defaults ), $catalog ); // @phpstan-ignore-line

	if ( isset( $args['attributes']['aria-label'] ) ) {
		$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
	}

	aucteeno_get_template( 'loop/view-catalog.php', $args );
}

/**
 * Output the product title.
 */
function aucteeno_template_single_title(): void {
	aucteeno_get_template( 'single-catalog/title.php' );
}

/**
 * Output the product short description (excerpt).
 */
function aucteeno_template_single_excerpt(): void {
	aucteeno_get_template( 'single-catalog/short-description.php' );
}

/**
 * Output the product meta.
 */
function aucteeno_template_single_meta() {
	aucteeno_get_template( 'single-catalog/meta.php' );
}

/**
 * Output the product tabs.
 */
function aucteeno_output_catalog_data_tabs(): void {
	aucteeno_get_template( 'single-catalog/tabs/tabs.php' );
}

/**
 * Output the description tab content.
 */
function aucteeno_catalog_description_tab(): void {
	aucteeno_get_template( 'single-catalog/tabs/description.php' );
}

/**
 * Related catalogs output.
 */
function aucteeno_output_related_catalogs(): void {
	$args = array(
		'posts_per_page' => 4,
		'columns'        => 4,
		'orderby'        => 'rand', // @codingStandardsIgnoreLine.
	);

	aucteeno_related_catalogs( apply_filters( 'aucteeno_output_related_catalog_args', $args ) );
}
