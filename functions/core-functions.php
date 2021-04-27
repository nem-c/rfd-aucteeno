<?php
/**
 * Aucteeno Core Functions
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Functions
 */

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 */
function aucteeno_get_template( string $template_name, $args = array(), $template_path = '', $default_path = '' ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	$cache_key = sanitize_key(
		implode(
			'-',
			array(
				'template',
				$template_name,
				$template_path,
				$default_path,
				RFD_AUCTEENO_VERSION,
			)
		)
	);
	$template  = (string) wp_cache_get( $cache_key, 'woocommerce' );

	if ( ! $template ) {
		$template = aucteeno_locate_template( $template_name, $template_path, $default_path );

		// Don't cache the absolute path so that it can be shared between web servers with different paths.
		$cache_path = aucteeno_tokenize_path( $template, aucteeno_get_path_define_tokens() );

		aucteeno_set_template_cache( $cache_key, $cache_path );
	} else {
		// Make sure that the absolute path to the template is resolved.
		$template = aucteeno_untokenize_path( $template, aucteeno_get_path_define_tokens() );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'wc_get_template', $template, $template_name, $args, $template_path, $default_path ); // @phpstan-ignore-line

	if ( $filter_template !== $template ) {
		if ( ! file_exists( $filter_template ) ) {
			/* translators: %s template */
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'rfd-aucteeno' ), '<code>' . $filter_template . '</code>' ), RFD_AUCTEENO_VERSION ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped,WordPress.WP.I18n.MissingTranslatorsComment

			return;
		}
		$template = $filter_template;
	}

	$action_args = array(
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located'       => $template,
		'args'          => $args,
	);

	if ( ! empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				esc_html( __( 'action_args should not be overwritten when calling wc_get_template.', 'rfd-aucteeno' ) ),
				esc_html( RFD_AUCTEENO_VERSION )
			);
			unset( $args['action_args'] );
		}
		extract( $args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	}

	do_action( 'aucteeno_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'aucteeno_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * WC_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @param mixed $slug Template slug.
 * @param string $name Template name (default: '').
 */
function aucteeno_get_template_part( $slug, $name = '' ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded,Generic.Metrics.NestingLevel.MaxExceeded
	$cache_key = sanitize_key(
		implode(
			'-',
			array(
				'template-part',
				$slug,
				$name,
				RFD_AUCTEENO_VERSION,
			)
		)
	);
	$template  = (string) wp_cache_get( $cache_key, 'aucteeno' );

	if ( true === empty( $template ) ) {
		if ( $name ) {
			$template = locate_template(
				array(
					"$slug-$name.php",
					"aucteeno/$slug-$name.php",
				)
			);

			if ( true === empty( $template ) ) {
				$fallback = RFD_AUCTEENO_TEMPLATES_DIR . "$slug-$name.php";
				$template = file_exists( $fallback ) ? $fallback : '';
			}
		}

		if ( true === empty( $template ) ) {
			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php.
			$template = locate_template(
				array(
					"$slug.php",
					"aucteeno/$slug.php",
				)
			);
		}

		// Don't cache the absolute path so that it can be shared between web servers with different paths.
		$cache_path = aucteeno_tokenize_path( $template, aucteeno_get_path_define_tokens() );

		aucteeno_set_template_cache( $cache_key, $cache_path );
	} else {
		// Make sure that the absolute path to the template is resolved.
		$template = aucteeno_untokenize_path( $template, aucteeno_get_path_define_tokens() );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'aucteeno_get_template_part', $template, $slug, $name ); // @phpstan-ignore-line

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string
 */
function aucteeno_locate_template( string $template_name, $template_path = '', $default_path = '' ): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	if ( true === empty( $template_path ) ) {
		$template_path = 'aucteeno/';
	}

	// phpcs:ignore
	if ( true === empty ( $default_path ) ) {
		$default_path = RFD_AUCTEENO_TEMPLATES_DIR;
	}

	// Look within passed path within the theme - this is priority.
	if ( false !== strpos( $template_name, 'catalog_cat' ) || false !== strpos( $template_name, 'catalog_tag' ) ) {
		$cs_template = str_replace( '_', '-', $template_name );
		$template    = locate_template(
			array(
				trailingslashit( $template_path ) . $cs_template,
				$cs_template,
			)
		);
	}

	if ( empty( $template ) ) {
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);
	}

	// Get default template/.
	if ( true === empty( $template ) ) {
		if ( empty( $cs_template ) ) {
			$template = $default_path . $template_name;
		} else {
			$template = $default_path . $cs_template;
		}
	}

	// Return what we found.
	return apply_filters( 'aucteeno_locate_template', $template, $template_name, $template_path ); // @phpstan-ignore-line
}

/**
 * Given a path, this will convert any of the subpaths into their corresponding tokens.
 *
 * @param string $path The absolute path to tokenize.
 * @param array $path_tokens An array keyed with the token, containing paths that should be replaced.
 *
 * @return string The tokenized path.
 */
function aucteeno_tokenize_path( string $path, array $path_tokens ): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
	uasort(
		$path_tokens,
		function ( $a, $b ) {
			$a = strlen( $a );
			$b = strlen( $b );

			if ( $a > $b ) {
				return - 1;
			}

			if ( $b > $a ) {
				return 1;
			}

			return 0;
		}
	);

	foreach ( $path_tokens as $token => $token_path ) {
		if ( 0 !== strpos( $path, $token_path ) ) {
			continue;
		}

		$path = str_replace( $token_path, '{{' . $token . '}}', $path );
	}

	return $path;
}

/**
 * Given a tokenized path, this will expand the tokens to their full path.
 *
 * @param string $path The absolute path to expand.
 * @param array $path_tokens An array keyed with the token, containing paths that should be expanded.
 *
 * @return string The absolute path.
 */
function aucteeno_untokenize_path( string $path, array $path_tokens ): string {
	foreach ( $path_tokens as $token => $token_path ) {
		$path = str_replace( '{{' . $token . '}}', $token_path, $path );
	}

	return $path;
}

/**
 * Fetches an array containing all of the configurable path constants to be used in tokenization.
 *
 * @return array The key is the define and the path is the constant.
 */
function aucteeno_get_path_define_tokens(): array {
	$defines = array(
		'ABSPATH',
		'WP_CONTENT_DIR',
		'WP_PLUGIN_DIR',
		'WPMU_PLUGIN_DIR',
		'PLUGINDIR',
		'WP_THEME_DIR',
	);

	$path_tokens = array();
	foreach ( $defines as $define ) {
		if ( defined( $define ) ) {
			$path_tokens[ $define ] = constant( $define );
		}
	}

	return apply_filters( 'aucteeno_get_path_define_tokens', $path_tokens );
}

/**
 * Add a template to the template cache.
 *
 * @param string $cache_key Object cache key.
 * @param string $template Located template.
 */
function aucteeno_set_template_cache( string $cache_key, string $template ): void {
	wp_cache_set( $cache_key, $template, 'aucteeno' );

	$cached_templates = wp_cache_get( 'cached_templates', 'aucteeno' );
	if ( is_array( $cached_templates ) ) {
		$cached_templates[] = $cache_key;
	} else {
		$cached_templates = array( $cache_key );
	}

	wp_cache_set( 'cached_templates', $cached_templates, 'aucteeno' );
}
