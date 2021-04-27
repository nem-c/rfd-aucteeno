<?php
/**
 * Content wrappers
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

$template = aucteeno_get_theme_slug_for_templates();

switch ( $template ) {
	case 'twentyten':
	case 'twentytwelve':
	case 'twentythirteen':
	case 'twentyfifteen':
		echo '</div></div>';
		break;
	case 'twentyeleven':
		echo '</div>';
		get_sidebar( 'catalogs' );
		echo '</div>';
		break;
	case 'twentyfourteen':
		echo '</div></div></div>';
		get_sidebar( 'content' );
		break;
	case 'twentysixteen':
	default:
		echo '</main></div>';
		break;
}
