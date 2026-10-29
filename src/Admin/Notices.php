<?php

namespace ItCareerPathFinder\Admin;

defined( 'ABSPATH' ) || exit;

final class Notices {

	private const QUERY_KEY = 'itcp_notice';

	public static function redirect( string $page, string $notice, array $arguments = array() ): void {
		$query = array_merge(
			$arguments,
			array(
				'page'          => $page,
				self::QUERY_KEY => $notice,
			)
		);
		wp_safe_redirect( add_query_arg( $query, admin_url( 'admin.php' ) ) );
		exit;
	}

	public static function render(): void {
		$messages = self::messages();
		$notice   = self::current_notice_key();
		if ( ! isset( $messages[ $notice ] ) ) {
			return;
		}
		printf(
			'<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			esc_attr( $messages[ $notice ]['type'] ),
			esc_html( self::with_counts( $messages[ $notice ]['text'] ) )
		);
	}

	private static function current_notice_key(): string {
		return isset( $_GET[ self::QUERY_KEY ] ) ? sanitize_key( wp_unslash( $_GET[ self::QUERY_KEY ] ) ) : '';
	}

	private static function query_number( string $key ): int {
		return isset( $_GET[ $key ] ) ? absint( wp_unslash( $_GET[ $key ] ) ) : 0;
	}

	private static function with_counts( string $message ): string {
		return strtr(
			$message,
			array(
				'{imported}' => number_format_i18n( self::query_number( 'imported' ) ),
				'{skipped}'  => number_format_i18n( self::query_number( 'skipped' ) ),
			)
		);
	}

	private static function messages(): array {
		return array(
			'saved'         => array(
				'type' => 'success',
				'text' => __( 'شغل ذخیره شد.', 'it-career-path-finder' ),
			),
			'deleted'       => array(
				'type' => 'success',
				'text' => __( 'شغل حذف شد. اگر شغل پیش‌فرض بوده باشد، از پایین همین صفحه می‌توانید برش گردانید.', 'it-career-path-finder' ),
			),
			'restored'      => array(
				'type' => 'success',
				'text' => __( 'شغل به نسخه پیش‌فرض افزونه برگشت.', 'it-career-path-finder' ),
			),
			'invalid'       => array(
				'type' => 'error',
				'text' => __( 'بعضی فیلدهای ضروری خالی یا نامعتبرند. لطفاً موارد مشخص‌شده را کامل کنید.', 'it-career-path-finder' ),
			),
			'missing'       => array(
				'type' => 'error',
				'text' => __( 'این شغل پیدا نشد.', 'it-career-path-finder' ),
			),
			'imported'      => array(
				'type' => 'success',
				'text' => __( '{imported} شغل درون‌ریزی شد و {skipped} مورد نامعتبر کنار گذاشته شد.', 'it-career-path-finder' ),
			),
			'import_failed' => array(
				'type' => 'error',
				'text' => __( 'فایل JSON معتبر نبود یا هیچ شغل معتبری نداشت. هیچ چیزی تغییر نکرد.', 'it-career-path-finder' ),
			),
			'reset'         => array(
				'type' => 'success',
				'text' => __( 'همه تغییرها پاک شد و فهرست شغل‌ها به حالت پیش‌فرض افزونه برگشت.', 'it-career-path-finder' ),
			),
		);
	}
}
