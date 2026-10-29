<?php

$GLOBALS['itcp_test_options'] = array();

function itcp_test_reset_options(): void {
	$GLOBALS['itcp_test_options'] = array();
}

function get_option( string $name, $default_value = false ) {
	return array_key_exists( $name, $GLOBALS['itcp_test_options'] ) ? $GLOBALS['itcp_test_options'][ $name ] : $default_value;
}

function update_option( string $name, $value, $autoload = null ): bool {
	$GLOBALS['itcp_test_options'][ $name ] = $value;
	return true;
}

function delete_option( string $name ): bool {
	unset( $GLOBALS['itcp_test_options'][ $name ] );
	return true;
}

function apply_filters( string $hook_name, $value ) {
	return $value;
}

function sanitize_key( string $key ): string {
	return (string) preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $key ) );
}

function sanitize_text_field( string $text ): string {
	return trim( (string) preg_replace( '/\s+/u', ' ', strip_tags( $text ) ) );
}

function sanitize_textarea_field( string $text ): string {
	return trim( strip_tags( $text ) );
}

function trailingslashit( string $path ): string {
	return rtrim( $path, '/\\' ) . '/';
}
