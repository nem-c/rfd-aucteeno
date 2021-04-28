<?php
/**
 * Adds default template hooks.
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno;

use RFD\Core\Loader;
use RFD\Core\View;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Template_Loader
 */
class Template_Hooks {
	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Default priority for edit_comment hook.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$template_hooks = new Template_Hooks();

		$loader->add_action( 'aucteeno_before_main_content', $template_hooks, 'output_content_wrapper', $priority );
		$loader->add_action( 'aucteeno_after_main_content', $template_hooks, 'output_content_wrapper_end', $priority );
		$loader->add_action( 'aucteeno_before_catalogs_loop', null, 'aucteeno_setup_loop', $priority );
		$loader->add_action( 'the_post', null, 'aucteeno_setup_catalog_data', $priority );

		$loader->add_action( 'aucteeno_before_catalogs_loop', null, 'aucteeno_setup_loop', $priority );
		$loader->add_action( 'aucteeno_after_catalogs_loop', null, 'aucteeno_reset_loop', $priority + 1000 );

		// template hooks.
		$loader->add_action( 'aucteeno_before_shop_loop_item', null, 'aucteeno_template_loop_catalog_link_open', $priority );
		$loader->add_action( 'aucteeno_after_shop_loop_item', null, 'aucteeno_template_loop_catalog_link_close', $priority );
		$loader->add_action( 'aucteeno_before_shop_loop_item_title', null, 'aucteeno_template_loop_catalog_thumbnail', $priority );
		$loader->add_action( 'aucteeno_shop_loop_item_title', null, 'aucteeno_template_loop_catalog_title', $priority );

		$loader->add_action( 'aucteeno_after_single_catalog_summary', null, 'aucteeno_output_catalog_data_tabs', $priority + 20 );
		$loader->add_action( 'aucteeno_after_single_catalog_summary', null, 'aucteeno_output_related_catalogs', $priority + 40 );
		$loader->add_action( 'aucteeno_single_catalog_summary', null, 'aucteeno_template_single_title', $priority );
		$loader->add_action( 'aucteeno_single_catalog_summary', null, 'aucteeno_template_single_excerpt', $priority + 10 );
		$loader->add_action( 'aucteeno_single_catalog_summary', null, 'aucteeno_template_single_meta', $priority + 10 );
		$loader->add_action( 'aucteeno_single_catalog_summary', null, 'aucteeno_template_single_sharing', $priority + 10 );
	}

	/**
	 * Content wrapper start.
	 */
	public function output_content_wrapper(): void {
		View::render_template(
			'global/wrapper-start.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Content wrapper end.
	 */
	public function output_content_wrapper_end(): void {
		View::render_template(
			'global/wrapper-end.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}
}