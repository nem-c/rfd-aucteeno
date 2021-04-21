<?php
/**
 * Datetime functions.
 *
 * @package RFD\Aucteeno
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

if ( false === function_exists( 'wp_datetime_string_to_zulu' ) ) {
	/**
	 * Convert datetime string in timezone to zulu timezone (GMT)
	 *
	 * @param string $datetime_string DateTime string in any format.
	 * @param string $timezone_string DateTimeZone string.
	 * @param string $format Format of returned date.
	 *
	 * @return string
	 */
	function datetime_string_to_zulu( string $datetime_string, string $timezone_string, $format = 'Y-m-d H:i:s' ): string {

		return '';
	}
}
if ( false === function_exists( 'wp_default_timezone' ) ) {
	/**
	 * Get default WordPress timezone.
	 *
	 * If timezone is not defined Zulu/GMT/UTC is returned.
	 *
	 * @return DateTimeZone
	 */
	function wp_default_timezone(): DateTimeZone {
		$timezone_string = get_option( 'timezone_string' );
		if ( true === empty( $timezone_string ) ) {
			$timezone_string = 'UTC';
		}

		return new DateTimeZone( $timezone_string );
	}
}
if ( false === function_exists( 'wp_date_immutable' ) ) {
	/**
	 * Get default WordPress timezone.
	 *
	 * If timezone is not defined Zulu/GMT/UTC is returned.
	 *
	 * @param string $datetime_string DateTime String.
	 *
	 * @return DateTimeImmutable
	 */
	function wp_date_immutable( string $datetime_string ): DateTimeImmutable {
		try {
			return new DateTimeImmutable( $datetime_string );
		} catch ( Exception $exception ) {
			wp_die( esc_html( $exception->getMessage() ) );
		}
	}
}
