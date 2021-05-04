<?php
/**
 * Enqueue config array.
 *
 * @package    RFD\Aucteeno
 */

return array(
	'css' => array(
		array(
			'handle' => 'aucteeno-frontend',
			'src'    => RFD_AUCTEENO_ASSETS_URL . 'css/main.css',
			'ver'    => RFD_AUCTEENO_VERSION,
		),
	),
	'js'  => array(),
);
