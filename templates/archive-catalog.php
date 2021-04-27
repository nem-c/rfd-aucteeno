<?php
/**
 * The Template for displaying catalog archives, including the main catalogs page which is a post type archive.
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

get_header( 'aucteeno' );

/**
 * Hook: aucteeno_before_main_content.
 *
 * @hooked Template_Hooks::output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked aucteeno_breadcrumb - 20
 * @hooked RFD\Aucteeno\Structured_Data::generate_website_data() - 30
 */
do_action( 'aucteeno_before_main_content' );

?>
    <header class="aucteeno-products-header">
		<?php if ( apply_filters( 'aucteeno_show_page_title', true ) ) : ?>
            <h1 class="aucteeno-catalogs-header__title page-title"><?php aucteeno_page_title(); ?></h1>
		<?php endif; ?>

		<?php
		/**
		 * Hook: aucteeno_archive_description.
		 *
		 * @hooked aucteeno_taxonomy_archive_description - 10
		 * @hooked aucteeno_catalog_archive_description - 10
		 */
		do_action( 'aucteeno_archive_description' );
		?>
    </header>
<?php
if ( aucteeno_catalog_loop() ) {

	/**
	 * Hook: aucteeno_before_shop_loop.
	 *
	 * @hooked aucteeno_output_all_notices - 10
	 * @hooked aucteeno_result_count - 20
	 * @hooked aucteeno_catalog_ordering - 30
	 */
	do_action( 'aucteeno_before_shop_loop' );

	aucteeno_catalog_loop_start();

	if ( aucteeno_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: aucteeno_catalogs_loop.
			 */
			do_action( 'aucteeno_catalogs_loop' );

			aucteeno_get_template_part( 'content', 'catalog' );
		}
	}

	aucteeno_catalog_loop_end();

	/**
	 * Hook: aucteeno_after_shop_loop.
	 *
	 * @hooked aucteeno_pagination - 10
	 */
	do_action( 'aucteeno_after_shop_loop' );
} else {
	/**
	 * Hook: aucteeno_no_products_found.
	 *
	 * @hooked acn_no_products_found - 10
	 */
	do_action( 'aucteeno_no_products_found' );
}

/**
 * Hook: aucteeno_after_main_content.
 *
 * @hooked aucteeno_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'aucteeno_after_main_content' );

/**
 * Hook: aucteeno_sidebar.
 *
 * @hooked aucteeno_get_sidebar - 10
 */
do_action( 'aucteeno_sidebar' );

get_footer( 'aucteeno' );
