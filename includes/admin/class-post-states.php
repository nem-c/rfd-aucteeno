<?php
/**
 * Add custom post states for admin page listings.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Admin
 */

namespace RFD\Aucteeno\Admin;

use WP_Post;
use RFD\Core\Loader;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Post_States
 */
class Post_States {
	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Default priority for edit_comment hook.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$instance = new static(); // @phpstan-ignore-line

		$loader->add_filter( 'display_post_states', $instance, 'customize', $priority, 2 );
	}

	/**
	 * Add new post states for Aucteeno.
	 *
	 * @param array $states Existing states.
	 * @param WP_Post $post Post object.
	 *
	 * @return array
	 */
	public function customize( array $states, WP_Post $post ): array {

		if ( 'page' === $post->post_type && acn_get_catalogs_page_id() === $post->ID ) {
			$states['aucteeno_catalogs_page'] = _x( 'Catalogs Page', 'page label' );
		}

		return $states;
	}
}
