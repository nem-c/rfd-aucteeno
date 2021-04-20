<?php
/**
 * Globals wrappers
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno;

/**
 * Class Globals
 */
class Globals {

	/**
	 * Globals stored
	 *
	 * @var array
	 */
	public static $values = array(
		'catalog_meta' => array(),
	);

	/**
	 * Init globals variable
	 */
	public static function init(): void {
		$GLOBALS[ RFD_AUCTEENO_PLUGIN_PREFIX ] = &self::$values;
	}

	/**
	 * Store catalog meta
	 *
	 * @param int $post_id Catalog Post ID.
	 * @param array $values Array of meta values.
	 */
	public static function set_catalog_meta( int $post_id, array $values ): void {
		self::$values['catalog_meta'][ $post_id ] = $values;
	}

	/**
	 * Get catalog meta.
	 *
	 * @param int $post_id Catalog Post ID.
	 * @param string $meta_key Meta key to return if any.
	 * @param false $single Return single or array.
	 *
	 * @return string|array
	 */
	public static function get_catalog_meta( int $post_id, $meta_key = '', $single = false ) {
		$catalog_meta = self::$values['catalog_meta'][ $post_id ] ?? array();

		if ( true === empty( $meta_key ) ) {
			$result = $catalog_meta;
		} else {
			// expected to be array due to get_post_meta result.
			$catalog_item = $catalog_meta[ $meta_key ] ?? array();
			$result       = $catalog_item;
			if ( true === $single ) {
				$result = current( $catalog_item );
			}
		}

		return $result;
	}
}
