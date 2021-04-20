<?php
/**
 * Enqueue config array.
 *
 * @package    RFD\Aucteeno
 */

return array(
	'css' => array(
		array(
			'handle' => 'vendor-simple-picker',
			'src'    => RFD_AUCTEENO_ASSETS_URL . 'vendor/simple-picker/simplepicker.css',
			'ver'    => RFD_AUCTEENO_VERSION,
		),
		array(
			'handle' => 'vendor-simple-picker-map',
			'src'    => RFD_AUCTEENO_ASSETS_URL . 'vendor/simple-picker/simplepicker.css.map',
			'ver'    => RFD_AUCTEENO_VERSION,
		),
	),
	'js'  => array(
		array(
			'handle'    => 'moment-js',
			'src'       => RFD_AUCTEENO_ASSETS_URL . 'vendor/moment.min.js',
			'ver'       => RFD_AUCTEENO_VERSION,
			'in_footer' => true,
		),
		array(
			'handle'    => 'vendor-simple-picker',
			'src'       => RFD_AUCTEENO_ASSETS_URL . 'vendor/simple-picker/simplepicker.js',
			'ver'       => RFD_AUCTEENO_VERSION,
			'in_footer' => true,
		),
		array(
			'handle'    => 'rfd-aucteeno-admin',
			'src'       => RFD_AUCTEENO_ASSETS_URL . 'js/admin.js',
			'ver'       => RFD_AUCTEENO_VERSION,
			'in_footer' => true,
		),
	),
);
