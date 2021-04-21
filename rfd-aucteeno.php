<?php
/**
 * Plugin Name: Aucteeno
 * Plugin URI:  https://cimba.dev/
 * Description: Auction Items displayed in a new way. Catalog your auctions and display items.
 * Version:     0.9.0
 * Author:      Nemanja Cimbaljevic
 * Author URI:  https://codeable.io/developers/nemanja-cimbaljevic/?ref=jjTaE
 * Text Domain: rfd-aucteeno
 * Domain Path: /languages
 * License:     GPL2
 * Requires at least: 5.1
 * Tested up to: 5.7
 * Requires PHP: 7.2
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno;

defined( 'ABSPATH' ) || exit;

/**
 * Current plugin version.
 */
define( 'RFD_AUCTEENO_PLUGIN', 'rfd-aucteeno' );
define( 'RFD_AUCTEENO_VERSION', '0.9.0' );

define( 'RFD_AUCTEENO_PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'RFD_AUCTEENO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'RFD_AUCTEENO_ASSETS_URL', RFD_AUCTEENO_PLUGIN_URL . 'assets/' );
define( 'RFD_AUCTEENO_TEMPLATES_DIR', RFD_AUCTEENO_PLUGIN_DIR . 'templates' . DIRECTORY_SEPARATOR );

define( 'RFD_AUCTEENO_BLOCKS_DIR', RFD_AUCTEENO_PLUGIN_DIR . 'blocks' . DIRECTORY_SEPARATOR );
define( 'RFD_AUCTEENO_BLOCKS_URL', RFD_AUCTEENO_PLUGIN_URL . 'blocks/' );

require_once RFD_AUCTEENO_PLUGIN_DIR . 'core/autoload.php';
require_once RFD_AUCTEENO_PLUGIN_DIR . 'functions/functions.php';

/**
 * The code that runs during plugin activation.
 */
$init_plugin = new Init();

( function () use ( $init_plugin ) {
	$init_plugin->prepare()->run();
} )();
