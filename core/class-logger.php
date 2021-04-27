<?php
/**
 * Simple logger class
 *
 * @link       https://rfd.rs/
 * @since      0.9.0
 *
 * @package    RFD\Core
 */

namespace RFD\Core;

/**
 * Class Logger
 *
 * @package RFD\Core
 */
class Logger {

	/**
	 * Log to file and maybe die.
	 *
	 * @param mixed $log Data to log.
	 * @param false $wp_die Run wp die after log.
	 */
	public static function log( $log, $wp_die = false ): void {
		self::write( $log );
		if ( true === $wp_die ) { // @phpstan-ignore-line.
			wp_die( esc_attr__( $log ) ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
		}
	}

	/**
	 * Log data to error log if WP_DEBUG allows it.
	 *
	 * @param mixed $log Data to log.
	 */
	public static function write( $log ): void {
		if ( true !== WP_DEBUG ) {
			return;
		}
		if ( is_array( $log ) || is_object( $log ) ) {
			error_log( print_r( $log, true ) ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log, WordPress.PHP.DevelopmentFunctions.error_log_print_r
		} else {
			error_log( $log ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}

	/**
	 * Query logger.
	 */
	public static function query_logger(): void {
		add_action(
			'shutdown',
			function () {
				global $wpdb;
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
				$log_file = fopen( WP_CONTENT_DIR . '/sql.log', 'a' ); // @phpstan-ignore-line
				foreach ( $wpdb->queries as $q ) {
					// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite, Generic.Strings.UnnecessaryStringConcat.Found
					fwrite( $log_file, $q[0] . " - ($q[1] s)" . "\n\n" ); // @phpstan-ignore-line
				}
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite, Generic.Strings.UnnecessaryStringConcat.Found
				fwrite( $log_file, "\n\n ------------------------------------------------------------------- \n\n" ); // @phpstan-ignore-line
				fclose( $log_file ); // @phpstan-ignore-line
			}
		);
	}
}
