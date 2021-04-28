<?php
/**
 * Single Catalog tabs
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Filter tabs and allow third parties to add their own.
 *
 * Each tab is an array containing title, callback and priority.
 *
 * @see aucteeno_default_catalog_tabs()
 */
$catalog_tabs = apply_filters( 'aucteeno_catalog_tabs', array() );

if ( false === empty( $catalog_tabs ) ) : ?>

    <div class="aucteeno-tabs aucteeno-tabs-wrapper">
        <ul class="tabs aucteeno-tabs" role="tablist">
			<?php foreach ( $catalog_tabs as $key => $catalog_tab ) : ?>
                <li class="<?php echo esc_attr( $key ); ?>_tab" id="tab-title-<?php echo esc_attr( $key ); ?>"
                    role="tab" aria-controls="tab-<?php echo esc_attr( $key ); ?>">
                    <a href="#tab-<?php echo esc_attr( $key ); ?>">
						<?php echo wp_kses_post( apply_filters( 'aucteeno_catalog_' . $key . '_tab_title', $catalog_tab['title'], $key ) ); ?>
                    </a>
                </li>
			<?php endforeach; ?>
        </ul>
		<?php foreach ( $catalog_tabs as $key => $catalog_tab ) : ?>
            <div class="aucteeno-Tabs-panel aucteeno-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content aucteeno-tab"
                 id="tab-<?php echo esc_attr( $key ); ?>" role="tabpanel"
                 aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
				<?php
				if ( isset( $catalog_tab['callback'] ) ) {
					call_user_func( $catalog_tab['callback'], $key, $catalog_tab );
				}
				?>
            </div>
		<?php endforeach; ?>

		<?php do_action( 'aucteeno_catalog_after_tabs' ); ?>
    </div>

<?php endif; ?>
