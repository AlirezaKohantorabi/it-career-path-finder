<?php

use ItCareerPathFinder\Admin\Notices;
use ItCareerPathFinder\Admin\RoleActions;
use ItCareerPathFinder\Support\View;

defined( 'ABSPATH' ) || exit;

$itcp_role         = $view['role'];
$itcp_schema       = $view['schema'];
$itcp_ratings      = $itcp_role['ratings_1_to_5'] ?? array();
$itcp_legend       = $itcp_schema->rating_legend();
$itcp_error_labels = array(
	'id'                => __( 'شناسه شغل خالی است.', 'it-career-path-finder' ),
	'title_fa'          => __( 'عنوان فارسی خالی است.', 'it-career-path-finder' ),
	'category_id'       => __( 'دسته‌بندی انتخاب نشده است.', 'it-career-path-finder' ),
	'plain_description' => __( 'توضیح ساده (غیرفنی) خالی است.', 'it-career-path-finder' ),
	'duplicate_id'      => __( 'این شناسه قبلاً استفاده شده است (شاید در فهرست شغل‌های حذف‌شده باشد). یک شناسه دیگر انتخاب کنید.', 'it-career-path-finder' ),
	'missing_role'      => __( 'شغلی که ویرایش می‌کردید دیگر وجود ندارد.', 'it-career-path-finder' ),
);
?>
<div class="wrap itcp-admin">
	<h1><?php echo esc_html( $view['is_new'] ? __( 'افزودن شغل جدید', 'it-career-path-finder' ) : __( 'ویرایش شغل', 'it-career-path-finder' ) ); ?></h1>
	<?php Notices::render(); ?>

	<?php if ( $view['missing'] ) : ?>
		<div class="notice notice-error"><p><?php esc_html_e( 'این شغل پیدا نشد.', 'it-career-path-finder' ); ?></p></div>
		<p><a class="button" href="<?php echo esc_url( $view['list_url'] ); ?>"><?php esc_html_e( 'بازگشت به فهرست شغل‌ها', 'it-career-path-finder' ); ?></a></p>
	<?php else : ?>

		<?php if ( array() !== $view['errors'] ) : ?>
			<div class="notice notice-error">
				<ul class="itcp-error-list">
					<?php foreach ( $view['errors'] as $itcp_error ) : ?>
						<li><?php echo esc_html( $itcp_error_labels[ $itcp_error ] ?? $itcp_error ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="itcp-role-form">
			<input type="hidden" name="action" value="<?php echo esc_attr( RoleActions::SAVE_ACTION ); ?>">
			<?php wp_nonce_field( RoleActions::SAVE_ACTION ); ?>
			<?php if ( ! $view['is_new'] ) : ?>
				<input type="hidden" name="original_id" value="<?php echo esc_attr( $itcp_role['id'] ); ?>">
			<?php endif; ?>

			<div class="itcp-card">
				<h2><?php esc_html_e( 'اطلاعات اصلی', 'it-career-path-finder' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="itcp-id"><?php esc_html_e( 'شناسه (انگلیسی)', 'it-career-path-finder' ); ?></label></th>
						<td>
							<?php if ( $view['is_new'] ) : ?>
								<input type="text" id="itcp-id" name="role[id]" value="<?php echo esc_attr( $itcp_role['id'] ); ?>" class="regular-text" dir="ltr" maxlength="64" pattern="[a-z0-9_\-]+" required>
								<p class="description"><?php esc_html_e( 'فقط حروف کوچک انگلیسی، عدد، خط تیره و زیرخط. بعداً قابل تغییر نیست. مثال: wordpress_developer', 'it-career-path-finder' ); ?></p>
							<?php else : ?>
								<code dir="ltr"><?php echo esc_html( $itcp_role['id'] ); ?></code>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="itcp-title-fa"><?php esc_html_e( 'عنوان فارسی', 'it-career-path-finder' ); ?></label></th>
						<td><input type="text" id="itcp-title-fa" name="role[title_fa]" value="<?php echo esc_attr( $itcp_role['title_fa'] ); ?>" class="regular-text" maxlength="200" required></td>
					</tr>
					<tr>
						<th scope="row"><label for="itcp-title-en"><?php esc_html_e( 'عنوان انگلیسی', 'it-career-path-finder' ); ?></label></th>
						<td><input type="text" id="itcp-title-en" name="role[title_en]" value="<?php echo esc_attr( $itcp_role['title_en'] ); ?>" class="regular-text" dir="ltr" maxlength="200"></td>
					</tr>
					<tr>
						<th scope="row"><label for="itcp-category"><?php esc_html_e( 'دسته‌بندی', 'it-career-path-finder' ); ?></label></th>
						<td>
							<select id="itcp-category" name="role[category_id]" required>
								<option value=""><?php esc_html_e( 'یک دسته انتخاب کنید', 'it-career-path-finder' ); ?></option>
								<?php foreach ( $itcp_schema->categories() as $itcp_category ) : ?>
									<option value="<?php echo esc_attr( $itcp_category['id'] ); ?>" <?php selected( $itcp_role['category_id'], $itcp_category['id'] ); ?>><?php echo esc_html( $itcp_category['title'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="itcp-plain"><?php esc_html_e( 'این شغل به زبان ساده', 'it-career-path-finder' ); ?></label></th>
						<td>
							<textarea id="itcp-plain" name="role[plain_description]" rows="5" class="large-text" maxlength="2000" required><?php echo esc_textarea( $itcp_role['plain_description'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'خیلی ساده و غیرفنی بنویسید این شغل چیست و چه کار می‌کند؛ طوری که کسی که هیچ آشنایی با IT ندارد هم بفهمد. این متن اولین چیزی است که در کارت شغل دیده می‌شود.', 'it-career-path-finder' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<div class="itcp-card">
				<h2><?php esc_html_e( 'سطح‌ها و امتیازها (۱ تا ۵)', 'it-career-path-finder' ); ?></h2>
				<p class="description"><?php esc_html_e( 'این عددها مشخص می‌کنند شغل با جواب‌های کاربر چقدر جور است. ۳ یعنی متوسط.', 'it-career-path-finder' ); ?></p>
				<table class="form-table" role="presentation">
					<?php foreach ( $itcp_schema->rating_fields() as $itcp_field ) : ?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( 'itcp-rating-' . $itcp_field['key'] ); ?>"><?php echo esc_html( $itcp_field['label'] ); ?></label></th>
							<td>
								<?php
								View::display(
									'admin/rating-select',
									array(
										'id'     => 'itcp-rating-' . $itcp_field['key'],
										'name'   => 'role[ratings_1_to_5][' . $itcp_field['key'] . ']',
										'value'  => $itcp_ratings[ $itcp_field['key'] ] ?? 3,
										'legend' => $itcp_legend,
									)
								);
								?>
								<p class="description"><?php echo esc_html( $itcp_field['help'] ?? '' ); ?></p>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>

				<?php foreach ( $itcp_schema->fit_groups() as $itcp_group ) : ?>
					<h3><?php echo esc_html( $itcp_group['label'] ); ?></h3>
					<p class="description"><?php echo esc_html( $itcp_group['help'] ?? '' ); ?></p>
					<table class="form-table" role="presentation">
						<?php foreach ( $itcp_group['options'] as $itcp_option ) : ?>
							<?php $itcp_select_id = 'itcp-fit-' . $itcp_group['key'] . '-' . $itcp_option['key']; ?>
							<tr>
								<th scope="row"><label for="<?php echo esc_attr( $itcp_select_id ); ?>"><?php echo esc_html( $itcp_option['label'] ); ?></label></th>
								<td>
									<?php
									View::display(
										'admin/rating-select',
										array(
											'id'     => $itcp_select_id,
											'name'   => 'role[ratings_1_to_5][' . $itcp_group['key'] . '][' . $itcp_option['key'] . ']',
											'value'  => $itcp_ratings[ $itcp_group['key'] ][ $itcp_option['key'] ] ?? 3,
											'legend' => $itcp_legend,
										)
									);
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				<?php endforeach; ?>
			</div>

			<div class="itcp-card">
				<h2><?php esc_html_e( 'علاقه‌ها، محیط کار و برچسب‌ها', 'it-career-path-finder' ); ?></h2>
				<h3><?php esc_html_e( 'به چه علاقه‌هایی می‌خورد؟', 'it-career-path-finder' ); ?></h3>
				<?php
				View::display(
					'admin/checkbox-group',
					array(
						'name'     => 'role[interest_tags]',
						'legend'   => __( 'علاقه‌ها', 'it-career-path-finder' ),
						'options'  => $itcp_schema->interest_tags(),
						'selected' => $itcp_role['interest_tags'],
					)
				);
				?>
				<h3><?php esc_html_e( 'محیط‌ها و کارفرماهای رایج در ایران', 'it-career-path-finder' ); ?></h3>
				<?php
				View::display(
					'admin/checkbox-group',
					array(
						'name'     => 'role[typical_workplaces]',
						'legend'   => __( 'محیط‌های کار', 'it-career-path-finder' ),
						'options'  => $itcp_schema->workplace_types(),
						'selected' => $itcp_role['typical_workplaces'],
					)
				);
				?>
				<h3><?php esc_html_e( 'برچسب‌ها', 'it-career-path-finder' ); ?></h3>
				<p class="description"><?php esc_html_e( 'برچسب «احتمال فشار کاری یا کار فوری» روی امتیاز کسانی که آن‌کال را قبول ندارند اثر می‌گذارد.', 'it-career-path-finder' ); ?></p>
				<?php
				View::display(
					'admin/checkbox-group',
					array(
						'name'     => 'role[labels]',
						'legend'   => __( 'برچسب‌ها', 'it-career-path-finder' ),
						'options'  => $itcp_schema->role_labels(),
						'selected' => $itcp_role['labels'],
					)
				);
				?>
			</div>

			<div class="itcp-card">
				<h2><?php esc_html_e( 'مهارت‌ها و راه ورود', 'it-career-path-finder' ); ?></h2>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="itcp-skills"><?php esc_html_e( 'مهارت‌های ضروری', 'it-career-path-finder' ); ?></label></th>
						<td>
							<textarea id="itcp-skills" name="role[essential_skills]" rows="5" class="large-text"><?php echo esc_textarea( implode( "\n", $itcp_role['essential_skills'] ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'هر مهارت در یک خط. حداکثر ۲۰ مورد. دو مورد اول در برنامه ۳۰ روزه استفاده می‌شوند.', 'it-career-path-finder' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="itcp-route"><?php esc_html_e( 'مسیر پیشنهادی ورود', 'it-career-path-finder' ); ?></label></th>
						<td><textarea id="itcp-route" name="role[recommended_entry_route]" rows="3" class="large-text" maxlength="2000"><?php echo esc_textarea( $itcp_role['recommended_entry_route'] ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="itcp-caveat"><?php esc_html_e( 'هشدار بازار کار', 'it-career-path-finder' ); ?></label></th>
						<td><textarea id="itcp-caveat" name="role[market_caveat]" rows="3" class="large-text" maxlength="2000"><?php echo esc_textarea( $itcp_role['market_caveat'] ); ?></textarea></td>
					</tr>
				</table>
			</div>

			<p class="submit">
				<button type="submit" class="button button-primary button-large"><?php esc_html_e( 'ذخیره شغل', 'it-career-path-finder' ); ?></button>
				<a class="button button-large" href="<?php echo esc_url( $view['list_url'] ); ?>"><?php esc_html_e( 'انصراف', 'it-career-path-finder' ); ?></a>
			</p>
		</form>
	<?php endif; ?>
</div>
