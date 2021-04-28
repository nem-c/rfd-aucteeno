<?php
/**
 * Related Products
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

$related_catalogs = array();

if ( $related_catalogs ) : ?>
    <section class="related catalogs">
		<?php
		$heading = apply_filters( 'aucteeno_catalog_related_catalogs_heading', __( 'Related catalogs', 'rfd-aucteeno' ) );

		if ( $heading ) :
			?>
            <h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php aucteeno_catalog_loop_start(); ?>

		<?php foreach ( $related_catalogs as $related_catalog ) : ?>

			<?php
			$post_object = get_post( $related_catalog->get_id() );

			setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

			aucteeno_get_template_part( 'content', 'catalog' );
			?>

		<?php endforeach; ?>

		<?php aucteeno_catalog_loop_end(); ?>

    </section>
<?php
endif;

wp_reset_postdata();
