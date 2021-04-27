<?php
/**
 * Aucteeno admin dashboard.
 *
 * @codingStandardsIgnoreFile
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>

<div class="wrap">
    <h1><?php _e( 'Aucteeno Dashboard', 'rfd-aucteeno' ); ?></h1>
    <div class="welcome-panel" id="welcome-panel">
        <div class="welcome-panel-content">
            <h2>Welcome to Aucteeno</h2>
            <p class="about-description">
                Here are some useful links to get started with your auctions, catalogs and listings.
            </p>

            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <h3>Get Started with Catalogs</h3>
                    <a href="post-new.php?post_type=catalog"
                       class="button button-primary button-hero load-customize hide-if-no-customize">
                        Post new Catalog
                    </a>
                </div>
                <div class="welcome-panel-column">
                    <h3>Get Started with Listings</h3>
                    <a href="post-new.php?post_type=listing"
                       class="button button-primary button-hero load-customize hide-if-no-customize">
                        Post new Listing
                    </a>
                </div>
                <div class="welcome-panel-column">
                    <h3>More Actions</h3>
                    <ul>
                        <li>
                            <a href="#" class="welcome-icon welcome-view-site">View your catalogs</a>
                        </li>
                        <li>
                            <a href="#" class="welcome-icon welcome-menus">Change Settings</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="welcome-panel-upgrade" class="aligncenter" style="text-align: center; padding: 1rem">
        <em>Aucteeno Version <?php echo RFD_AUCTEENO_VERSION; ?></em>
    </div>
</div>