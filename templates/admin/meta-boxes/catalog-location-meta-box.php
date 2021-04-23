<?php
/**
 * Catalog Date Meta Box.
 *
 * @var string $nonce_field Nonce hidden field html.
 * @var string $location_address Location Address.
 * @var string $location_address_2 Location Address 2.
 * @var string $location_city Location City.
 * @var string $location_postal_code Location Postal Code.
 * @var string $location_state Location State.
 * @var string $location_country_iso2 Location Country ISO2.
 * @var float $location_latitude Location Latitude.
 * @var float $location_longitude Location Longitude.
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Aucteeno\Countries;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>
<table style="width: 100%">
    <tr>
        <td style="width: 50%; vertical-align: top; padding-right: 1rem;">
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <label class="components-base-control__label" for="rfd_aucteeno_catalog_location_address">
						<?php _e( 'Location Address', 'rfd-aucteeno' ); ?>
                    </label>
                    <br/>
                    <input type="text" value="<?php echo $location_address; ?>" name="location_address"
                           class="components-text-control__input" id="rfd_aucteeno_catalog_location_address">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <label class="components-base-control__label" for="rfd_aucteeno_catalog_location_address_2">
						<?php _e( 'Location Address 2', 'rfd-aucteeno' ); ?>
                    </label>
                    <br/>
                    <input type="text" value="<?php echo $location_address_2; ?>" name="location_address_2"
                           class="components-text-control__input" id="rfd_aucteeno_catalog_location_address_2">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <label class="components-base-control__label" for="rfd_aucteeno_catalog_location_city">
						<?php _e( 'Location City', 'rfd-aucteeno' ); ?>
                    </label>
                    <br/>
                    <input type="text" value="<?php echo $location_city; ?>" name="location_city"
                           class="components-text-control__input" id="rfd_aucteeno_catalog_location_city">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <label class="components-base-control__label" for="rfd_aucteeno_catalog_location_postal_code">
						<?php _e( 'Location Postal Code', 'rfd-aucteeno' ); ?>
                    </label>
                    <br/>
                    <input type="text" value="<?php echo $location_postal_code; ?>" name="location_postal_code"
                           class="components-text-control__input" id="rfd_aucteeno_catalog_location_postal_code">
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <label class="components-base-control__label" for="rfd_aucteeno_catalog_location_state">
						<?php _e( 'Location State', 'rfd-aucteeno' ); ?>
                    </label>
                    <br/>
                    <input type="text" value="<?php echo $location_state; ?>" name="location_state"
                           class="components-text-control__input" id="rfd_aucteeno_catalog_location_state">
                    <br/>
                    <em class="components-base-control__help">
						<?php _e( '2 letter ISO2 State abbreviation if applicable.' ); ?>
                    </em>
                </div>
            </div>
            <div class="components-base-control">
                <div class="components-base-control__field">
                    <label class="components-base-control__label" for="rfd_aucteeno_catalog_location_country_iso2">
						<?php _e( 'Location Country', 'rfd-aucteeno' ); ?>
                    </label>
                    <br/>
                    <select name="location_country_iso2" class="components-base-control__field"
                            id="rfd_aucteeno_catalog_location_country_iso2">
						<?php Countries::country_dropdown_options( $location_country_iso2 ); ?>
                    </select>

                    <button id="location_to_catalog_map" class="components-button is-secondary" style="float: right">
                        <?php _e( 'Find on map', 'rfd-aucteeno' ); ?>
                    </button>
                </div>
            </div>
        </td>
        <td style="width: 50%">
            <div id="catalog_location_map" style="width: 100%; height: 300px;"></div>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%">
                        <div class="components-base-control">
                            <div class="components-base-control__field">
                                <label class="components-base-control__label"
                                       for="rfd_aucteeno_catalog_location_longitude">
									<?php _e( 'Location Longitude', 'rfd-aucteeno' ); ?>
                                </label>
                                <br/>
                                <input type="number"
                                       step="0.000001"
                                       value="<?php echo $location_longitude; ?>"
                                       name="location_longitude"
                                       class="components-text-control__input"
                                       id="rfd_aucteeno_catalog_location_longitude">
                            </div>
                        </div>
                    </td>
                    <td style="width: 50%">
                        <div class="components-base-control">
                            <div class="components-base-control__field">
                                <label class="components-base-control__label"
                                       for="rfd_aucteeno_catalog_location_latitude">
									<?php _e( 'Location Longitude', 'rfd-aucteeno' ); ?>
                                </label>
                                <br/>
                                <input type="number"
                                       step="0.000001"
                                       value="<?php echo $location_latitude; ?>"
                                       name="location_latitude"
                                       class="components-text-control__input"
                                       id="rfd_aucteeno_catalog_location_latitude">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php echo $nonce_field; ?>
