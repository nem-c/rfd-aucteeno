<?php
/**
 * Aucteeno Template
 *
 * Functions for templating system.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Functions
 */

/**
 * Get a slug identifying the current theme.
 *
 * @return string
 */
function aucteeno_get_theme_slug_for_templates(): string {
	return apply_filters( 'aucteeno_theme_slug_for_templates', get_option( 'template' ) );
}
