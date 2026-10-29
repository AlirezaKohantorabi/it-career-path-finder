<?php

use ItCareerPathFinder\Admin\Notices;
use ItCareerPathFinder\Admin\TransferActions;
use ItCareerPathFinder\Data\RoleImporter;

defined( 'ABSPATH' ) || exit;

$itcp_post_url = admin_url( 'admin-post.php' );
?>
<div class="wrap itcp-admin">
	<h1><?php esc_html_e( 'درون‌ریزی و برون‌بری شغل‌ها', 'it-career-path-finder' ); ?></h1>
	<?php Notices::render(); ?>

	<div class="itcp-card">
		<h2><?php esc_html_e( 'برون‌بری (دانلود فایل JSON)', 'it-career-path-finder' ); ?></h2>
		<p><?php esc_html_e( 'همه شغل‌هایی که الان در ابزار نمایش داده می‌شوند، با همه تغییرهای شما، در یک فایل JSON دانلود می‌شوند. این فایل دقیقاً هم‌قالب data/roles.json افزونه است؛ پس می‌توانید آن را جایگزین فایل اصلی در مخزن گیت‌هاب کنید.', 'it-career-path-finder' ); ?></p>
		<form method="post" action="<?php echo esc_url( $itcp_post_url ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( TransferActions::EXPORT_ACTION ); ?>">
			<?php wp_nonce_field( TransferActions::EXPORT_ACTION ); ?>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'دانلود فایل JSON', 'it-career-path-finder' ); ?></button>
		</form>
	</div>

	<div class="itcp-card">
		<h2><?php esc_html_e( 'درون‌ریزی (بارگذاری فایل JSON)', 'it-career-path-finder' ); ?></h2>
		<p><?php esc_html_e( 'یک فایل JSON با همان قالب برون‌بری انتخاب کنید یا متن آن را در کادر پایین بچسبانید. هر شغل بررسی و پاک‌سازی می‌شود و موارد نامعتبر کنار گذاشته می‌شوند.', 'it-career-path-finder' ); ?></p>
		<form method="post" action="<?php echo esc_url( $itcp_post_url ); ?>" enctype="multipart/form-data">
			<input type="hidden" name="action" value="<?php echo esc_attr( TransferActions::IMPORT_ACTION ); ?>">
			<?php wp_nonce_field( TransferActions::IMPORT_ACTION ); ?>
			<p>
				<label for="itcp-file"><?php esc_html_e( 'فایل JSON (حداکثر ۲ مگابایت):', 'it-career-path-finder' ); ?></label><br>
				<input type="file" id="itcp-file" name="itcp_file" accept=".json,application/json">
			</p>
			<p>
				<label for="itcp-json"><?php esc_html_e( 'یا متن JSON را اینجا بچسبانید:', 'it-career-path-finder' ); ?></label><br>
				<textarea id="itcp-json" name="itcp_json" rows="8" class="large-text code" dir="ltr"></textarea>
			</p>
			<fieldset>
				<legend><?php esc_html_e( 'روش درون‌ریزی:', 'it-career-path-finder' ); ?></legend>
				<label><input type="radio" name="itcp_mode" value="<?php echo esc_attr( RoleImporter::MODE_MERGE ); ?>" checked> <?php esc_html_e( 'ادغام: شغل‌های جدید اضافه و شغل‌های هم‌شناسه جایگزین شوند (بقیه دست‌نخورده می‌مانند).', 'it-career-path-finder' ); ?></label><br>
				<label><input type="radio" name="itcp_mode" value="<?php echo esc_attr( RoleImporter::MODE_REPLACE ); ?>"> <?php esc_html_e( 'جایگزینی کامل: فقط شغل‌های داخل فایل باقی بمانند.', 'it-career-path-finder' ); ?></label>
			</fieldset>
			<p><button type="submit" class="button button-primary"><?php esc_html_e( 'درون‌ریزی', 'it-career-path-finder' ); ?></button></p>
		</form>
	</div>

	<div class="itcp-card itcp-danger">
		<h2><?php esc_html_e( 'بازگشت به حالت پیش‌فرض', 'it-career-path-finder' ); ?></h2>
		<p><?php esc_html_e( 'همه شغل‌های اضافه‌شده، ویرایش‌ها و حذف‌ها پاک می‌شوند و فهرست شغل‌ها دقیقاً مثل فایل data/roles.json افزونه می‌شود. قبلش یک برون‌بری بگیرید.', 'it-career-path-finder' ); ?></p>
		<form method="post" action="<?php echo esc_url( $itcp_post_url ); ?>" data-itcp-confirm="<?php esc_attr_e( 'مطمئنید؟ همه تغییرهای شما در شغل‌ها پاک می‌شود.', 'it-career-path-finder' ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( TransferActions::RESET_ACTION ); ?>">
			<?php wp_nonce_field( TransferActions::RESET_ACTION ); ?>
			<button type="submit" class="button button-link-delete"><?php esc_html_e( 'پاک کردن همه تغییرها', 'it-career-path-finder' ); ?></button>
		</form>
	</div>
</div>
