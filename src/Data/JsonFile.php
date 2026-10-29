<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class JsonFile {

	private const MAX_DEPTH = 32;

	public static function decode_file( string $path ): array {
		if ( ! is_readable( $path ) ) {
			return array();
		}
		$contents = file_get_contents( $path );
		return false === $contents ? array() : self::decode_text( $contents );
	}

	public static function decode_text( string $text ): array {
		$decoded = json_decode( self::without_byte_order_mark( $text ), true, self::MAX_DEPTH );
		return is_array( $decoded ) ? $decoded : array();
	}

	private static function without_byte_order_mark( string $text ): string {
		return 0 === strpos( $text, "\xEF\xBB\xBF" ) ? substr( $text, 3 ) : $text;
	}
}
