<?php
/**
 * Admin Pages.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Admin
 */

namespace RFD\Aucteeno\Admin;

use RFD\Core\View;

/**
 * Class Admin_Pages
 */
class Admin_Pages {

	/**
	 * Static instance of the class.
	 *
	 * @var Admin_Pages $instance Instance.
	 */
	public static $instance;

	/**
	 * Get current static instance, and make new and return.
	 *
	 * @return Admin_Pages
	 */
	public static function instance(): Admin_Pages {
		// @phpstan-ignore-next-line
		if ( false === self::$instance instanceof Admin_Pages ) {
			self::$instance = new Admin_Pages();
		}

		return self::$instance;
	}

	/**
	 * Dashboard callback.
	 */
	public static function dashboard(): void {
		View::render_template(
			'admin/pages/dashboard.php',
			array(),
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Settings page callback.
	 */
	public static function settings(): void {
		$admin_pages = self::instance();
		$admin_pages->maybe_save();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab      = sanitize_text_field( wp_unslash( $_GET['tab'] ?? '' ) );
		$tab      = $admin_pages->valid_tab_name( $tab );
		$template = $admin_pages->get_template_for_tab( $tab );

		$tab_data = array();
		if ( true === method_exists( $admin_pages, 'get_tab_data_' . $tab ) ) {
			$tab_data = $admin_pages->{'get_tab_data_' . $tab}();
		}

		$tab_data = array_merge(
			$tab_data,
			compact(
				'tab'
			)
		);

		View::render_template(
			$template,
			$tab_data,
			'',
			RFD_AUCTEENO_TEMPLATES_DIR
		);
	}

	/**
	 * Maybe save form data.
	 */
	public function maybe_save(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$submitted = (bool) strlen( sanitize_text_field( wp_unslash( $_POST['submit'] ?? '' ) ) );
		if ( true === $submitted ) {
			// if submit post is set, we can presume form was submitted.
			$tab                = sanitize_text_field( wp_unslash( $_GET['tab'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$nonce_string       = sanitize_text_field( wp_unslash( $_POST[ 'settings-' . $tab ] ?? '' ) );
			$nonce_verification = wp_verify_nonce( $nonce_string, 'save' );
			if ( 1 <= $nonce_verification ) {
				$request_data = wp_unslash( $_REQUEST );
				$this->save( $request_data );
			}
		}
	}

	/**
	 * Save options data.
	 *
	 * @param array $request_data $_REQUEST array unslashed.
	 *
	 * @return bool
	 */
	public function save( array $request_data ): bool {
		$tab = sanitize_text_field( wp_unslash( $request_data['tab'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( method_exists( $this, 'save_' . $tab ) ) {
			return $this->{'save_' . $tab}( $request_data );
		}

		return false;
	}

	/**
	 * Save options data from Advanved tab.
	 *
	 * @param array $request_data $_REQUEST array unslashed.
	 *
	 * @return bool
	 */
	protected function save_advanced( array $request_data ): bool {

		update_option( RFD_AUCTEENO_OPTIONS_CATALOGS_PAGE_ID, intval( $request_data['catalogs_page_id'] ), false );

		return true;
	}

	/**
	 * Get data for general tab.
	 *
	 * @return array
	 */
	protected function get_tab_data_advanced(): array {
		$pages = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'orderby'        => 'title',
				'order'          => 'asc',
			)
		);

		$catalogs_page_id = acn_get_catalogs_page_id();

		return compact(
			'pages',
			'catalogs_page_id'
		);
	}

	/**
	 * Converts tab name to valid tab name.
	 *
	 * @param string $tab Tab name.
	 *
	 * @return string
	 */
	protected function valid_tab_name( string $tab ): string {
		switch ( $tab ) {
			case 'integrations':
				$tab = 'integrations';
				break;
			case 'advanced':
				$tab = 'advanced';
				break;
			default:
				$tab = 'general';
		}

		return $tab;
	}

	/**
	 * Get template for tab.
	 *
	 * @param string $tab Tab name.
	 *
	 * @return string
	 */
	protected function get_template_for_tab( string $tab ): string {
		switch ( $tab ) {
			case 'integrations':
				$template = 'admin/pages/settings-integrations.php';
				break;
			case 'advanced':
				$template = 'admin/pages/settings-advanced.php';
				break;
			default:
				$template = 'admin/pages/settings-general.php';
		}

		return $template;
	}
}
