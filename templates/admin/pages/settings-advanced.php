<?php
/**
 * Aucteeno admin dashboard - Advanced Tab.
 *
 * @var string $tab Tab name.
 * @var array $pages Listing of pages.
 * @var int $catalogs_page_id Selected Catalogs Page ID.
 *
 * @codingStandardsIgnoreFile
 */

use RFD\Core\View;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

?>

<div class="wrap">
    <h1><?php _e( 'Aucteeno Advanced Settings', 'rfd-aucteeno' ); ?></h1>
    <hr class="wp-header-end"/>
	<?php View::render_template(
		'admin/pages/parts/settings-tabs.php',
		compact( 'tab' ),
		'',
		RFD_AUCTEENO_TEMPLATES_DIR
	); ?>
    <form method="post" action="admin.php?page=aucteeno-settings&tab=<?php echo $tab; ?>">
		<?php wp_nonce_field( 'save', 'settings-' . $tab, true ); ?>
        <table class="form-table" role="presentation">
            <tbody>
            <tr>
                <th scope="row">
                    <label for="rfd_aucteeno_catalogs_page_id">
						<?php _e( 'Catalogs Page', 'rfd-aucteeno' ); ?>
                    </label>
                </th>
                <td>
                    <select name="catalogs_page_id" id="rfd_aucteeno_catalogs_page_id">
						<?php foreach ( $pages as $page ): ?>
                            <option value="<?php echo $page->ID; ?>" <?php echo ( intval( $page->ID ) === $catalogs_page_id ) ? 'selected="selected"' : ''; ?>>
								<?php echo $page->post_title; ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary"
                   value="<?php _e( 'Save Changes', 'rfd-aucteeno' ); ?>">
        </p>
    </form>
</div>