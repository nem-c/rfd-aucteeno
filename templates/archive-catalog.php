<?php
/**
 * The Template for displaying catalog archives, including the main catalogs page which is a post type archive.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Aucteeno\Template\Template_Catalog;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

get_header( 'aucteeno' );

/**
 * Hook: aucteeno_before_main_content.
 *
 * @hooked Template_Hooks::content_wrapper_start - 10 (outputs opening divs for the content)
 * @hooked Template_Hooks::breadcrumb - 20
 * @hooked RFD\Aucteeno\Structured_Data::generate_website_data() - 30
 */
do_action( 'aucteeno_before_main_content' );

?>
    <header class="aucteeno-catalogs-header <?php echo apply_filters( 'aucteeno_catalogs_header_woo_class', 'woocommerce-products-header' ); ?>">
		<?php if ( true === apply_filters( 'aucteeno_show_page_title', true ) ) : ?>
            <h1 class="aucteeno-catalogs-header__title page-title <?php echo apply_filters( 'aucteeno_catalogs_header_title_woo_class', 'woocommerce-products-header__title' ); ?>">
				<?php Template_Catalog::page_title(); ?>
            </h1>
		<?php endif; ?>

		<?php
		/**
		 * Hook: aucteeno_archive_description.
		 *
		 * @hooked Template_Catalog::taxonomy_archive_description - 10
		 * @hooked Template_Catalog::archive_description - 10
		 */
		do_action( 'aucteeno_archive_description' );
		?>
    </header>
<?php
if ( Template_Catalog::loop() ) {

	/**
	 * Hook: aucteeno_before_catalogs_loop.
	 *
	 * @hooked Template_Hooks::all_notices - 10
	 * @hooked Template_Catalog::result_count - 20
	 * @hooked Template_Catalog::ordering - 30
	 */
	do_action( 'aucteeno_before_catalogs_loop' );

	Template_Catalog::loop_start();

	if ( Template_Catalog::get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: aucteeno_catalogs_loop.
			 */
			do_action( 'aucteeno_catalogs_loop' );

			aucteeno_get_template_part( 'content', 'catalog' );
		}
	}

	Template_Catalog::loop_end();

	/**
	 * Hook: aucteeno_after_catalogs_loop.
	 *
	 * @hooked aucteeno_pagination - 10
	 */
	do_action( 'aucteeno_after_catalogs_loop' );
} else {
	/**
	 * Hook: aucteeno_no_catalogs_found.
	 *
	 * @hooked Template_Catalog::nothing_found - 10
	 */
	do_action( 'aucteeno_no_catalogs_found' );
}

/**
 * Hook: aucteeno_after_main_content.
 *
 * @hooked Template_Hooks::content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'aucteeno_after_main_content' );

/**
 * Hook: aucteeno_sidebar.
 *
 * @hooked Template_Hooks::get_sidebar - 10
 */
do_action( 'aucteeno_sidebar' );

get_footer( 'aucteeno' );
