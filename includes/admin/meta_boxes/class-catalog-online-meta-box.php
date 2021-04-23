<?php
/**
 * Catalog Is Online Meta Box
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
 * Class Catalog_Dates_Meta_Box
 */
class Catalog_Online_Meta_Box extends Post_Meta_Box {
	/**
	 * Post meta box ID
	 *
	 * @var string
	 */
	protected $id = 'catalog-online-meta-box';

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
	protected $nonce_name = 'catalog_online_nonce';

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
	protected $title = 'Catalog Bidding Online';

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
			wp_die( esc_html( $exception->getMessage() ) );
		}

		if ( true === empty( $catalog ) ) {
			$has_online_bidding = false;
			$online_bidding_url = '';
		} else {
			$has_online_bidding = $catalog->get_is_online( 'edit' );
			$online_bidding_url = $catalog->get_online_url( 'edit' );
		}

		View::render_template(
			'admin/meta-boxes/catalog-online-meta-box.php',
			compact(
				'nonce_field',
				'has_online_bidding',
				'online_bidding_url'
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
			wp_die( esc_html( $exception->getMessage() ) );
		}

		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$has_online_bidding = sanitize_text_field( wp_unslash( $_POST['is_online'] ?? '' ) );
		//phpcs:ignore WordPress.Security.NonceVerification.Missing
		$online_bidding_url = sanitize_text_field( wp_unslash( $_POST['online_url'] ?? '' ) );

		if ( false === empty( $catalog ) ) {
			$catalog->set_is_online( $has_online_bidding );
			$catalog->set_online_url( $online_bidding_url );

			$catalog->save();
		}

		return true;
	}
}
