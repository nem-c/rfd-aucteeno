<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno;

use RFD\Aucteeno\Admin\Post_States;
use RFD\Aucteeno\Admin_Columns\Catalog_Admin_Columns;
use RFD\Aucteeno\Theme_Starters\Kadence_Theme_Starter;
use RFD\Core\I18n;
use RFD\Core\Abstracts\Init as Abstract_Init;
use RFD\Aucteeno\Post_Statuses\Expired_Post_Status;
use RFD\Aucteeno\Template\Template_Loader;
use RFD\Aucteeno\Template\Template_Hooks;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Init
 */
class Init extends Abstract_Init {

	/**
	 * Meta boxes to be registered.
	 *
	 * @var array
	 */
	protected $meta_boxes = array();

	/**
	 * Define core variables.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 *
	 * @since    0.9.0
	 */
	public function __construct() {
		if ( defined( 'RFD_AUCTEENO_VERSION' ) ) {
			$this->version = RFD_AUCTEENO_VERSION;
		} else {
			$this->version = '0.9.0';
		}
		$this->plugin_name = RFD_AUCTEENO_PLUGIN;
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Dom_Woo_Customize_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since 0.9.0
	 * @access protected
	 */
	protected function set_locale(): void {
		I18n::init(
			$this->loader,
			array(
				'domain'          => 'rfd-woo-variable-table',
				'plugin_rel_path' => RFD_AUCTEENO_PLUGIN_DIR . 'languages' . DIRECTORY_SEPARATOR,
			)
		);
	}

	/**
	 * Prepare hooks for both admin and frontend.
	 */
	protected function prepare_general(): void {
		Expired_Post_Status::init( $this->loader );
		$this->add_taxonomy( 'RFD\Aucteeno\Taxonomies\Catalog_Cat_Taxonomy' );
		$this->add_taxonomy( 'RFD\Aucteeno\Taxonomies\Catalog_Tag_Taxonomy' );
		$this->add_post_type( 'RFD\Aucteeno\Post_Types\Catalog_Post_Type' );
		$this->add_post_type( 'RFD\Aucteeno\Post_Types\Listing_Post_Type' );
	}

	/**
	 * Prepare hooks for admin side only.
	 */
	protected function prepare_admin(): void {
		$this->add_meta_box( 'RFD\Aucteeno\Admin\Meta_Boxes\Catalog_Dates_Meta_Box' );
		$this->add_meta_box( 'RFD\Aucteeno\Admin\Meta_Boxes\Catalog_Online_Meta_Box' );
		$this->add_meta_box( 'RFD\Aucteeno\Admin\Meta_Boxes\Catalog_Location_Meta_Box' );

		Catalog_Admin_Columns::init( $this->loader );
		Post_States::init( $this->loader );
	}

	/**
	 * Prepare hooks for frontend side only.
	 */
	protected function prepare_frontend(): void {
		Query::init( $this->loader );
		Template_Loader::init( $this->loader );
		Template_Hooks::init( $this->loader );
	}

	/**
	 * Register Custom Post Types
	 */
	protected function register_post_types(): void {
		parent::register_post_types();
		do_action( 'aucteeno_after_register_post_type', $this );
	}

	/**
	 * Register Custom Taxonomies
	 */
	protected function register_taxonomies(): void {
		parent::register_taxonomies();
		do_action( 'aucteeno_after_register_taxonomy', $this );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run(): void {
		do_action( 'aucteeno_before_init', $this );
		parent::run();
		do_action( 'aucteeno_init', $this );
	}
}
