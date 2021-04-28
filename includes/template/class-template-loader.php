<?php
/**
 * Overwrite and override default templates when Aucteeno is needed.
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno\Template;

use RFD\Core\Loader;

/**
 * Class Template_Loader
 */
class Template_Loader {
	/**
	 * Static init for easy access to library
	 *
	 * @param Loader $loader Loader object.
	 * @param int $priority Default priority for edit_comment hook.
	 */
	final public static function init( Loader $loader, $priority = 10 ): void {
		$template_loader = new Template_Loader();

		$loader->add_filter( 'template_include', $template_loader, 'load', $priority );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the theme's.
	 *
	 * Templates are in the 'templates' folder. Aucteeno looks for theme
	 * overrides in /theme/aucteeno/ by default.
	 *
	 * For beginners, it also looks for a woocommerce.php template first. If the user adds
	 * this to the theme (containing a woocommerce() inside) this will be used for all
	 * WooCommerce templates.
	 *
	 * @param string $template Template to load.
	 *
	 * @return string
	 */
	public function load( string $template ): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh,Generic.Metrics.NestingLevel.MaxExceeded
		if ( true === is_embed() ) {
			return $template;
		}

		$default_file = $this->get_template_loader_default_file();
		if ( false === empty( $default_file ) ) {
			/**
			 * Filter hook to choose which files to find before Aucteeno does it's own logic.
			 *
			 * @var array
			 */
			$search_files = self::get_template_loader_files( $default_file );
			$template     = locate_template( $search_files );

			if ( true === empty( $template ) ) {
				if ( false !== strpos( $default_file, 'catalog_cat' ) || false !== strpos( $default_file, 'catalog_tag' ) ) {
					$cs_template = str_replace( '_', '-', $default_file );
					$template    = RFD_AUCTEENO_TEMPLATES_DIR . $cs_template;
				} else {
					$template = RFD_AUCTEENO_TEMPLATES_DIR . $default_file;
				}
			}
		}

		return $template;
	}

	/**
	 * Get the default filename for a template.
	 *
	 * @return string
	 */
	private function get_template_loader_default_file(): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		if ( is_singular( 'catalog' ) ) {
			$default_file = 'single-catalog.php';
		} elseif ( is_catalog_taxonomy() ) {
			$object = get_queried_object();

			if ( is_catalog_category() || is_catalog_tag() ) {
				$default_file = 'taxonomy-' . $object->taxonomy . '.php'; // @phpstan-ignore-line
			} else {
				$default_file = 'archive-catalog.php';
			}
		} elseif ( is_post_type_archive( 'catalog' ) || is_page( acn_get_catalogs_page_id() ) ) {
			$default_file = 'archive-catalog.php';
		} else {
			$default_file = '';
		}

		return $default_file;
	}

	/**
	 * Get an array of filenames to search for a given template.
	 *
	 * @param string $default_file The default file name.
	 *
	 * @return string[]
	 */
	private static function get_template_loader_files( string $default_file ): array { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
		$templates   = apply_filters( 'aucteeno_template_loader_files', array(), $default_file ); // @phpstan-ignore-line
		$templates[] = 'aucteeno.php';

		if ( is_page_template() ) {
			$page_template = get_page_template_slug();

			if ( $page_template ) {
				$validated_file = validate_file( $page_template );
				if ( 0 === $validated_file ) {
					$templates[] = $page_template;
				} else {
					error_log( "Aucteeno: Unable to validate template path: \"$page_template\". Error Code: $validated_file." ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				}
			}
		}

		if ( true === is_singular( 'catalog' ) ) {
			$object       = get_queried_object();
			$name_decoded = urldecode( $object->post_name ); // @phpstan-ignore-line
			if ( $name_decoded !== $object->post_name ) { // @phpstan-ignore-line
				$templates[] = "single-catalog-$name_decoded.php";
			}
			$templates[] = "single-catalog-$object->post_name.php"; // @phpstan-ignore-line
		}

		if ( true === is_singular( 'listing' ) ) {
			$object       = get_queried_object();
			$name_decoded = urldecode( $object->post_name ); // @phpstan-ignore-line
			if ( $name_decoded !== $object->post_name ) { // @phpstan-ignore-line
				$templates[] = "single-listing-$name_decoded.php";
			}
			$templates[] = "single-listing-$object->post_name.php"; // @phpstan-ignore-line
		}

		if ( true === is_catalog_taxonomy() ) {
			$object = get_queried_object();

			$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php'; // @phpstan-ignore-line
			$templates[] = RFD_AUCTEENO_TEMPLATES_DIR . 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php'; // @phpstan-ignore-line
			$templates[] = 'taxonomy-' . $object->taxonomy . '.php'; // @phpstan-ignore-line
			$templates[] = RFD_AUCTEENO_TEMPLATES_DIR . 'taxonomy-' . $object->taxonomy . '.php'; // @phpstan-ignore-line

			if ( is_tax( 'catalog_cat' ) || is_tax( 'catalog_tag' ) ) {
				$cs_taxonomy = str_replace( '_', '-', $object->taxonomy ); // @phpstan-ignore-line
				$cs_default  = str_replace( '_', '-', $default_file );
				$templates[] = 'taxonomy-' . $object->taxonomy . '-' . $object->slug . '.php'; // @phpstan-ignore-line
				$templates[] = RFD_AUCTEENO_TEMPLATES_DIR . 'taxonomy-' . $cs_taxonomy . '-' . $object->slug . '.php'; // @phpstan-ignore-line
				$templates[] = 'taxonomy-' . $object->taxonomy . '.php'; // @phpstan-ignore-line
				$templates[] = RFD_AUCTEENO_TEMPLATES_DIR . 'taxonomy-' . $cs_taxonomy . '.php';
				$templates[] = $cs_default;
			}
		}

		$templates[] = $default_file;
		if ( true === isset( $cs_default ) ) {
			$templates[] = RFD_AUCTEENO_TEMPLATES_DIR . $cs_default;
		}
		$templates[] = RFD_AUCTEENO_TEMPLATES_DIR . $default_file;

		return array_unique( $templates );
	}
}
