<?php
/**
 * WC Wrapper for PHP DateTime which adds support for gmt/utc offset when a
 * timezone is absent
 *
 * @package RFD\Core
 */

namespace RFD\Core;

use DateTime as PHPDateTime;
use DateTimeZone;

defined( 'ABSPATH' ) || exit;

/**
 * Datetime class.
 */
class DateTime extends PHPDateTime {

	/**
	 * UTC Offset, if needed. Only used when a timezone is not set. When
	 * timezones are used this will equal 0.
	 *
	 * @var integer
	 */
	protected $utc_offset = 0;

	/**
	 * Output an ISO 8601 date string in local (WordPress) timezone.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->format( DATE_ATOM );
	}

	/**
	 * Set UTC offset - this is a fixed offset instead of a timezone.
	 *
	 * @param int $offset Offset.
	 */
	public function set_utc_offset( int $offset ) {
		$this->utc_offset = $offset;
	}

	/**
	 * Get UTC offset if set, or default to the DateTime object's offset.
	 */
	public function get_offset(): int {
		return $this->utc_offset ?: parent::getOffset();
	}

	/**
	 * Set timezone.
	 *
	 * @param DateTimeZone $timezone DateTimeZone instance.
	 *
	 * @return PHPDateTime
	 */
	public function set_timezone( DateTimeZone $timezone ): PHPDateTime {
		$this->utc_offset = 0;

		return parent::setTimezone( $timezone );
	}

	/**
	 * Missing in PHP 5.2 so just here so it can be supported consistently.
	 *
	 * @return int
	 */
	public function get_timestamp(): int {
		return method_exists( 'DateTime', 'getTimestamp' ) ? parent::getTimestamp() : $this->format( 'U' );
	}

	/**
	 * Get the timestamp with the WordPress timezone offset added or subtracted.
	 *
	 * @return int
	 */
	public function get_offset_timestamp(): int {
		return $this->get_timestamp() + $this->get_offset();
	}

	/**
	 * Format a date based on the offset timestamp.
	 *
	 * @param string $format Date format.
	 *
	 * @return string
	 */
	public function date( string $format ): string {
		return gmdate( $format, $this->get_offset_timestamp() );
	}

	/**
	 * Return a localised date based on offset timestamp. Wrapper for date_i18n function.
	 *
	 * @param string $format Date format.
	 *
	 * @return string
	 */
	public function date_i18n( $format = 'Y-m-d' ): string {
		return date_i18n( $format, $this->get_offset_timestamp() );
	}
}