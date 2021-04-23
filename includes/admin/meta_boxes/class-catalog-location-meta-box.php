<?php
/**
 * Catalog Location Meta Box
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno\Admin\Meta_Boxes;

use Exception;
use WP_Post;
use RFD\Core\Abstracts\Admin\Meta_Boxes\Post_Meta_Box;
use RFD\Core\View;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Catalog_Location_Meta_Box
 */
class Catalog_Location_Meta_Box extends Post_Meta_Box {
	/**
	 * Post meta box ID
	 *
	 * @var string
	 */
	protected $id = 'catalog-location-meta-box';

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
	protected $context = 'normal';

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
	protected $nonce_name = 'catalog_location_nonce';

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
	protected $title = 'Location Details (for in person auctions)';

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
	public function render( WP_Post $post ): void { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		$nonce_field = $this->nonce_field();

		try {
			$catalog = acn_get_catalog( $post->ID );
		} catch ( Exception $exception ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			_doing_it_wrong( __FUNCTION__, $exception->getMessage(), RFD_AUCTEENO_VERSION );
			exit( 1 );
		}

		if ( true === empty( $catalog ) ) {
			$location_address      = '';
			$location_address_2    = '';
			$location_city         = '';
			$location_postal_code  = '';
			$location_state        = '';
			$location_country_iso2 = '';
			$location_latitude     = '';
			$location_longitude    = '';
		} else {
			$location_address      = $catalog->get_location_address( 'edit' );
			$location_address_2    = $catalog->get_location_address_2( 'edit' );
			$location_city         = $catalog->get_location_city( 'edit' );
			$location_postal_code  = $catalog->get_location_postal_code( 'edit' );
			$location_state        = $catalog->get_location_state( 'edit' );
			$location_country_iso2 = $catalog->get_location_country_iso2( 'edit' );
			$location_latitude     = $catalog->get_location_latitude( 'edit' );
			$location_longitude    = $catalog->get_location_longitude( 'edit' );
		}

		View::render_template(
			'admin/meta-boxes/catalog-location-meta-box.php',
			compact(
				'nonce_field',
				'location_address',
				'location_address_2',
				'location_city',
				'location_postal_code',
				'location_state',
				'location_country_iso2',
				'location_latitude',
				'location_longitude'
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
		$location_address = sanitize_text_field( wp_unslash( $_POST['location_address'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_address_2 = sanitize_text_field( wp_unslash( $_POST['location_address_2'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_city = sanitize_text_field( wp_unslash( $_POST['location_city'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_postal_code = sanitize_text_field( wp_unslash( $_POST['location_postal_code'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_state = sanitize_text_field( wp_unslash( $_POST['location_state'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_country_iso2 = sanitize_text_field( wp_unslash( $_POST['location_country_iso2'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_latitude = sanitize_text_field( wp_unslash( $_POST['location_latitude'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$location_longitude = sanitize_text_field( wp_unslash( $_POST['location_longitude'] ?? '' ) );

		if ( false === empty( $catalog ) ) {
			$catalog->set_location_address( $location_address );
			$catalog->set_location_address_2( $location_address_2 );
			$catalog->set_location_city( $location_city );
			$catalog->set_location_postal_code( $location_postal_code );
			$catalog->set_location_state( $location_state );
			$catalog->set_location_country_iso2( $location_country_iso2 );
			$catalog->set_location_latitude( floatval( $location_latitude ) );
			$catalog->set_location_longitude( floatval( $location_longitude ) );

			$catalog->save();
		}

		return true;
	}
}
