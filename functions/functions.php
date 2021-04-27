<?php
/**
 * Additional plugin functions.
 *
 * Include external functions or helpers
 * If needed it might use namespace for functions too, to avoid collision with other plugins that
 * might use same function name
 *
 * @package RFD\Aucteeno
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Include other function files.
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'constants.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'datetime.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'catalog-functions.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'conditional-functions.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core-functions.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'options-functions.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'template-functions.php';
