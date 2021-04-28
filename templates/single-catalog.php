<?php
/**
 * The Template for displaying all single catalogs
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

get_header( 'aucteeno' ); ?>

<?php
/**
 * Hook: aucteeno_before_main_content.
 *
 * @hooked aucteeno_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked aucteeno_breadcrumb - 20
 */
do_action( 'aucteeno_before_main_content' );
?>

<?php while ( have_posts() ) : ?>
	<?php the_post(); ?>

	<?php aucteeno_get_template_part( 'content', 'single-catalog' ); ?>

<?php endwhile; // end of the loop. ?>

<?php
/**
 * Hook: aucteeno_after_main_content.
 *
 * @hooked aucteeno_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'aucteeno_after_main_content' );
?>

<?php
/**
 * Hook: aucteeno_sidebar.
 *
 * @hooked aucteeno_get_sidebar - 10
 */
do_action( 'aucteeno_sidebar' );
?>

<?php
get_footer( 'aucteeno' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
