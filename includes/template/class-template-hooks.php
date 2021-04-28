<?php
/**
 * Adds default template hooks.
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno\Template;

use RFD\Aucteeno\Template\Template_Catalog;
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
		$template_hooks   = new Template_Hooks();
		$template_catalog = new Template_Catalog();

		$loader->add_action( 'aucteeno_before_main_content', $template_hooks, 'content_wrapper_start', $priority );
		$loader->add_action( 'aucteeno_before_main_content', $template_hooks, 'breadcrumb', $priority + 10 );
		$loader->add_action( 'aucteeno_after_main_content', $template_hooks, 'content_wrapper_end', $priority );
		$loader->add_action( 'aucteeno_sidebar', $template_hooks, 'get_sidebar', $priority );
		$loader->add_action( 'the_post', $template_catalog, 'setup_post_data', $priority );

		$loader->add_filter( 'aucteeno_show_page_title', $template_hooks, 'show_page_title', $priority );

		$template_hooks->init_catalogs_loop( $loader, $priority );
		$template_hooks->init_single_catalog( $loader, $priority );
	}

	/**
	 * Init Catalogs Loop hooks.
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Priority.
	 */
	public function init_catalogs_loop( Loader $loader, $priority = 10 ): void {
		$template_catalog = new Template_Catalog();

		$loader->add_action( 'aucteeno_before_catalogs_loop', $template_catalog, 'setup_loop', $priority );
		$loader->add_action( 'aucteeno_before_catalogs_loop_item', $template_catalog, 'loop_link_open', $priority );
		$loader->add_action( 'aucteeno_after_catalog_loop_item', $template_catalog, 'loop_link_close', $priority );
		$loader->add_action( 'aucteeno_before_catalogs_loop_item_title', $template_catalog, 'loop_thumbnail', $priority );
		$loader->add_action( 'aucteeno_catalogs_loop_item_title', $template_catalog, 'loop_title', $priority );
		$loader->add_action( 'aucteeno_after_catalogs_loop', $template_catalog, 'reset_loop', $priority + 1000 );
	}

	/**
	 * Init Catalogs Single hooks.
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Priority.
	 */
	public function init_single_catalog( Loader $loader, $priority = 10 ): void {
		$template_catalog = new Template_Catalog();

		$loader->add_filter( 'aucteeno_catalog_tabs', $template_catalog, 'get_default_tabs', $priority );

		$loader->add_action( 'aucteeno_after_single_catalog_summary', $template_catalog, 'output_tabs', $priority + 20 );
		$loader->add_action( 'aucteeno_after_single_catalog_summary', $template_catalog, 'output_related_catalogs', $priority + 40 );

		$loader->add_action( 'aucteeno_single_catalog_summary', $template_catalog, 'single_title', $priority );
		$loader->add_action( 'aucteeno_single_catalog_summary', $template_catalog, 'single_excerpt', $priority + 10 );
		$loader->add_action( 'aucteeno_single_catalog_summary', $template_catalog, 'single_meta', $priority + 10 );
		$loader->add_action( 'aucteeno_single_catalog_summary', $template_catalog, 'single_sharing', $priority + 10 );
	}

	/**
	 * Content wrapper start.
	 */
	public function content_wrapper_start(): void {
		if ( true === is_woocommerce_activated() ) {
			do_action( 'woocommerce_before_main_content' );
		} else {
			View::render_template(
				'global/wrapper-start.php',
				array(),
				'',
				RFD_AUCTEENO_TEMPLATES_DIR
			);
		}
	}

	/**
	 * Content wrapper end.
	 */
	public function content_wrapper_end(): void {
		if ( true === is_woocommerce_activated() ) {
			do_action( 'woocommerce_after_main_content' );
		} else {
			View::render_template(
				'global/wrapper-end.php',
				array(),
				'',
				RFD_AUCTEENO_TEMPLATES_DIR
			);
		}
	}

	/**
	 * Breadcrumbs renderer.
	 */
	public function breadcrumb(): void {
		if ( true === is_woocommerce_activated() ) {

		} else {
			echo '';
		}
	}

	/**
	 * Get sidebar for page.
	 */
	public function get_sidebar(): void {
		if ( true === is_woocommerce_activated() ) {
			do_action( 'woocommerce_sidebar' );
		} else {
			echo '';
		}
	}

	/**
	 * Extend show_page_title to use woocommerce if needed.
	 *
	 * @param bool $show Current show status.
	 *
	 * @return bool
	 */
	public function show_page_title( bool $show ): bool {
		if ( true === is_woocommerce_activated() ) {
			return apply_filters( 'woocommerce_show_page_title', $show );
		}

		return $show;
	}
}
