<?php
/**
 * Admin columns abstract
 *
 * @package RFD\Core
 */

namespace RFD\Core\Abstracts;


use RFD\Core\Loader;

/**
 * Class Admin_Columns
 */
abstract class Admin_Columns {

	/**
	 * Associate with Post Type.
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Default priority for edit_comment hook.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$instance = new static(); // @phpstan-ignore-line
		if ( false === empty( $instance->post_type ) ) {
			$loader->add_filter( 'manage_' . $instance->post_type . '_posts_columns', $instance, 'register', $priority );
			$loader->add_action( 'manage_' . $instance->post_type . '_posts_custom_column', $instance, 'populate', $priority, 2 );
			$loader->add_filter( 'manage_edit-' . $instance->post_type . '_sortable_columns', $instance, 'sortable', $priority );
			$loader->add_filter( 'pre_get_posts', $instance, 'sort', $priority );
		}
	}
}