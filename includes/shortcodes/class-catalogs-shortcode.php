<?php
/**
 * Catalogs Shortcode
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno\Shortcodes;

use Exception;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Catalogs_Shortcode
 */
class Catalogs_Shortcode {

	/**
	 * Output shortcode content.
	 *
	 * @param array $attributes Attributes.
	 */
	public static function output( array $attributes ): void {
		if ( false === apply_filters( 'aucteeno_output_catalogs_shortcode_content', true ) ) {
			return;
		}

		$attributes = shortcode_atts( array(), $attributes, 'catalogs' );
		try {
			$catalogs = acn_get_catalogs(
				array(
					'status'   => 'publish',
					'category' => '',
				)
			);
		} catch ( Exception $exception ) {
			$catalogs = array();
		}
		do_action( 'aucteeno_catalog_items' );
	}
}