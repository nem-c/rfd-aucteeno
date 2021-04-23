<?php
/**
 * Catalog Date Meta Box.
 *
 * @var string $nonce_field Nonce hidden field html.
 * @var bool $has_online_bidding Online bidding flag.
 * @var string $online_bidding_url Online bidding URL.
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Core\Input;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>

<div class="components-base-control components-toggle-control">
    <div class="components-base-control__field rfd_aucteeno_catalog_is_online__field">
        <label class="components-base-control__label" for="rfd_aucteeno_catalog_is_online">
			<?php _e( 'Has online bidding:', 'rfd-aucteeno' ); ?>
            <span class="components-form-toggle__input">
                <input type="checkbox" value="1" name="is_online"
                       <?php echo ( true === $has_online_bidding ) ? 'checked="checked"' : ''; ?>
                       class="components-form-toggle__input" id="rfd_aucteeno_catalog_is_online">
            </span>
        </label>
    </div>
</div>
<div class="components-base-control">
    <div class="components-base-control__field rfd_aucteeno_catalog_online_url__field">
        <label class="components-base-control__label" for="rfd_aucteeno_catalog_online_url">
			<?php _e( 'Online bidding URL', 'rfd-aucteeno' ); ?>
        </label><br/>
        <input type="url" value="<?php echo $online_bidding_url; ?>" name="online_url"
               class="components-text-control__input" id="rfd_aucteeno_catalog_online_url">
    </div>
</div>
<?php echo $nonce_field; ?>