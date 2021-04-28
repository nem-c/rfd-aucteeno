<?php
/**
 * The template for displaying product content in the single-catalog.php template
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Aucteeno\Template\Template_Catalog;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $catalog;

/**
 * Hook: aucteeno_before_single_product.
 *
 * @hooked aucteeno_output_all_notices - 10
 */
do_action( 'aucteeno_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form();

	return;
}
?>
<div id="catalog-<?php the_ID(); ?>" <?php Template_Catalog::class_attr( '', $catalog ); ?>>

	<?php
	/**
	 * Hook: aucteeno_before_single_catalog_summary.
	 *
	 * @hooked Template_Catalog::sale_flash - 10
	 * @hooked Template_Catalog::catalog_images - 20
	 */
	do_action( 'aucteeno_before_single_catalog_summary' );
	?>

    <div class="summary entry-summary">
		<?php
		/**
		 * Hook: aucteeno_single_catalog_summary.
		 *
		 * @hooked Template_Catalog::single_title - 10
		 * @hooked Template_Catalog::single_excerpt - 20
		 * @hooked Template_Catalog::single_meta - 40
		 * @hooked Template_Catalog::single_sharing - 50
		 */
		do_action( 'aucteeno_single_catalog_summary' );
		?>
    </div>

	<?php
	/**
	 * Hook: aucteeno_after_single_catalog_summary.
	 *
	 * @hooked Template_Catalog::output_tabs - 30
	 * @hooked Template_Catalog::output_related_catalogs - 60
	 */
	do_action( 'aucteeno_after_single_catalog_summary' );
	?>
</div>

<?php do_action( 'aucteeno_after_single_product' ); ?>
