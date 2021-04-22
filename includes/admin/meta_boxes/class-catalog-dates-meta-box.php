<?php
/**
 * Listing Date Meta Box
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno\Admin\Meta_Boxes;

use DateTimeZone;
use RFD\Core\DateTime;
use Exception;
use WP_Post;
use RFD\Core\Abstracts\Admin\Meta_Boxes\Post_Meta_Box;
use RFD\Core\View;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Listing_Dates_Meta_Box
 */
class Catalog_Dates_Meta_Box extends Post_Meta_Box {
	/**
	 * Post meta box ID
	 *
	 * @var string
	 */
	protected $id = 'listing-date-meta-box';

	/**
	 * Post meta box post-type screen availability
	 *
	 * @var string
	 */
	protected $screen = 'catalog';

	/**
	 * Post meta box context availability
	 *
	 * @var string
	 */
	protected $context = 'side';

	/**
	 * Post meta box priority
	 * Accepts 'high', 'core', 'default', or 'low'. Default 'default'.
	 *
	 * @var string
	 */
	protected $priority = 'default';

	/**
	 * Nonce name to be used when running actions.
	 *
	 * @var string
	 */
	protected $nonce_name = 'catalog_dates_nonce';

	/**
	 * Nonce save action name
	 *
	 * @var string
	 */
	protected $nonce_action = 'save';

	/**
	 * Comment meta box title
	 *
	 * @var string
	 */
	protected $title = 'Catalog On-Sale Dates';

	/**
	 * Comment meta box lang domain.
	 *
	 * @var string
	 */
	protected $lang_domain = 'rfd-aucteeno';

	/**
	 * Render meta box.
	 *
	 * @param WP_Post $post Post Object.
	 *
	 * @throws Exception Exception.
	 */
	public function render( WP_Post $post ): void {
		$nonce_field = $this->nonce_field();

		try {
			$catalog = acn_get_catalog( $post->ID );
		} catch ( Exception $exception ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			_doing_it_wrong( __FUNCTION__, $exception->getMessage(), RFD_AUCTEENO_VERSION );
			exit( 1 );
		}

		if ( true === empty( $catalog ) ) {
			$datetime_start          = '';
			$datetime_start_timezone = '';
			$datetime_end            = '';
			$datetime_end_timezone   = '';
			$datetime_promoted       = '';
		} else {
			// @phpstan-ignore-next-line
			$datetime_start          = rfd_string_to_datetime( $catalog->get_datetime_start() )->format( 'Y-m-d\TH:i:s' );
			$datetime_start_timezone = $catalog->get_datetime_start_timezone();
			// @phpstan-ignore-next-line
			$datetime_end          = rfd_string_to_datetime( $catalog->get_datetime_end() )->format( 'Y-m-d\TH:i:s' );
			$datetime_end_timezone = $catalog->get_datetime_end_timezone();
			// @phpstan-ignore-next-line
			$datetime_promoted = rfd_string_to_datetime( $catalog->get_datetime_promoted() )->format( 'Y-m-d\TH:i:s' );
		}

		View::render_template(
			'admin/meta-boxes/catalog-dates-meta-box.php',
			compact(
				'nonce_field',
				'datetime_promoted',
				'datetime_start',
				'datetime_start_timezone',
				'datetime_end',
				'datetime_end_timezone'
			),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Save meta data.
	 *
	 * @param int $post_id Post ID.
	 * @param mixed $post Post Object.
	 *
	 * @return bool
	 * @throws Exception Exception.
	 */
	public function save( int $post_id, $post ): bool { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh,Generic.Metrics.CyclomaticComplexity.MaxExceeded
		try {
			$catalog = acn_get_catalog( $post_id );
		} catch ( Exception $exception ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			_doing_it_wrong( __FUNCTION__, $exception->getMessage(), RFD_AUCTEENO_VERSION );
			exit( 1 );
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$date_promoted = sanitize_text_field( wp_unslash( $_POST['date_promoted'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$date_start = sanitize_text_field( wp_unslash( $_POST['date_start'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$date_start_timezone = sanitize_text_field( wp_unslash( $_POST['date_start_timezone'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$date_end = sanitize_text_field( wp_unslash( $_POST['date_end'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$date_end_timezone = sanitize_text_field( wp_unslash( $_POST['date_end_timezone'] ?? '' ) );

		$datetime_promoted = wp_date_immutable( $date_promoted )->format( 'Y-m-d H:i:s' );
		$datetime_start    = wp_date_immutable( $date_start )->format( 'Y-m-d H:i:s' );
		$datetime_end      = wp_date_immutable( $date_end )->format( 'Y-m-d H:i:s' );

		// if timezone is empty use default.
		if ( true === empty( $date_start_timezone ) ) {
			$datetime_start_timezone = wp_default_timezone();
		} else {
			$datetime_start_timezone = new DateTimeZone( $date_start_timezone );
		}
		if ( true === empty( $date_end_timezone ) ) {
			$datetime_end_timezone = wp_default_timezone();
		} else {
			$datetime_end_timezone = new DateTimeZone( $date_end_timezone );
		}

		$zulu_timezone = new DateTimeZone( 'UTC' );

		try {
			$datetime_start_gmt = new DateTime( $datetime_start, $datetime_start_timezone );
			$datetime_end_gmt   = new DateTime( $datetime_end, $datetime_end_timezone );
		} catch ( Exception $exception ) {
			wp_die( esc_html( $exception->getMessage() ) );
		}

		$datetime_start_gmt->setTimezone( $zulu_timezone );
		$datetime_start_gmt = $datetime_start_gmt->format( 'Y-m-d H:i:s' );

		$datetime_end_gmt->setTimezone( $zulu_timezone );
		$datetime_end_gmt = $datetime_end_gmt->format( 'Y-m-d H:i:s' );

		if ( false === empty( $catalog ) ) {
			$catalog->set_datetime_start( $datetime_start );
			$catalog->set_datetime_start_timezone( $datetime_start_timezone->getName() );
			$catalog->set_datetime_start_gmt( $datetime_start_gmt );
			$catalog->set_datetime_end( $datetime_end );
			$catalog->set_datetime_end_timezone( $datetime_end_timezone->getName() );
			$catalog->set_datetime_end_gmt( $datetime_end_gmt );
			$catalog->set_datetime_promoted( $datetime_promoted );

			$catalog->save();
		}

		return true;
	}
}
