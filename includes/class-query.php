<?php
/**
 * Contains the query functions for Aucteeno which alter the front-end post queries and loops
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno;

use WP_Query;
use RFD\Core\Loader;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Query
 */
class Query {
	/**
	 * Query vars to add to wp.
	 *
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * Reference to the main product query on the page.
	 *
	 * @var WP_Query
	 */
	protected $catalogs_query;

	/**
	 * Stores chosen attributes.
	 *
	 * @var array
	 */
	protected $chosen_attributes;

	/**
	 * Static init for easy access to library.
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Priority.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$query = new Query();

		if ( false === is_admin() ) {
			$loader->add_filter( 'query_vars', $query, 'add_query_vars', 0 );
			$loader->add_action( 'parse_request', $query, 'parse_request', 0 );
			$loader->add_action( 'pre_get_posts', $query, 'pre_get_posts', $priority );
		}
	}

	/**
	 * Set catalogs query.
	 *
	 * @param WP_Query $query WP Query.
	 */
	public function catalogs_query( WP_Query $query ): void {
		$this->catalogs_query = $query;
	}

	/**
	 * Get query vars.
	 *
	 * @return array
	 */
	public function get_query_vars(): array {
		return apply_filters( 'aucteeno_get_query_vars', $this->query_vars );
	}

	/**
	 * Add query vars.
	 *
	 * @param array $vars Query vars.
	 *
	 * @return array
	 */
	public function add_query_vars( array $vars ): array {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}

		return $vars;
	}

	/**
	 * Parse the request and look for query vars - endpoints may not be supported.
	 */
	public function parse_request(): void {
		global $wp;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// Map query vars to their keys, or get them if endpoints are not supported.
		foreach ( $this->get_query_vars() as $key => $var ) {
			if ( isset( $_GET[ $var ] ) ) {
				$wp->query_vars[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $var ] ) );
			} elseif ( isset( $wp->query_vars[ $var ] ) ) {
				$wp->query_vars[ $key ] = $wp->query_vars[ $var ];
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Hook into pre_get_posts to do the main product query.
	 *
	 * @param WP_Query $query Query instance.
	 */
	public function pre_get_posts( WP_Query $query ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		if ( false === $query->is_main_query() ) {
			return;
		}

		// Fixes for queries on static homepages.
		if ( true === $this->is_showing_page_on_front( $query ) ) {
			// Fix for endpoints on the homepage.
			if ( false === $this->page_on_front_is( $query->get( 'page_id' ) ) ) {
				$_query = wp_parse_args( $query->query );
				if ( false === empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->get_query_vars() ) ) ) {
					$query->is_page     = true;
					$query->is_home     = false;
					$query->is_singular = true;
					$query->set( 'page_id', (int) get_option( 'page_on_front' ) );
					add_filter( 'redirect_canonical', '__return_false' );
				}
			}

			// When orderby is set, WordPress shows posts on the front-page. Get around that here.
			if ( $this->page_on_front_is( acn_get_catalogs_page_id() ) ) {
				$_query     = wp_parse_args( $query->query );
				$query_keys = array_diff(
					array_keys( $_query ),
					array(
						'preview',
						'page',
						'paged',
						'cpage',
						'orderby',
					)
				);
				if ( true === empty( $_query ) || true === empty( $query_keys ) ) {
					$query->set( 'page_id', (int) get_option( 'page_on_front' ) );
					$query->is_page = true;
					$query->is_home = false;

					$query->set( 'post_type', 'product' );
				}
			} elseif ( false === empty( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query->set( 'page_id', (int) get_option( 'page_on_front' ) );
				$query->is_page     = true;
				$query->is_home     = false;
				$query->is_singular = true;
			}
		}

		// Fix catalog feeds.
		if ( $query->is_feed() && $query->is_post_type_archive( 'catalog' ) ) {
			$query->is_comment_feed = false;
		}

		$is_page_on_front = ( 'page' === get_option( 'show_on_front' ) );
		$is_catalogs_page = ( acn_get_catalogs_page_id() === $query->queried_object_id );

		// Special check for shops with the PRODUCT POST TYPE ARCHIVE on front.
		if ( $query->is_page() && true === $is_page_on_front && true === $is_catalogs_page ) {
			// This is a front-page catalogs.
			$query->set( 'post_type', 'catalog' );
			$query->set( 'page_id', '' );
			$query->set( 'pagename', '' );
			$query->set( 'page', '' );

			if ( isset( $query->query['paged'] ) ) {
				$query->set( 'paged', $query->query['paged'] );
			}

			// Get the actual WP page to avoid errors and let us use is_front_page().
			// This is hacky but works. Awaiting https://core.trac.wordpress.org/ticket/21096.
			global $wp_post_types;

			$catalogs_page = get_post( acn_get_catalogs_page_id() );
			if ( false === empty( $catalogs_page ) ) {
				$wp_post_types['catalog']->ID         = $catalogs_page->ID;
				$wp_post_types['catalog']->post_title = $catalogs_page->post_title;
				$wp_post_types['catalog']->post_name  = $catalogs_page->post_name;
				$wp_post_types['catalog']->post_type  = $catalogs_page->post_type;
				$wp_post_types['catalog']->ancestors  = get_ancestors( $catalogs_page->ID, $catalogs_page->post_type );
			}

			// Fix conditional Functions like is_front_page.
			$query->is_singular          = false;
			$query->is_post_type_archive = true;
			$query->is_archive           = true;
			$query->is_page              = true;

			// Remove post type archive name from front page title tag.
			add_filter( 'post_type_archive_title', '__return_empty_string', 5 );

			// Fix WP SEO.
			if ( class_exists( 'WPSEO_Meta' ) ) {
				add_filter( 'wpseo_metadesc', array( $this, 'wpseo_metadesc' ) );
				add_filter( 'wpseo_metakey', array( $this, 'wpseo_metakey' ) );
			}
		} elseif ( false === $query->is_post_type_archive( 'catalog' ) && ! $query->is_tax( get_object_taxonomies( 'catalog' ) ) ) { // @phpstan-ignore-line
			// Only apply to product categories, the product post archive, the shop page, product tags, and product attribute taxonomies.
			return;
		}

		$this->catalogs_query( $query );
	}

	/**
	 * Are we currently on the front page?
	 *
	 * @param WP_Query $query Query instance.
	 *
	 * @return bool
	 */
	private function is_showing_page_on_front( WP_Query $query ): bool {
		return ( $query->is_home() && false === $query->is_posts_page ) && 'page' === get_option( 'show_on_front' );
	}

	/**
	 * Is the front page a page we define?
	 *
	 * @param int $page_id Page ID.
	 *
	 * @return bool
	 */
	private function page_on_front_is( int $page_id ): bool {
		return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
	}

	/**
	 * WP SEO meta description.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 */
	public function wpseo_metadesc(): string {
		return WPSEO_Meta::get_value( 'metadesc', acn_get_catalogs_page_id() ); // @phpstan-ignore-line
	}

	/**
	 * WP SEO meta key.
	 *
	 * Hooked into wpseo_ hook already, so no need for function_exist.
	 *
	 * @return string
	 */
	public function wpseo_metakey(): string {
		return WPSEO_Meta::get_value( 'metakey', acn_get_catalogs_page_id() ); // @phpstan-ignore-line
	}
}
