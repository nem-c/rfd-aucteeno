<?php
/**
 * Listing post type
 *
 * @link       https://cimba.blog/
 * @since      0.9.0
 *
 * @package    RFD\Aucteeno
 * @subpackage RFD\Aucteeno\Includes
 */

namespace RFD\Aucteeno\Post_Types;

use RFD\Core\Abstracts\Post_Type;

/**
 * Class Listing_Post_Type
 */
class Listing_Post_Type extends Post_Type {
	/**
	 * Post type name
	 *
	 * @var string
	 */
	protected $name = 'listing';

	/**
	 * Post type slug
	 *
	 * @var string
	 */
	protected $slug = 'listing';

	/**
	 * Post type menu item label
	 *
	 * @var string
	 */
	protected $menu_title = 'Listings';

	/**
	 * Post type admin bar label
	 *
	 * @var string
	 */
	protected $admin_bar_title = 'Listings';

	/**
	 * Post type singular label
	 *
	 * @var string
	 */
	protected $singular_label = 'Listing';

	/**
	 * Post type plural label.
	 *
	 * @var string
	 */
	protected $plural_label = 'Listing';

	/**
	 * Post type description.
	 *
	 * @var string
	 */
	protected $description = 'Listing Post Type';

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
		'thumbnail',
		'excerpt',
	);
}