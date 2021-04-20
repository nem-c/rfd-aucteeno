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
use DateTime;
use Exception;
use RFD\Aucteeno\Globals;
use WP_Post;
use RFD\Core\Abstracts\Admin\Meta_Boxes\Post_Meta_Box;
use RFD\Core\View;

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
	 */
	public function render( WP_Post $post ): void {
		$nonce_field = $this->nonce_field();

		$catalog_meta = get_post_meta( $post->ID );
		Globals::set_catalog_meta( $post->ID, $catalog_meta );

		$date_start_timezone = acn_get_catalog_promoted_date( $post );
		$date_end_timezone   = '';

		View::render_template(
			'admin/meta-boxes/catalog-dates-meta-box.php',
			compact(
				'nonce_field',
				'date_start_timezone',
				'date_end_timezone'
			),
			null,
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
	 */
	public function save( int $post_id, $post ): bool {
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

		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED, $datetime_promoted );
		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_START, $datetime_start );
		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_START_TIMEZONE, $date_start_timezone );
		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_START_GMT, $datetime_start_gmt );
		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_END, $datetime_end );
		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_END_TIMEZONE, $date_end_timezone );
		update_post_meta( $post_id, RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT, $datetime_end_gmt );

		return true;
	}
}
