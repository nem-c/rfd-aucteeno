<?php
/**
 * Aucteeno Settings page tabs.
 *
 * @var string $tab Selected tab.
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>

<nav class="nav-tab-wrapper wp-clearfix">
    <a href="?page=aucteeno-settings&tab=general"
       class="nav-tab <?php echo ( 'general' === $tab ) ? 'nav-tab-active' : '' ?>">
		<?php _e( 'Settings', 'rfd-aucteeno' ); ?>
    </a>
    <a href="?page=aucteeno-settings&tab=integrations"
       class="nav-tab <?php echo ( 'integrations' === $tab ) ? 'nav-tab-active' : '' ?>">
		<?php _e( 'Integrations', 'rfd-aucteeno' ); ?>
    </a>
    <a href="?page=aucteeno-settings&tab=advanced"
       class="nav-tab <?php echo ( 'advanced' === $tab ) ? 'nav-tab-active' : '' ?>">
		<?php _e( 'Advanced', 'rfd-aucteeno' ); ?>
    </a>
</nav>