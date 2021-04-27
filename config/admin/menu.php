<?php
/**
 * Admin menu config array.
 *
 * @package    RFD\Aucteeno
 */

return array(
	'aucteeno' => array(
		'page_title' => __( 'Aucteeno', 'rfd-aucteeno' ),
		'menu_title' => __( 'Aucteeno', 'rfd-aucteeno' ),
		'slug'       => 'aucteeno-dashboard',
		'capability' => 'manage_options',
		'callback'   => array( 'RFD\Aucteeno\Admin\Admin_Pages', 'dashboard' ),
		'icon'       => RFD_AUCTEENO_ASSETS_URL . 'images/aucteeno-letter-logo-20x20.png',
		'position'   => 50,
		'submenus'   => array(
			'aucteeno-dashboard' => array(
				'page_title' => __( 'Dashboard', 'rfd-aucteeno' ),
				'menu_title' => __( 'Dashboard', 'rfd-aucteeno' ),
				'slug'       => 'aucteeno-dashboard',
				'capability' => 'manage_options',
				'callback'   => array( 'RFD\Aucteeno\Admin\Admin_Pages', 'dashboard' ),
				'position'   => 10,
			),
			'aucteeno-catalogs'  => array(
				'page_title' => __( 'Catalogs', 'rfd-aucteeno' ),
				'menu_title' => __( 'Catalogs', 'rfd-aucteeno' ),
				'slug'       => 'edit.php?post_type=catalog',
				'capability' => 'publish_posts',
				'callback'   => '',
				'position'   => 11,
			),
			'aucteeno-listings'  => array(
				'page_title' => __( 'Listings', 'rfd-aucteeno' ),
				'menu_title' => __( 'Listings', 'rfd-aucteeno' ),
				'slug'       => 'edit.php?post_type=listing',
				'capability' => 'manage_options',
				'callback'   => '',
				'position'   => 12,
			),
			'aucteeno-settings'  => array(
				'page_title' => __( 'Settings', 'rfd-aucteeno' ),
				'menu_title' => __( 'Settings', 'rfd-aucteeno' ),
				'slug'       => 'aucteeno-settings',
				'capability' => 'manage_options',
				'callback'   => array( 'RFD\Aucteeno\Admin\Admin_Pages', 'settings' ),
				'position'   => 13,
			),
		),
	),
);
