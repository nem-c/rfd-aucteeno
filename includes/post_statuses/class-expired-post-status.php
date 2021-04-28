<?php
/**
 * Expired post status.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Post_Status
 */

namespace RFD\Aucteeno\Post_Statuses;

use RFD\Core\Abstracts\Post_Status;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Expired_Post_Status
 */
class Expired_Post_Status extends Post_Status {

	/**
	 * Name of Post Status
	 *
	 * @var string
	 */
	protected $name = 'expired';

	/**
	 * Expired_Post_Status constructor.
	 */
	public function __construct() {
		$this->label = _x( 'Expired', 'auction' );
		/* translators: */
		$this->label_count = _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>' );
	}
}
