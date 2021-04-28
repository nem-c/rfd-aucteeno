<?php
/**
 * The template for displaying catalog content within loops
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Aucteeno\Template\Template_Catalog;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $catalog;

?>
<li <?php Template_Catalog::class_attr( '', $catalog ); ?>>
	<?php
	/**
	 * Hook: aucteeno_before_catalogs_loop_item.
	 *
	 * @hooked Template_Catalog::loop_link_open - 10
	 */
	do_action( 'aucteeno_before_catalogs_loop_item' );

	/**
	 * Hook: aucteeno_before_catalogs_loop_item_title.
	 *
	 * @hooked Template_Catalog::loop_thumbnail - 10
	 */
	do_action( 'aucteeno_before_catalogs_loop_item_title' );

	/**
	 * Hook: aucteeno_catalogs_loop_item_title.
	 *
	 * @hooked Template_Catalog::loop_title - 10
	 */
	do_action( 'aucteeno_catalogs_loop_item_title' );
	do_action( 'aucteeno_after_catalogs_loop_item_title' );

	/**
	 * Hook: aucteeno_after_catalogs_loop_item.
	 *
	 * @hooked Template_Catalog::loop_link_close - 10
	 * @hooked Template_Catalog::loop_view_catalog - 20
	 */
	do_action( 'aucteeno_after_catalogs_loop_item' );
	?>
</li>
