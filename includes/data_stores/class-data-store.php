<?php
/**
 * WC Data Store.
 *
 * @package RFD\Aucteeno
 */

namespace RFD\Aucteeno\Data_Stores;

use RFD\Core\Abstracts\Data_Store as Abstract_Data_Store;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Data store class.
 */
class Data_Store extends Abstract_Data_Store {
	/**
	 * Contains an array of default supported data stores.
	 *
	 * @var array
	 */
	protected $stores = array(
		'catalog' => Catalog_Data_Store_Cpt::class,
	);
}
