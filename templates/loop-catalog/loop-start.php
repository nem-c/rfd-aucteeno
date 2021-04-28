<?php
/**
 * Catalogs Loop Start
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Templates
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Aucteeno\Template\Template_Catalog;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>
<ul class="catalogs <?php echo apply_filters('aucteeno_catalogs_woo_class', 'products'); ?> columns-<?php echo esc_attr( Template_Catalog::get_loop_prop( 'columns' ) ); ?>">
