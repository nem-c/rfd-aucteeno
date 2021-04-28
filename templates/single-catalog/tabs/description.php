<?php
/**
 * Description tab
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $post;

$heading = apply_filters( 'aucteeno_catalog_description_heading', __( 'Sale Bill', 'rfd-aucteeno' ) );

?>

<?php if ( $heading ) : ?>
    <h2><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<?php the_content(); ?>
