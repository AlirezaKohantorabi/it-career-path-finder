<?php

use ItCareerPathFinder\Admin\Notices;
use ItCareerPathFinder\Admin\RoleActions;
use ItCareerPathFinder\Data\RoleRepository;

defined( 'ABSPATH' ) || exit;

$itcp_origin_labels = array(
	RoleRepository::ORIGIN_DEFAULT => __( 'پیش‌فرض', 'it-career-path-finder' ),
	RoleRepository::ORIGIN_EDITED  => __( 'ویرایش‌شده', 'it-career-path-finder' ),
	RoleRepository::ORIGIN_CUSTOM  => __( 'اضافه‌شده', 'it-career-path-finder' ),
);
$itcp_post_url      = admin_url( 'admin-post.php' );
?>
<div class="wrap itcp-admin">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'شغل‌های مسیر شغلی IT', 'it-career-path-finder' ); ?></h1>
	<a href="<?php echo esc_url( $view['add_url'] ); ?>" class="page-title-action"><?php esc_html_e( 'افزودن شغل', 'it-career-path-finder' ); ?></a>
	<hr class="wp-header-end">
	<?php Notices::render(); ?>

	<p class="itcp-admin-intro">
		<?php esc_html_e( 'برای نمایش ابزار، این شورت‌کد را در هر برگه یا نوشته‌ای قرار دهید:', 'it-career-path-finder' ); ?>
		<code dir="ltr">[<?php echo esc_html( $view['shortcode'] ); ?>]</code>
	</p>

	<p class="itcp-filter">
		<label for="itcp-role-filter"><?php esc_html_e( 'جست‌وجو:', 'it-career-path-finder' ); ?></label>
		<input type="search" id="itcp-role-filter" class="regular-text" data-itcp-filter="itcp-roles-table" placeholder="<?php esc_attr_e( 'نام شغل، دسته یا شناسه...', 'it-career-path-finder' ); ?>">
		<span class="itcp-count"><?php echo esc_html( sprintf( __( '%s شغل', 'it-career-path-finder' ), number_format_i18n( count( $view['rows'] ) ) ) ); ?></span>
	</p>

	<table class="widefat striped itcp-roles-table" id="itcp-roles-table">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'عنوان', 'it-career-path-finder' ); ?></th>
				<th scope="col"><?php esc_html_e( 'عنوان انگلیسی', 'it-career-path-finder' ); ?></th>
				<th scope="col"><?php esc_html_e( 'دسته', 'it-career-path-finder' ); ?></th>
				<th scope="col"><?php esc_html_e( 'شناسه', 'it-career-path-finder' ); ?></th>
				<th scope="col"><?php esc_html_e( 'وضعیت', 'it-career-path-finder' ); ?></th>
				<th scope="col"><?php esc_html_e( 'کارها', 'it-career-path-finder' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $view['rows'] as $itcp_row ) : ?>
				<tr data-itcp-row>
					<td><strong><a href="<?php echo esc_url( $itcp_row['edit_url'] ); ?>"><?php echo esc_html( $itcp_row['title_fa'] ); ?></a></strong></td>
					<td dir="ltr"><?php echo esc_html( $itcp_row['title_en'] ); ?></td>
					<td><?php echo esc_html( $itcp_row['category'] ); ?></td>
					<td><code dir="ltr"><?php echo esc_html( $itcp_row['id'] ); ?></code></td>
					<td><span class="itcp-badge itcp-badge-<?php echo esc_attr( $itcp_row['origin'] ); ?>"><?php echo esc_html( $itcp_origin_labels[ $itcp_row['origin'] ] ?? '' ); ?></span></td>
					<td class="itcp-row-actions">
						<a class="button button-small" href="<?php echo esc_url( $itcp_row['edit_url'] ); ?>"><?php esc_html_e( 'ویرایش', 'it-career-path-finder' ); ?></a>
						<?php if ( RoleRepository::ORIGIN_EDITED === $itcp_row['origin'] ) : ?>
							<form method="post" action="<?php echo esc_url( $itcp_post_url ); ?>" class="itcp-inline-form" data-itcp-confirm="<?php esc_attr_e( 'تغییرهای این شغل پاک شود و نسخه پیش‌فرض برگردد؟', 'it-career-path-finder' ); ?>">
								<input type="hidden" name="action" value="<?php echo esc_attr( RoleActions::RESTORE_ACTION ); ?>">
								<input type="hidden" name="role_id" value="<?php echo esc_attr( $itcp_row['id'] ); ?>">
								<?php wp_nonce_field( RoleActions::nonce_action_for( RoleActions::RESTORE_ACTION, $itcp_row['id'] ), '_wpnonce', false ); ?>
								<button type="submit" class="button button-small"><?php esc_html_e( 'بازگردانی پیش‌فرض', 'it-career-path-finder' ); ?></button>
							</form>
						<?php endif; ?>
						<form method="post" action="<?php echo esc_url( $itcp_post_url ); ?>" class="itcp-inline-form" data-itcp-confirm="<?php esc_attr_e( 'این شغل حذف شود؟', 'it-career-path-finder' ); ?>">
							<input type="hidden" name="action" value="<?php echo esc_attr( RoleActions::DELETE_ACTION ); ?>">
							<input type="hidden" name="role_id" value="<?php echo esc_attr( $itcp_row['id'] ); ?>">
							<?php wp_nonce_field( RoleActions::nonce_action_for( RoleActions::DELETE_ACTION, $itcp_row['id'] ), '_wpnonce', false ); ?>
							<button type="submit" class="button button-small button-link-delete"><?php esc_html_e( 'حذف', 'it-career-path-finder' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ( array() !== $view['deleted_rows'] ) : ?>
		<h2><?php esc_html_e( 'شغل‌های پیش‌فرضی که حذف کرده‌اید', 'it-career-path-finder' ); ?></h2>
		<p class="description"><?php esc_html_e( 'این شغل‌ها در ابزار نمایش داده نمی‌شوند. هر وقت خواستید می‌توانید برشان گردانید.', 'it-career-path-finder' ); ?></p>
		<table class="widefat striped itcp-deleted-table">
			<tbody>
				<?php foreach ( $view['deleted_rows'] as $itcp_deleted ) : ?>
					<tr>
						<td><?php echo esc_html( $itcp_deleted['title_fa'] ); ?> <code dir="ltr"><?php echo esc_html( $itcp_deleted['id'] ); ?></code></td>
						<td class="itcp-row-actions">
							<form method="post" action="<?php echo esc_url( $itcp_post_url ); ?>" class="itcp-inline-form">
								<input type="hidden" name="action" value="<?php echo esc_attr( RoleActions::RESTORE_ACTION ); ?>">
								<input type="hidden" name="role_id" value="<?php echo esc_attr( $itcp_deleted['id'] ); ?>">
								<?php wp_nonce_field( RoleActions::nonce_action_for( RoleActions::RESTORE_ACTION, $itcp_deleted['id'] ), '_wpnonce', false ); ?>
								<button type="submit" class="button button-small"><?php esc_html_e( 'برگرداندن', 'it-career-path-finder' ); ?></button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
</div>
