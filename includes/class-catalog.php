<?php
/**
 * Aucteeno Catalog class.
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno;

use Exception;
use RFD\Core\DateTime;
use RFD\Core\Abstracts\Data;
use RFD\Aucteeno\Data_Stores\Data_Store;

defined( 'ABSPATH' ) || exit;

/**
 * Class Catalog
 */
class Catalog extends Data {
	/**
	 * Post type.
	 *
	 * @var string
	 */
	protected $post_type = 'catalog';

	/**
	 * Cache group.
	 *
	 * @var string
	 */
	protected $cache_group = 'catalogs';

	/**
	 * Stores catalog data.
	 *
	 * @var array
	 */
	protected $data = array(
		'name'                    => '',
		'slug'                    => '',
		'description'             => '',
		'short_description'       => '',
		'parent_id'               => 0,
		'menu_order'              => 0,
		'post_password'           => '',
		'date_created'            => null,
		'date_modified'           => null,
		'status'                  => false,
		'featured'                => false,
		'datetime_promoted'       => '',
		'datetime_start_gmt'      => '',
		'datetime_start'          => '',
		'datetime_start_timezone' => '',
		'datetime_end_gmt'        => '',
		'datetime_end'            => '',
		'datetime_end_timezone'   => '',
		'image_id'                => '',
		'gallery_image_ids'       => array(),
		'category_ids'            => array(),
		'tag_ids'                 => array(),
	);

	/**
	 * Get the catalog if ID is passed, otherwise the catalog is new and empty.
	 * This class should NOT be instantiated, but the acn_get_catalogs() function
	 * should be used. It is possible, but the wc_get_product() is preferred.
	 *
	 * @param int|Catalog|object $catalog Catalog to init.
	 *
	 * @throws Exception Exception.
	 */
	public function __construct( $catalog = 0 ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		parent::__construct();

		if ( is_numeric( $catalog ) && $catalog > 0 ) {
			$this->set_id( $catalog );
		} elseif ( $catalog instanceof self ) {
			$this->set_id( absint( $catalog->get_id() ) );
		} elseif ( false === empty( $product->ID ) ) {
			$this->set_id( absint( $product->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->data_store = Data_Store::load( 'catalog' );
		if ( $this->get_id() > 0 ) {
			$this->data_store->read( $this );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Non-CRUD Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the product's title. For products this is the product name.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return apply_filters( 'aucteeno_product_title', $this->get_name(), $this );
	}

	/**
	 * Product permalink.
	 *
	 * @return string
	 */
	public function get_permalink(): string {
		return get_permalink( $this->get_id() );
	}

	/**
	 * Returns the children IDs if applicable. Overridden by child classes.
	 *
	 * @return array of IDs
	 */
	public function get_children(): array {
		return array();
	}

	/**
	 * Get product name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_name( $context = 'view' ): string {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get product slug.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_slug( $context = 'view' ): string {
		return $this->get_prop( 'slug', $context );
	}

	/**
	 * Get product created date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|null object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ): DateTime {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Get product modified date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|null object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ): DateTime {
		return $this->get_prop( 'date_modified', $context );
	}

	/**
	 * Get product status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_status( $context = 'view' ): string {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * If the product is featured.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return boolean
	 */
	public function get_featured( $context = 'view' ): bool {
		return $this->get_prop( 'featured', $context );
	}

	/**
	 * Get product description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_description( $context = 'view' ): string {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Get product short description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_short_description( $context = 'view' ): string {
		return $this->get_prop( 'short_description', $context );
	}

	/**
	 * Get post password.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return int
	 */
	public function get_post_password( $context = 'view' ): int {
		return $this->get_prop( 'post_password', $context );
	}

	/**
	 * Get category ids.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array
	 */
	public function get_category_ids( $context = 'view' ): array {
		return $this->get_prop( 'category_ids', $context );
	}

	/**
	 * Get tag ids.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array
	 */
	public function get_tag_ids( $context = 'view' ): array {
		return $this->get_prop( 'tag_ids', $context );
	}

	/**
	 * Returns the gallery attachment ids.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return array
	 */
	public function get_gallery_image_ids( $context = 'view' ): array {
		return $this->get_prop( 'gallery_image_ids', $context );
	}

	/**
	 * Get main image ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_image_id( $context = 'view' ): string {
		return $this->get_prop( 'image_id', $context );
	}

	/**
	 * Get parent ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return int
	 */
	public function get_parent_id( $context = 'view' ): int {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Get Start Date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|string|null
	 */
	public function get_datetime_start( $context = 'view' ) {
		return $this->get_prop( 'datetime_start', $context );
	}

	/**
	 * Get Start Date GMT.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|string|null
	 */
	public function get_datetime_start_gmt( $context = 'view' ) {
		return $this->get_prop( 'datetime_start_gmt', $context );
	}

	/**
	 * Get Date Start Timezone.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_datetime_start_timezone( $context = 'view' ): ?string {
		return $this->get_prop( 'datetime_start_timezone', $context );
	}

	/**
	 * Get End Date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|string|null
	 */
	public function get_datetime_end( $context = 'view' ) {
		return $this->get_prop( 'datetime_end', $context );
	}

	/**
	 * Get End Date GMT.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|string|null
	 */
	public function get_datetime_end_gmt( $context = 'view' ) {
		return $this->get_prop( 'datetime_end_gmt', $context );
	}

	/**
	 * Get Date End Timezone.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_datetime_end_timezone( $context = 'view' ): ?string {
		return $this->get_prop( 'datetime_end_timezone', $context );
	}

	/**
	 * Get Date End Promoted.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|string|null
	 */
	public function get_datetime_promoted( $context = 'view' ) {
		return $this->get_prop( 'datetime_promoted', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting catalog data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set product name.
	 *
	 * @param string $name Product name.
	 */
	public function set_name( string $name ) {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Set product slug.
	 *
	 * @param string $slug Product slug.
	 */
	public function set_slug( string $slug ) {
		$this->set_prop( 'slug', $slug );
	}

	/**
	 * Set product created date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_created( $date = null ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set product modified date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_modified( $date = null ) {
		$this->set_date_prop( 'date_modified', $date );
	}

	/**
	 * Set product status.
	 *
	 * @param string $status Product status.
	 */
	public function set_status( string $status ) {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Set if the catalog is featured.
	 *
	 * @param bool|string $featured Whether the product is featured or not.
	 */
	public function set_featured( $featured ) {
		$this->set_prop( 'featured', rfd_string_to_bool( $featured ) );
	}

	/**
	 * Set Start Date.
	 *
	 * @param string $datetime_start Start date time string.
	 */
	public function set_datetime_start( string $datetime_start ) {
		$this->set_date_prop( 'datetime_start', $datetime_start );
	}

	/**
	 * Set Start Date GMT.
	 *
	 * @param string $datetime_start_gmt Start date time GMT string.
	 */
	public function set_datetime_start_gmt( string $datetime_start_gmt ) {
		$this->set_date_prop( 'datetime_start_gmt', $datetime_start_gmt );
	}

	/**
	 * Set Start Date Timezone.
	 *
	 * @param string $datetime_start_timezone Start date timezone string.
	 */
	public function set_datetime_start_timezone( string $datetime_start_timezone ) {
		$this->set_prop( 'datetime_start_timezone', $datetime_start_timezone );
	}

	/**
	 * Set End Date.
	 *
	 * @param string $datetime_end End date time string.
	 */
	public function set_datetime_end( string $datetime_end ) {
		$this->set_date_prop( 'datetime_end', $datetime_end );
	}

	/**
	 * Set End Date GMT.
	 *
	 * @param string $datetime_end_gmt End date time GMT string.
	 */
	public function set_datetime_end_gmt( string $datetime_end_gmt ) {
		$this->set_date_prop( 'datetime_end_gmt', $datetime_end_gmt );
	}

	/**
	 * Set End Date Timezone.
	 *
	 * @param string $datetime_end_timezone End date timezone string.
	 */
	public function set_datetime_end_timezone( string $datetime_end_timezone ) {
		$this->set_prop( 'datetime_end_timezone', $datetime_end_timezone );
	}

	/**
	 * Set Promoted Date.
	 *
	 * @param string $datetime_promoted Promoted date.
	 */
	public function set_datetime_promoted( string $datetime_promoted ) {
		$this->set_date_prop( 'datetime_promoted', $datetime_promoted );
	}
}
