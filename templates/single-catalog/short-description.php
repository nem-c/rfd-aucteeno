<?php
/**
 * Single catalog short description
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $post;

$short_description = apply_filters( 'aucteeno_short_description', $post->post_excerpt );
if ( true === empty( $short_description ) ) {
	return;
}

?>
<div class="aucteeno-catalog-details__short-description">
	<?php echo $short_description; ?>
</div>
