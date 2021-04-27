<?php
/**
 * Additional Catalog admin Columns.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Admin
 */

namespace RFD\Aucteeno\Admin_Columns;

use RFD\Core\Abstracts\Admin_Columns;
use RFD\Core\Contracts\Admin_Columns_Interface;
use WP_Query;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Catalog_Admin_Columns
 */
class Catalog_Admin_Columns extends Admin_Columns implements Admin_Columns_Interface {

	/**
	 * Apply to post type.
	 *
	 * @var string
	 */
	protected $post_type = 'catalog';

	/**
	 * Register new columns for Catalog admin listing.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function register( array $columns ): array {
		unset( $columns['date'] );
		unset( $columns['author'] );

		$columns['date_promoted'] = __( 'Promoted date', 'rfd-aucteeno' );
		$columns['date_start']    = __( 'Start date', 'rfd-aucteeno' );
		$columns['date_end']      = __( 'End date', 'rfd-aucteeno' );
		$columns['new_listing']   = __( 'Add new listing', 'rfd-aucteeno' );

		return $columns;
	}

	/**
	 * Assign value for post for column.
	 *
	 * @param string $column Column name.
	 * @param int|string $post_id Post ID.
	 */
	public function populate( string $column, $post_id ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		$post_id = intval( $post_id );
		switch ( $column ) {
			case 'date_promoted':
				echo esc_html( get_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED, true ) );
				break;
			case 'date_start':
				echo esc_html( get_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_START, true ) );
				break;
			case 'date_end':
				echo esc_html( get_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_END, true ) );
				break;
			case 'new_listing':
				echo '<a href="#">+ New Listing</a>';
				break;
		}
	}

	/**
	 * New columns that support sort feature.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function sortable( array $columns ): array {
		$columns['date_start'] = 'date_start';
		$columns['date_end']   = 'date_end';

		return $columns;
	}

	/**
	 * Apply sorting for custom columns.
	 *
	 * @param WP_Query $query WP_Query instance.
	 */
	public function sort( WP_Query $query ): void {
		if ( false === is_admin() || false === $query->is_main_query() ) {
			return;
		}

		if ( 'date_start' === $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', RFD_AUCTEENO_CATALOG_META_DATETIME_START );
			$query->set( 'meta_type', 'DATE' );
		}

		if ( 'date_end' === $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', RFD_AUCTEENO_CATALOG_META_DATETIME_END );
			$query->set( 'meta_type', 'DATE' );
		}
	}
}
