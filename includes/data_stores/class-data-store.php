<?php

namespace RFD\Aucteeno\Data_Stores;

use RFD\Core\Abstracts\Data_Store as Abstract_Data_Store;

class Data_Store extends Abstract_Data_Store {
	protected $stores = array(
		'RFD\Aucteeno\Data_Stores\Catalog_Data_Store',
	);
}