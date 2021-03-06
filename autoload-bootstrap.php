<?php
/**
 * Autoloader Bootstrap to improve performance when needed.
 *
 * @package RFD\Aucteeno
 */

/**
 * List of files for plugin.
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'constants.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-init.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-post-type.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-data.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-data-store.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-object-query.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-post-status.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'class-taxonomy.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'class-post-meta-box.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'abstracts' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-admin-columns.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-enqueue.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-i18n.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-loader.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-logger.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-menu.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-settings.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-input.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-view.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-data-store-wp.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'class-datetime.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'contracts' . DIRECTORY_SEPARATOR . 'interface-admin-columns.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'contracts' . DIRECTORY_SEPARATOR . 'interface-object-data-store.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'exceptions' . DIRECTORY_SEPARATOR . 'class-data-exception.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-init.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'admin_columns' . DIRECTORY_SEPARATOR . 'class-catalog-admin-columns.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'class-catalog-dates-meta-box.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'class-catalog-location-meta-box.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'class-catalog-online-meta-box.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-post-states.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-admin-pages.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'post_statuses' . DIRECTORY_SEPARATOR . 'class-expired-post-status.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'post_types' . DIRECTORY_SEPARATOR . 'class-catalog-post-type.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'post_types' . DIRECTORY_SEPARATOR . 'class-listing-post-type.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'data_stores' . DIRECTORY_SEPARATOR . 'class-data-store.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'data_stores' . DIRECTORY_SEPARATOR . 'class-catalog-data-store-cpt.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'queries' . DIRECTORY_SEPARATOR . 'class-catalog-query.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'taxonomies' . DIRECTORY_SEPARATOR . 'class-catalog-cat-taxonomy.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'taxonomies' . DIRECTORY_SEPARATOR . 'class-catalog-tag-taxonomy.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'class-template-loader.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'class-template-hooks.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'class-template-catalog.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'theme_starters' . DIRECTORY_SEPARATOR . 'class-kadence-theme-start.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-catalog.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-countries.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-query.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'functions.php';
