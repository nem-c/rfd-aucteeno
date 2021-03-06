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

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

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
		'is_online'               => false,
		'online_url'              => '',
		'location_address'        => '',
		'location_address_2'      => '',
		'location_city'           => '',
		'location_postal_code'    => '',
		'location_state'          => '',
		'location_country_iso2'   => '',
		'location_latitude'       => 0.0,
		'location_longitude'      => 0.0,
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
		} elseif ( false === empty( $catalog->ID ) ) {
			$this->set_id( absint( $catalog->ID ) );
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
		return apply_filters( 'aucteeno_product_title', $this->get_name(), $this ); // @phpstan-ignore-line
	}

	/**
	 * Product permalink.
	 *
	 * @return string|false
	 */
	public function get_permalink() {
		return get_permalink( $this->get_id() );
	}

	/**
	 * Get location string combined.
	 *
	 * @return string
	 */
	public function get_location_string(): string { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
		$address      = $this->get_location_address();
		$address_2    = $this->get_location_address_2();
		$city         = $this->get_location_city();
		$postal_code  = $this->get_location_postal_code();
		$state        = $this->get_location_state();
		$country_iso2 = $this->get_location_country_iso2();

		$location_string = $address;
		if ( false === empty( $location_string ) ) {
			$location_string .= ', ';
		}
		if ( false === empty( $address_2 ) ) {
			$location_string .= $address_2 . ', ';
		}
		if ( false === empty( $city ) ) {
			$location_string .= $city . ' ';
		}
		if ( false === empty( $state ) ) {
			$location_string .= $state . ' ';
		}
		if ( false === empty( $postal_code ) ) {
			$location_string .= $postal_code . ', ';
		}
		if ( false === empty( $country_iso2 ) ) {
			$location_string .= $country_iso2;
		}

		return apply_filters( 'aucteeno_catalog_location_string', $location_string, $address, $address_2, $city, $postal_code, $state, $country_iso2 );
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
	 * Return View Catalog button text.
	 *
	 * @return string
	 */
	public function view_catalog_text(): string {
		return __( 'View', 'rfd-aucteeno' );
	}

	/**
	 * Return View Catalog button text.
	 *
	 * @return string
	 */
	public function view_catalog_description(): string {
		return __( 'View catalog', 'rfd-aucteeno' );
	}

	/**
	 * Returns the main product image.
	 *
	 * @param string $size (default: 'aucteeno_thumbnail').
	 * @param array $attr Image attributes.
	 * @param bool $placeholder True to return $placeholder if no image is found, or false to return an empty string.
	 *
	 * @return string
	 */
	public function get_image( $size = 'aucteeno_thumbnail', $attr = array(), $placeholder = true ): string { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
		$image = '';
		if ( $this->get_image_id() ) {
			$image = wp_get_attachment_image( $this->get_image_id(), $size, false, $attr );
		}

		if ( true === empty( $image ) && true === $placeholder ) {
			$image = aucteeno_catalog_placeholder_img( $size, $attr );
		}

		return apply_filters( 'aucteeno_catalog_get_image', $image, $this, $size, $attr, $placeholder, $image );
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
	 * @return DateTime object if the date is set or null if there is no date.
	 */
	public function get_date_created( $context = 'view' ): DateTime {
		$date_created = $this->get_prop( 'date_created', $context );
		if ( true === empty( $date_created ) ) {
			$date_created = new DateTime();
		}

		return $date_created;
	}

	/**
	 * Get product modified date.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return DateTime|null object if the date is set or null if there is no date.
	 */
	public function get_date_modified( $context = 'view' ): ?DateTime {
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
	 * @return int
	 */
	public function get_image_id( $context = 'view' ): int {
		return absint( $this->get_prop( 'image_id', $context ) );
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

	/**
	 * Get Is Online Flag.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return bool|null
	 */
	public function get_is_online( $context = 'view' ): ?bool {
		return $this->get_prop( 'is_online', $context );
	}

	/**
	 * Get Online URL.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_online_url( $context = 'view' ): ?string {
		return $this->get_prop( 'online_url', $context );
	}

	/**
	 * Get Location Address.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_location_address( $context = 'view' ): ?string {
		return $this->get_prop( 'location_address', $context );
	}

	/**
	 * Get Location Address 2.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_location_address_2( $context = 'view' ): ?string {
		return $this->get_prop( 'location_address_2', $context );
	}

	/**
	 * Get Location City.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_location_city( $context = 'view' ): ?string {
		return $this->get_prop( 'location_city', $context );
	}

	/**
	 * Get Location Postal Code.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_location_postal_code( $context = 'view' ): ?string {
		return $this->get_prop( 'location_postal_code', $context );
	}

	/**
	 * Get Location State.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_location_state( $context = 'view' ): ?string {
		return $this->get_prop( 'location_state', $context );
	}

	/**
	 * Get Location Country ISO2.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_location_country_iso2( $context = 'view' ): ?string {
		return $this->get_prop( 'location_country_iso2', $context );
	}

	/**
	 * Get Location Latitude.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return float|null
	 */
	public function get_location_latitude( $context = 'view' ): ?float {
		return floatval( $this->get_prop( 'location_latitude', $context ) );
	}

	/**
	 * Get Location Longitude.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return float|null
	 */
	public function get_location_longitude( $context = 'view' ): ?float {
		return floatval( $this->get_prop( 'location_longitude', $context ) );
	}

	/**
	 * Get Location Longitude.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string|null
	 */
	public function get_directions( $context = 'view' ): ?string {
		return $this->get_prop( 'directions', $context );
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
	public function set_name( string $name ): void {
		$this->set_prop( 'name', $name );
	}

	/**
	 * Set product slug.
	 *
	 * @param string $slug Product slug.
	 */
	public function set_slug( string $slug ): void {
		$this->set_prop( 'slug', $slug );
	}

	/**
	 * Set product created date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_created( $date = null ): void {
		$this->set_date_prop( 'date_created', $date );
	}

	/**
	 * Set product modified date.
	 *
	 * @param string|integer|null $date UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
	 */
	public function set_date_modified( $date = null ): void {
		$this->set_date_prop( 'date_modified', $date );
	}

	/**
	 * Set product status.
	 *
	 * @param string $status Product status.
	 */
	public function set_status( string $status ): void {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Set if the catalog is featured.
	 *
	 * @param bool|string $featured Whether the product is featured or not.
	 */
	public function set_featured( $featured ): void {
		$this->set_prop( 'featured', rfd_string_to_bool( $featured ) );
	}

	/**
	 * Set Start Date.
	 *
	 * @param string $datetime_start Start date time string.
	 */
	public function set_datetime_start( string $datetime_start ): void {
		$this->set_date_prop( 'datetime_start', $datetime_start );
	}

	/**
	 * Set Start Date GMT.
	 *
	 * @param string $datetime_start_gmt Start date time GMT string.
	 */
	public function set_datetime_start_gmt( string $datetime_start_gmt ): void {
		$this->set_date_prop( 'datetime_start_gmt', $datetime_start_gmt );
	}

	/**
	 * Set Start Date Timezone.
	 *
	 * @param string $datetime_start_timezone Start date timezone string.
	 */
	public function set_datetime_start_timezone( string $datetime_start_timezone ): void {
		$this->set_prop( 'datetime_start_timezone', $datetime_start_timezone );
	}

	/**
	 * Set End Date.
	 *
	 * @param string $datetime_end End date time string.
	 */
	public function set_datetime_end( string $datetime_end ): void {
		$this->set_date_prop( 'datetime_end', $datetime_end );
	}

	/**
	 * Set End Date GMT.
	 *
	 * @param string $datetime_end_gmt End date time GMT string.
	 */
	public function set_datetime_end_gmt( string $datetime_end_gmt ): void {
		$this->set_date_prop( 'datetime_end_gmt', $datetime_end_gmt );
	}

	/**
	 * Set End Date Timezone.
	 *
	 * @param string $datetime_end_timezone End date timezone string.
	 */
	public function set_datetime_end_timezone( string $datetime_end_timezone ): void {
		$this->set_prop( 'datetime_end_timezone', $datetime_end_timezone );
	}

	/**
	 * Set Promoted Date.
	 *
	 * @param string $datetime_promoted Promoted date.
	 */
	public function set_datetime_promoted( string $datetime_promoted ): void {
		$this->set_date_prop( 'datetime_promoted', $datetime_promoted );
	}

	/**
	 * Set Is Online Flag
	 *
	 * @param string|int|bool $is_online Flag.
	 */
	public function set_is_online( $is_online ): void {
		$this->set_prop( 'is_online', rfd_string_to_bool( $is_online ) );
	}

	/**
	 * Set Catalog Online URL.
	 *
	 * @param string $url External Catalog URL.
	 */
	public function set_online_url( string $url ): void {
		$this->set_prop( 'online_url', $url );
	}

	/**
	 * Set Catalog Location Address.
	 *
	 * @param string $address Address.
	 */
	public function set_location_address( string $address ): void {
		$this->set_prop( 'location_address', $address );
	}

	/**
	 * Set Catalog Location Address 2
	 *
	 * @param string $address_2 Address 2.
	 */
	public function set_location_address_2( string $address_2 ): void {
		$this->set_prop( 'location_address_2', $address_2 );
	}

	/**
	 * Set Catalog Location City.
	 *
	 * @param string $city City.
	 */
	public function set_location_city( string $city ): void {
		$this->set_prop( 'location_city', $city );
	}

	/**
	 * Set Location Postal Code.
	 *
	 * @param string $postal_code Postal Code.
	 */
	public function set_location_postal_code( string $postal_code ): void {
		$this->set_prop( 'location_postal_code', $postal_code );
	}

	/**
	 * Set Location State.
	 *
	 * @param string $state State.
	 */
	public function set_location_state( string $state ): void {
		$this->set_prop( 'location_state', $state );
	}

	/**
	 * Set Location Country ISO2 Code.
	 *
	 * @param string $country_iso2 ISO2 Code.
	 */
	public function set_location_country_iso2( string $country_iso2 ): void {
		$this->set_prop( 'location_country_iso2', $country_iso2 );
	}

	/**
	 * Set Location Latitude.
	 *
	 * @param float $latitude Latitude.
	 */
	public function set_location_latitude( float $latitude ): void {
		$this->set_prop( 'location_latitude', $latitude );
	}

	/**
	 * Set Location Longitude.
	 *
	 * @param float $longitude Longitude.
	 */
	public function set_location_longitude( float $longitude ): void {
		$this->set_prop( 'location_longitude', $longitude );
	}

	/**
	 * Set Directions.
	 *
	 * @param string $directions Longitude.
	 */
	public function set_directions( string $directions ): void {
		$this->set_prop( 'directions', $directions );
	}
}
