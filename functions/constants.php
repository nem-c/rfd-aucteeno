<?php
/**
 * Define additional plugin constants.
 *
 * @package RFD\Aucteeno
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

if ( false === defined( 'SAVEQUERIES' ) ) {
	define( 'SAVEQUERIES', false );
}

const RFD_AUCTEENO_PLUGIN_PREFIX = '_acn_';

const RFD_AUCTEENO_CATALOG_META_DATETIME_PROMOTED       = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_promoted';
const RFD_AUCTEENO_CATALOG_META_DATETIME_START          = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_start';
const RFD_AUCTEENO_CATALOG_META_DATETIME_START_TIMEZONE = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_start_timezone';
const RFD_AUCTEENO_CATALOG_META_DATETIME_START_GMT      = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_start_gmt';
const RFD_AUCTEENO_CATALOG_META_DATETIME_END            = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_end';
const RFD_AUCTEENO_CATALOG_META_DATETIME_END_TIMEZONE   = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_end_timezone';
const RFD_AUCTEENO_CATALOG_META_DATETIME_END_GMT        = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_datetime_end_gmt';

const RFD_AUCTEENO_CATALOG_META_IS_ONLINE  = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_is_online';
const RFD_AUCTEENO_CATALOG_META_ONLINE_URL = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_online_url';

const RFD_AUCTEENO_CATALOG_META_LOCATION_ADDRESS      = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_address';
const RFD_AUCTEENO_CATALOG_META_LOCATION_ADDRESS_2    = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_address_2';
const RFD_AUCTEENO_CATALOG_META_LOCATION_CITY         = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_city';
const RFD_AUCTEENO_CATALOG_META_LOCATION_POSTAL_CODE  = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_postal_code';
const RFD_AUCTEENO_CATALOG_META_LOCATION_STATE        = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_state';
const RFD_AUCTEENO_CATALOG_META_LOCATION_COUNTRY_ISO2 = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_country_iso2';
const RFD_AUCTEENO_CATALOG_META_LOCATION_LATITUDE     = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_latitude';
const RFD_AUCTEENO_CATALOG_META_LOCATION_LONGITUDE    = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalog_location_longitude';

const RFD_AUCTEENO_OPTIONS_CATALOGS_PAGE_ID     = RFD_AUCTEENO_PLUGIN_PREFIX . 'catalogs_page_id';
const RFD_AUCTEENO_OPTIONS_PLACEHOLDER_IMAGE_ID = RFD_AUCTEENO_PLUGIN_PREFIX . 'placeholder_image_id';
