<?php
/**
 * Countries
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Countries.
 */
class Countries {

	/**
	 * Store list of countries.
	 *
	 * @var array $countries Country list.
	 */
	protected static $countries = array();

	/**
	 * Get all countries.
	 *
	 * @return array
	 */
	public static function get_countries(): array { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded
		if ( true === empty( self::$countries ) ) {
			$countries = apply_filters( 'aucteeno_countries', include RFD_AUCTEENO_PLUGIN_DIR . 'i18n/countries.php' );
			if ( apply_filters( 'aucteeno_sort_countries', true ) ) {
				$raw_data = $countries;

				array_walk(
					$countries,
					function ( &$value ) {
						$value = remove_accents( html_entity_decode( $value ) );
					}
				);
				uasort( $countries, 'strcmp' );
				foreach ( $countries as $key => $val ) {
					$countries[ $key ] = $raw_data[ $key ];
				}
			}
			self::$countries = $countries;
		}

		return self::$countries;
	}

	/**
	 * Get dropdown list of countries.
	 *
	 * @param string $selected_country Selected country if any.
	 * @param string $selected_state Selected state.
	 * @param false $escape Escape option value.
	 */
	public static function country_dropdown_options( $selected_country = '', $selected_state = '*', $escape = false ): void { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded,Generic.Metrics.CyclomaticComplexity.TooHigh
		if ( true === empty( self::$countries ) ) {
			self::get_countries();
		}
		if ( false === empty( self::$countries ) ) {
			foreach ( self::$countries as $key => $value ) {
				/**
				 * TODO: states
				 */
				echo '<option';
				if ( $selected_country === $key && '*' === $selected_state ) {
					echo ' selected="selected"';
				}
				// @phpstan-ignore-next-line
				echo ' value="' . esc_attr( $key ) . '">' . ( $escape ? esc_html( $value ) : $value ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			}
		}
	}
}
