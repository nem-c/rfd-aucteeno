<?php
/**
 * Catalog Category Taxonomy.
 *
 * @package RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Taxonomies
 */

namespace RFD\Aucteeno\Taxonomies;

use RFD\Core\Abstracts\Taxonomy;

/**
 * Class Catalog_Cat_Taxonomy
 */
class Catalog_Cat_Taxonomy extends Taxonomy {
	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $name = 'catalog_cat';

	/**
	 * Taxonomy belongs to post types.
	 *
	 * @var array
	 */
	protected $objects = array(
		'catalog',
	);

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $slug = 'catalog-cat';

	/**
	 * Lang domain to be used.
	 *
	 * @var string
	 */
	protected $lang_domain = 'rfd-aucteeno';

	/**
	 * Taxonomy singular label.
	 *
	 * @var string
	 */
	protected $singular_label = 'Catalog Category';

	/**
	 * Taxonomy plural label
	 *
	 * @var string
	 */
	protected $plural_label = 'Catalog Categories';

	/**
	 * Taxonomy is hierarchical.
	 *
	 * @var bool
	 */
	protected $hierarchical = true;

	/**
	 * Taxonomy show in UI.
	 *
	 * @var bool
	 */
	protected $show_ui = true;

	/**
	 * Taxonomy show admin column.
	 *
	 * @var bool
	 */
	protected $show_admin_column = true;

	/**
	 * Taxonomy add query var.
	 *
	 * @var bool
	 */
	protected $query_var = true;

	/**
	 * Taxonomy show in REST.
	 *
	 * @var bool
	 */
	protected $show_in_rest = true;
}