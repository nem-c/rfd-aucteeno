<?php
/**
 * Aucteeno Template for Catalogs.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Helpers
 */

namespace RFD\Aucteeno\Template;

use Exception;
use WP_Post;
use RFD\Core\Loader;
use RFD\Core\View;
use RFD\Aucteeno\Catalog;

/**
 * Class Template_Catalog
 */
class Template_Catalog {

	/**
	 * Loop name stored in $GLOBALS.
	 *
	 * @var string
	 */
	private static $loop_name = 'aucteeno_catalogs_loop';

	/**
	 * Catalog_Template_Helper instance.
	 *
	 * @var Template_Catalog
	 */
	protected static $instance;

	/**
	 * Should the loop be displayed?
	 *
	 * @return bool
	 */
	public static function loop(): bool {
		return have_posts() || 'catalogs' !== self::get_loop_display_mode();
	}

	/**
	 * Setup loop.
	 *
	 * @param array $args Arguments.
	 */
	public static function setup_loop( $args = array() ): void {
		$default_args = array(
			'loop'         => 0,
			'columns'      => self::items_per_row(),
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
		if ( isset( $GLOBALS[ self::$loop_name ] ) ) {
			$default_args = array_merge( $default_args, $GLOBALS[ self::$loop_name ] );
		}

		$GLOBALS[ self::$loop_name ] = wp_parse_args( $args, $default_args );
	}

	/**
	 * See what is going to display in the loop.
	 *
	 * @return string Either catalogs, subcategories, or both, based on current page.
	 */
	public static function get_loop_display_mode(): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		// Only return catalogs when filtering things.
		if ( self::get_loop_prop( 'is_search' ) || self::get_loop_prop( 'is_filtered' ) ) {
			return 'catalogs';
		}

		$parent_id    = 0;
		$display_type = '';

		if ( true === is_catalogs_page() ) {
			// TODO: display type option.
			$display_type = get_option( '_acn_shop_page_display', '' );
		} elseif ( is_catalog_category() ) {
			$parent_id    = get_queried_object_id();
			$display_type = get_term_meta( $parent_id, 'display_type', true );
			// TODO: category_archive_display.
			$display_type = '' === $display_type ? get_option( '_acn_category_archive_display', '' ) : $display_type;
		}

		if ( ( false === is_catalogs_page() || 'subcategories' !== $display_type ) && 1 < self::get_loop_prop( 'current_page' ) ) {
			return 'catalogs';
		}

		// Ensure valid value.
		if ( '' === $display_type || ! in_array( $display_type, array( 'catalogs', 'subcategories', 'both' ), true ) ) {
			$display_type = 'catalogs';
		}

		// If we're showing categories, ensure we actually have something to show.
		if ( in_array( $display_type, array( 'subcategories', 'both' ), true ) ) {
			$subcategories = self::get_subcategories( $parent_id );

			if ( empty( $subcategories ) ) {
				$display_type = 'catalogs';
			}
		}

		return $display_type;
	}

	/**
	 * Resets the loop global.
	 */
	public static function reset_loop(): void {
		unset( $GLOBALS[ self::$loop_name ] );
	}

	/**
	 * Gets a property from the loop global.
	 *
	 * @param string $prop Prop to get.
	 * @param string $default Default if the prop does not exist.
	 *
	 * @return mixed
	 */
	public static function get_loop_prop( string $prop, $default = '' ) {
		self::setup_loop(); // Ensure shop loop is setup.

		return isset( $GLOBALS[ self::$loop_name ], $GLOBALS[ self::$loop_name ][ $prop ] ) ? $GLOBALS[ self::$loop_name ][ $prop ] : $default;
	}

	/**
	 * Sets a property in the loop global.
	 *
	 * @param string $prop Prop to set.
	 * @param string $value Value to set.
	 */
	public static function set_loop_prop( string $prop, $value = '' ): void {
		if ( false === isset( $GLOBALS[ self::$loop_name ] ) ) {
			self::setup_loop();
		}
		$GLOBALS[ self::$loop_name ][ $prop ] = $value;
	}

	/**
	 * Get the default columns setting - this is how many catalogs will be shown per row in loops.
	 *
	 * @return int
	 */
	public static function items_per_row(): int {
		$columns = get_option( RFD_AUCTEENO_OPTIONS_CATALOGS_COLUMNS, 4 );

		$min_columns = 4;
		$max_columns = 8;

		if ( $columns < $min_columns ) {
			$columns = $min_columns;
			update_option( RFD_AUCTEENO_OPTIONS_CATALOGS_COLUMNS, $columns );
		} elseif ( $columns > $max_columns ) {
			$columns = $max_columns;
			update_option( RFD_AUCTEENO_OPTIONS_CATALOGS_COLUMNS, $columns );
		}

		$columns = absint( $columns );

		return max( 1, $columns );
	}

	/**
	 * Output the start of a catalog loop. By default this is a UL.
	 *
	 * @param bool $echo Should echo?.
	 *
	 * @return string|void
	 */
	public static function loop_start( $echo = true ) {
		self::set_loop_prop( 'loop', '0' );

		$loop_start = View::get_template_html(
			'loop-catalog/loop-start.php',
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
	 * Output the end of a catalog loop. By default this is a UL.
	 *
	 * @param bool $echo Should echo?.
	 *
	 * @return string|void
	 */
	public static function loop_end( $echo = true ) {

		$loop_end = View::get_template_html(
			'loop-catalog/loop-end.php',
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
	 * Page Title function.
	 *
	 * @param bool $echo Should echo title.
	 *
	 * @return string|void
	 */
	public static function page_title( $echo = true ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
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
	 * Retrieves the classes for the post div as an array.
	 *
	 * @param string|array $class Class string.
	 * @param int|WP_Post|Catalog $catalog Catalog ID or catalog object.
	 *
	 * @return array
	 * @throws Exception Exception.
	 */
	public static function get_class_attr_data( $class = '', $catalog = null ): array { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		if ( is_null( $catalog ) && false === empty( $GLOBALS['catalog'] ) ) {
			// Catalog was null so pull from global.
			$catalog = $GLOBALS['catalog'];
		}

		if ( $catalog && false === is_a( $catalog, 'RFD\Aucteeno\Catalog' ) ) {
			// Make sure we have a valid catalog, or set to false.
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
			),
			apply_filters(
				'aucteeno_catalog_woo_class',
				array(
					'product',
					'type-product',
				)
			)
		);

		/**
		 * Post Class filter.
		 *
		 * @param array $classes Array of CSS classes.
		 * @param Catalog $catalog Catalog object.
		 *
		 * @since 3.6.2
		 */
		$classes = apply_filters( 'aucteeno_post_class', $classes, $catalog ); // @phpstan-ignore-line

		return array_map( 'esc_attr', array_unique( array_filter( $classes ) ) );
	}

	/**
	 * Display the classes for the catalog div.
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @param int|WP_Post|Catalog $catalog_id Catalog ID or catalog object.
	 */
	public static function class_attr( $class = '', $catalog_id = null ): void {
		try {
			echo 'class="' . esc_attr( implode( ' ', self::get_class_attr_data( $class, $catalog_id ) ) ) . '"';
		} catch ( Exception $exception ) {
			echo 'class="error"';
		}
	}

	/**
	 * Get the product thumbnail, or the placeholder if not set.
	 *
	 * @param string $size (default: 'aucteeno_thumbnail').
	 * @param int|WP_Post|Catalog $catalog Catalog ID or catalog object.
	 *
	 * @return string
	 */
	public static function get_thumbnail( $size = 'aucteeno_thumbnail', $catalog = null ): string {
		if ( is_null( $catalog ) && false === empty( $GLOBALS['catalog'] ) ) {
			// Catalog was null so pull from global.
			$catalog = $GLOBALS['catalog'];
		}

		if ( $catalog && false === is_a( $catalog, 'RFD\Aucteeno\Catalog' ) ) {
			// Make sure we have a valid catalog, or set to false.
			try {
				$catalog = acn_get_catalog( $catalog );
			} catch ( Exception $exception ) {
				return '';
			}
		}

		$image_size = apply_filters( 'single_catalog_archive_thumbnail_size', $size );

		return $catalog ? $catalog->get_image( $image_size ) : '';
	}

	/**
	 * Static init for easy access to library.
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Priority.
	 *
	 * @return Catalog_Template_Helper
	 */
	public static function init( Loader $loader, $priority = 10 ): Catalog_Template_Helper {
		if ( true === empty( self::$instance ) ) {
			$helper         = new Catalog_Template_Helper();
			self::$instance = $helper;
		}

		return self::$instance;
	}

	/**
	 * When the_post is called, put catalog data into a global.
	 *
	 * @param mixed $post Post Object.
	 */
	public function setup_post_data( $post ): void {
		unset( $GLOBALS['catalog'] );

		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if (
			true === empty( $post->post_type ) ||
			false === in_array( $post->post_type, array( 'catalog' ), true )
		) {
			return;
		}

		try {
			$GLOBALS['catalog'] = acn_get_catalog( $post );
		} catch ( Exception $exception ) {
			return;
		}
	}

	/**
	 * Insert the opening anchor tag for products in the loop.
	 */
	public function loop_link_open(): void {
		global $catalog;

		$link = apply_filters( 'aucteeno_loop_product_link', get_the_permalink(), $catalog ); // @phpstan-ignore-line

		echo '<a href="' . esc_url( $link ) . '" class="aucteeno-LoopCatalog-link aucteeno-loop-catalog__link">';
	}

	/**
	 * Insert the closing anchor tag for products in the loop.
	 */
	public function loop_link_close(): void {
		echo '</a>';
	}

	/**
	 * Get the product thumbnail for the loop.
	 */
	public function loop_thumbnail(): void {
		global $catalog;
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::get_thumbnail( 'aucteeno_thumbnail', $catalog );
	}

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	public function loop_title(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<h2 class="' . esc_attr( apply_filters( 'aucteeno_catalog_loop_title_classes', 'aucteeno-loop-catalog__title' ) ) . '">' . get_the_title() . '</h2>';
	}

	/**
	 * Get the view catalog template for the loop.
	 *
	 * @param array $args Arguments.
	 */
	public function loop_view( array $args ): void {
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

		View::render_template(
			'loop/view-catalog.php',
			$args,
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Output the product title.
	 */
	public function single_title(): void {
		View::render_template(
			'single-catalog/title.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Output the product short description (excerpt).
	 */
	public function single_excerpt(): void {
		View::render_template(
			'single-catalog/short-description.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Output the product meta.
	 */
	public function single_meta(): void {
		View::render_template(
			'single-catalog/meta.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Output the product sharing.
	 */
	public function single_sharing(): void {
		View::render_template(
			'single-catalog/share.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Output the product tabs.
	 */
	public function output_tabs(): void {
		View::render_template(
			'single-catalog/tabs/tabs.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Add default catalog tabs to catalogs pages.
	 *
	 * @param array $tabs Array of tabs.
	 *
	 * @return array
	 */
	public function get_default_tabs( $tabs = array() ): array {
		global $catalog, $post;

		// Description tab - shows catalog content.
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Sale Bill', 'rfd-aucteeno' ),
				'priority' => 10,
				'callback' => array( $this, 'tab_description' ),
			);
		}
		$tabs['location'] = array(
			'title'    => __( 'Location & Directions', 'rfd-aucteeno' ),
			'priority' => 15,
			'callback' => array( $this, 'tab_location' ),
		);

		return $tabs;
	}

	/**
	 * Output the description tab content.
	 */
	public function tab_description(): void {
		View::render_template(
			'single-catalog/tabs/description.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Output the description tab content.
	 */
	public function tab_location(): void {
		View::render_template(
			'single-catalog/tabs/location.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Related catalogs output.
	 */
	public function output_related_catalogs(): void {
		$args = array(
			'posts_per_page' => 4,
			'columns'        => 4,
			'orderby'        => 'rand',
		);

		$this->related_catalogs( apply_filters( 'aucteeno_output_related_catalog_args', $args ) );
	}

	/**
	 * Output the related catalogs.
	 *
	 * @param array $args Provided arguments.
	 */
	public function related_catalogs( $args = array() ): void {
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
		self::set_loop_prop( 'name', 'related' );
		self::set_loop_prop( 'columns', apply_filters( 'aucteeno_related_catalogs_columns', $args['columns'] ) );

		View::get_template_html(
			'single-catalog/related.php',
			$args,
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}
}
