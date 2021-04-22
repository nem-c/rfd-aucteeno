<?php
/**
 * Catalog post type
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno\Post_Types;

use RFD\Core\Abstracts\Post_Type;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class Listing_Post_Type
 */
class Catalog_Post_Type extends Post_Type {
	/**
	 * Post type name
	 *
	 * @var string
	 */
	protected $name = 'catalog';

	/**
	 * Post type slug
	 *
	 * @var string
	 */
	protected $slug = 'catalog';

	/**
	 * Post type menu item label
	 *
	 * @var string
	 */
	protected $menu_title = 'Catalogs';

	/**
	 * Post type admin bar label
	 *
	 * @var string
	 */
	protected $admin_bar_title = 'Catalogs';

	/**
	 * Post type singular label
	 *
	 * @var string
	 */
	protected $singular_label = 'Catalog';

	/**
	 * Post type plural label.
	 *
	 * @var string
	 */
	protected $plural_label = 'Catalogs';

	/**
	 * Post type description.
	 *
	 * @var string
	 */
	protected $description = 'Catalog Post Type';

	/**
	 * Post type lang domain (for i18n - usually matches lang of plugin).
	 *
	 * @var string
	 */
	protected $lang_domain = 'rfd-aucteeno';

	/**
	 * Post type support arguments.
	 *
	 * @var array
	 */
	protected $supports = array(
		'title',
		'editor',
		'author',
		'excerpt',
	);
}
