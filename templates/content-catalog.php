<?php
/**
 * The template for displaying catalog content within loops
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $catalog;

?>
<li <?php aucteeno_catalog_class( '', $catalog ); ?>>
	<?php
	/**
	 * Hook: aucteeno_before_shop_loop_item.
	 *
	 * @hooked aucteeno_template_loop_catalog_link_open - 10
	 */
	do_action( 'aucteeno_before_shop_loop_item' );

	/**
	 * Hook: aucteeno_before_shop_loop_item_title.
	 *
	 * @hooked aucteeno_template_loop_catalog_thumbnail - 10
	 */
	do_action( 'aucteeno_before_shop_loop_item_title' );

	/**
	 * Hook: aucteeno_shop_loop_item_title.
	 *
	 * @hooked aucteeno_template_loop_catalog_title - 10
	 */
	do_action( 'aucteeno_shop_loop_item_title' );
	do_action( 'aucteeno_after_shop_loop_item_title' );

	/**
	 * Hook: aucteeno_after_shop_loop_item.
	 *
	 * @hooked aucteeno_template_loop_catalog_link_close - 10
	 * @hooked aucteeno_template_loop_view_catalog - 20
	 */
	do_action( 'aucteeno_after_shop_loop_item' );
	?>
</li>
