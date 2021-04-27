<?php
/**
 * Aucteeno admin dashboard - General Tab.
 *
 * @var string $tab Tab name.
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Core\View;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>

<div class="wrap">
    <h1><?php _e( 'Aucteeno Settings', 'rfd-aucteeno' ); ?></h1>
    <hr class="wp-header-end"/>
	<?php View::render_template(
		'admin/pages/parts/settings-tabs.php',
		compact( 'tab' ),
		'',
		RFD_AUCTEENO_TEMPLATES_DIR
	); ?>
</div>