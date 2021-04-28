<?php
/**
 * Single catalog meta
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $catalog;
?>
<div class="product_meta">

	<?php do_action( 'aucteeno_product_meta_start' ); ?>

	<?php echo aucteeno_get_catalog_category_list( $catalog->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $catalog->get_category_ids() ), 'rfd-aucteeno' ) . ' ', '</span>' ); ?>

	<?php echo aucteeno_get_catalog_tag_list( $catalog->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $catalog->get_tag_ids() ), 'rfd-aucteeno' ) . ' ', '</span>' ); ?>

	<?php do_action( 'aucteeno_catalog_meta_end' ); ?>

</div>
