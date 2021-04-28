<?php
/**
 * Location tab
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $post, $catalog;

$heading_location   = apply_filters( 'aucteeno_catalog_location_heading', __( 'Location', 'rfd-aucteeno' ) );
$heading_directions = apply_filters( 'aucteeno_catalog_directions_heading', __( 'Directions', 'rfd-aucteeno' ) );

$location_string = $catalog->get_location_string();
$directions_text = $catalog->get_directions();
?>

<?php if ( false === empty( $heading_location ) && false === empty( $location_string ) ) : ?>
    <h3><?php echo esc_html( $heading_location ); ?></h3>

	<?php echo $catalog->get_location_string(); ?>
<?php endif; ?>

<?php if ( false === empty( $heading_directions ) && false === empty( $directions_text ) ) : ?>
    <h3><?php echo esc_html( $heading_directions ); ?></h3>

	<?php echo $catalog->get_directions(); ?>
<?php endif; ?>

