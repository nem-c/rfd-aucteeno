<?php
/**
 * Admin Columns Interface
 *
 * @package RFD\Core
 * @subpackage RFD\Core\Contracts
 */

namespace RFD\Core\Contracts;

use WP_Query;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Data Store Interface
 */
interface Admin_Columns_Interface {

	/**
	 * Register new columns to be displayed.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function register( array $columns ): array;

	/**
	 * Populate column for given post ID.
	 *
	 * @param string $column Columns name.
	 * @param string|int $post_id Post ID.
	 */
	public function populate( string $column, $post_id ): void;

	/**
	 * Add columns with sortable behavior.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function sortable( array $columns ): array;

	/**
	 * Alter WP query with sorting options for custom sorting columns.
	 *
	 * @param WP_Query $query Current page query.
	 */
	public function sort( WP_Query $query ): void;
}
