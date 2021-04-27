<?php
/**
 * Aucteeno Options Functions
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Functions
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Get catalogs pages ID.
 *
 * @return int
 */
function acn_get_catalogs_page_id(): int {
	return absint( get_option( RFD_AUCTEENO_OPTIONS_CATALOGS_PAGE_ID ) );
}
