<?php
/**
 * Loop View Catalog
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @var RFD\Aucteeno\Catalog $catalog
 * @var array $args
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

global $catalog;

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo apply_filters(
	'aucteeno_loop_view_catalog_link',
	sprintf(
		'<a href="%s" class="%s" %s>%s</a>',
		esc_url( $catalog->get_permalink() ),
		esc_attr( $args['class'] ?? 'button' ),
		isset( $args['attributes'] ) ? rfd_implode_html_attributes( $args['attributes'] ) : '',
		esc_html( $catalog->view_catalog_text() )
	),
	$catalog,
	$args
);
