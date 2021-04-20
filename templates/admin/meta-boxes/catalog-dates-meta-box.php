<?php
/**
 * Catalog Date Meta Box.
 *
 * @var string $date_promoted Promoted date.
 * @var string $date_start Start date.
 * @var string $date_start_timezone Start date timezone.
 * @var string $date_end End date.
 * @var string $date_end_timezone End date timezone.
 * @var string $nonce_field Nonce hidden field html.
 *
 * @codingStandardsIgnoreFile
 */
?>
    <div class="components-base-control__field rfd_aucteeno_date_promoted__field">
        <label class="components-base-control__label" for="rfd_aucteeno_date_promoted">
			<?php _e( 'Promoted Date:', 'rfd-aucteeno' ); ?>
        </label><br/>
        <input type="datetime-local" name="date_promoted" id="rfd_aucteeno_date_promoted"
               class="simplepicker components-text-control__input">
    </div>
    <hr/>
    <div class="components-base-control__field rfd_aucteeno_date_start__field">
        <label class="components-base-control__label" for="rfd_aucteeno_date_start">
			<?php _e( 'Start Date:', 'rfd-aucteeno' ); ?>
        </label><br/>
        <input type="datetime-local" id="rfd_aucteeno_date_start" name="date_start"
               class="simplepicker components-text-control__input">
    </div>
    <div class="components-base-control__field rfd_aucteeno_date_start_timezone__field">
        <label class="components-base-control__label" for="timezone_string">
			<?php _e( 'Start Date Timezone:', 'rfd-aucteeno' ); ?>
        </label><br/>
        <select id="timezone_string" name="timezone_string" aria-describedby="timezone-description">
			<?php echo wp_timezone_choice( $date_start_timezone ); ?>
        </select>
        <br/>
        <em>If not selected, default timezone is used.</em>
    </div>
    <hr/>
    <div class="components-base-control__field rfd_aucteeno_date_end__field">
        <label class="components-base-control__label" for="rfd_aucteeno_date_end">
			<?php _e( 'End Date:', 'rfd-aucteeno' ); ?>
        </label><br/>
        <input type="datetime-local" name="date_end" id="rfd_aucteeno_date_end"
               class="simplepicker components-text-control__input">
    </div>
    <div class="components-base-control__field rfd_aucteeno_date_end_timezone__field">
        <label class="components-base-control__label" for="rfd_aucteeno_date_end_timezone">
			<?php _e( 'End Date Timezone:', 'rfd-aucteeno' ); ?>
        </label><br/>
        <select id="rfd_aucteeno_date_end_timezone" name="date_end_timezone"
                class="components-base-control__select">
			<?php echo wp_timezone_choice( $date_end_timezone ); ?>
        </select>
        <br/>
        <em>If not selected, default timezone is used.</em>
    </div>
<?php echo $nonce_field; ?>