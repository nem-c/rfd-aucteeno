<?php
/**
 * Contains the query functions for Aucteeno which alter the front-end post queries and loops
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno\Theme_Starters;

use RFD\Aucteeno\Template\Template_Catalog;
use RFD\Aucteeno\Template\Template_Hooks;
use WP_Query;
use RFD\Core\Loader;
use Kadence\Woocommerce\Component as KadenceStarter;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Query
 */
class Kadence_Theme_Starter {

	/**
	 * Simple init function to make it easier to initiate them.
	 *
	 * @param Loader $loader Loader object.
	 * @param Template_Hooks $template_hooks Template Hooks Instance.
	 * @param Template_Catalog $template_catalog Template Catalog Instance.
	 */
	final public static function init( Loader $loader, Template_Hooks $template_hooks, Template_Catalog $template_catalog ): void {
		$starter = new Kadence_Theme_Starter();

		if ( true === is_admin() ) {
			return;
		}

		if ( 'kadence' !== get_option( 'template' ) ) {
			return;
		}

		$starter->start_theme( $loader, $template_hooks, $template_catalog );
	}

	/**
	 * Kadence Start theme and adjust for WooCommerce
	 *
	 * @param Loader $loader Loader instance.
	 * @param Template_Hooks $template_hooks Template hooks instance.
	 * @param Template_Catalog $template_catalog Template catalog instance.
	 */
	public function start_theme( Loader $loader, Template_Hooks $template_hooks, Template_Catalog $template_catalog ): void {
		$theme_dir = wp_get_theme()->get_template_directory();
		require_once $theme_dir . '/inc/components/component_interface.php';
		require_once $theme_dir . '/inc/components/woocommerce/component.php';
		$kadence_starter = new KadenceStarter(); // @phpstan-ignore-line

		$loader->remove_action( 'aucteeno_before_main_content', $template_hooks, 'content_wrapper_start', 10 );
		$loader->remove_action( 'aucteeno_before_main_content', $template_hooks, 'breadcrumb', 20 );
		$loader->remove_action( 'aucteeno_after_main_content', $template_hooks, 'content_wrapper_end', 10 );
		$loader->remove_action( 'aucteeno_sidebar', $template_hooks, 'get_sidebar', 10 );

		$loader->add_action( 'aucteeno_before_main_content', $kadence_starter, 'output_product_above_title', 5 );
		$loader->add_action( 'aucteeno_before_main_content', $kadence_starter, 'output_content_wrapper', 10 );
		$loader->add_action( 'aucteeno_after_main_content', $kadence_starter, 'output_content_wrapper_end', 10 );

		$loader->add_filter( 'aucteeno_show_page_title', null, '__return_false', 20 );
	}
}

