<?php

namespace ItCareerPathFinder\Support;

defined( 'ABSPATH' ) || exit;

final class View {

	private const SAFE_NAME_PATTERN = '#^[a-z0-9\-]+(/[a-z0-9\-]+)*$#';

	public static function render( string $template, array $view = array() ): string {
		ob_start();
		self::include_template( $template, $view );
		return (string) ob_get_clean();
	}

	public static function display( string $template, array $view = array() ): void {
		self::include_template( $template, $view );
	}

	private static function include_template( string $template, array $view ): void {
		if ( ! self::is_safe_name( $template ) ) {
			return;
		}
		$file = ITCP_PLUGIN_DIR . 'templates/' . $template . '.php';
		if ( is_readable( $file ) ) {
			include $file;
		}
	}

	private static function is_safe_name( string $template ): bool {
		return 1 === preg_match( self::SAFE_NAME_PATTERN, $template );
	}
}
