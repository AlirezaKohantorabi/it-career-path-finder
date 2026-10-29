<?php

namespace ItCareerPathFinder;

defined( 'ABSPATH' ) || exit;

final class Autoloader {

	private const NAMESPACE_PREFIX = 'ItCareerPathFinder\\';

	private static string $base_directory = '';

	public static function register( string $base_directory ): void {
		self::$base_directory = $base_directory;
		spl_autoload_register( array( self::class, 'load_class' ) );
	}

	public static function load_class( string $class_name ): void {
		if ( ! self::belongs_to_plugin( $class_name ) ) {
			return;
		}
		$file = self::file_for_class( $class_name );
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	private static function belongs_to_plugin( string $class_name ): bool {
		return 0 === strpos( $class_name, self::NAMESPACE_PREFIX );
	}

	private static function file_for_class( string $class_name ): string {
		$relative_class = substr( $class_name, strlen( self::NAMESPACE_PREFIX ) );
		return self::$base_directory . str_replace( '\\', '/', $relative_class ) . '.php';
	}
}
