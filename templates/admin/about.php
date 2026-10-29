<?php

defined( 'ABSPATH' ) || exit;

$itcp_shortcode = '[' . $view['shortcode'] . ']';
?>
<div class="wrap itcp-admin">
	<h1><?php esc_html_e( 'راهنما و سازندگان', 'it-career-path-finder' ); ?></h1>

	<div class="itcp-card">
		<h2><?php esc_html_e( 'چطور ابزار را در سایت نمایش بدهم؟', 'it-career-path-finder' ); ?></h2>
		<p><?php esc_html_e( 'این شورت‌کد را در هر برگه، نوشته یا ابزارکی قرار دهید. در ویرایشگر بلوکی (گوتنبرگ) از بلوک «کد کوتاه» استفاده کنید.', 'it-career-path-finder' ); ?></p>
		<p><code dir="ltr"><?php echo esc_html( $itcp_shortcode ); ?></code></p>
		<h3><?php esc_html_e( 'تغییر عنوان و زیرعنوان', 'it-career-path-finder' ); ?></h3>
		<p><?php esc_html_e( 'اگر می‌خواهید در یک صفحه عنوان دیگری نمایش داده شود:', 'it-career-path-finder' ); ?></p>
		<p><code dir="ltr"><?php echo esc_html( '[' . $view['shortcode'] . ' title="..." subtitle="..."]' ); ?></code></p>
		<p class="description"><?php esc_html_e( 'فونت ابزار همان فونت پیش‌فرض قالب سایت شماست. پاسخ‌های کاربران فقط در مرورگر خودشان می‌ماند و به سرور فرستاده نمی‌شود.', 'it-career-path-finder' ); ?></p>
	</div>

	<div class="itcp-card">
		<h2><?php esc_html_e( 'سازندگان', 'it-career-path-finder' ); ?></h2>
		<ul class="itcp-developers">
			<?php foreach ( $view['developers'] as $itcp_developer ) : ?>
				<li>
					<strong><?php echo esc_html( $itcp_developer['name'] ); ?></strong>
					<?php if ( '' !== $itcp_developer['website'] ) : ?>
						— <a href="<?php echo esc_url( $itcp_developer['website'] ); ?>" target="_blank" rel="noopener noreferrer" dir="ltr"><?php echo esc_html( wp_parse_url( $itcp_developer['website'], PHP_URL_HOST ) ); ?></a>
					<?php endif; ?>
					— <a href="<?php echo esc_url( $itcp_developer['github'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'گیت‌هاب', 'it-career-path-finder' ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
		<p>
			<?php esc_html_e( 'نسخه افزونه:', 'it-career-path-finder' ); ?>
			<code dir="ltr"><?php echo esc_html( $view['version'] ); ?></code>
			— <a href="<?php echo esc_url( $view['repository'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'کد منبع و مستندات در گیت‌هاب', 'it-career-path-finder' ); ?></a>
		</p>
	</div>
</div>
