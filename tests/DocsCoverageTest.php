<?php

namespace ItCareerPathFinder\Tests;

use ItCareerPathFinder\Tests\Support\TestCase;

final class DocsCoverageTest extends TestCase {

	private const CODE_PATTERNS = array(
		'*.php',
		'src/*/*.php',
		'src/*.php',
		'templates/*.php',
		'templates/admin/*.php',
		'assets/js/*.js',
		'assets/css/*.css',
		'data/*.json',
		'tests/*.php',
		'tests/Support/*.php',
		'tests/js/*',
		'tools/*.php',
	);

	private function code_files(): array {
		$files = array();
		foreach ( self::CODE_PATTERNS as $pattern ) {
			foreach ( glob( ITCP_TESTS_ROOT . $pattern ) as $file ) {
				if ( 'index.php' !== basename( $file ) && is_file( $file ) ) {
					$files[] = substr( $file, strlen( ITCP_TESTS_ROOT ) );
				}
			}
		}
		sort( $files );
		return array_values( array_unique( $files ) );
	}

	private function doc_path( string $code_file ): string {
		return ITCP_TESTS_ROOT . 'docs/code/' . preg_replace( '/\.[a-z]+$/', '.md', $code_file );
	}

	private function function_names( string $code_file ): array {
		$source = (string) file_get_contents( ITCP_TESTS_ROOT . $code_file );
		if ( '.php' === substr( $code_file, -4 ) ) {
			return $this->php_function_names( $source );
		}
		if ( '.js' === substr( $code_file, -3 ) ) {
			return $this->javascript_function_names( $source );
		}
		return array();
	}

	private function php_function_names( string $source ): array {
		$tokens = token_get_all( $source );
		$names  = array();
		foreach ( $tokens as $position => $token ) {
			if ( is_array( $token ) && T_FUNCTION === $token[0] ) {
				$name = $this->next_identifier( $tokens, $position );
				if ( null !== $name ) {
					$names[] = $name;
				}
			}
		}
		return array_values( array_unique( $names ) );
	}

	private function next_identifier( array $tokens, int $position ): ?string {
		for ( $next = $position + 1, $count = count( $tokens ); $next < $count; $next++ ) {
			$token = $tokens[ $next ];
			if ( is_array( $token ) && T_WHITESPACE === $token[0] ) {
				continue;
			}
			return is_array( $token ) && T_STRING === $token[0] ? $token[1] : null;
		}
		return null;
	}

	private function javascript_function_names( string $source ): array {
		preg_match_all( '/function\s+([A-Za-z_$][\w$]*)\s*\(|\.prototype\.(\w+)\s*=\s*function/', $source, $found );
		return array_values( array_unique( array_filter( array_merge( $found[1], $found[2] ) ) ) );
	}

	public function test_every_code_file_has_a_document(): void {
		$missing = array_filter(
			$this->code_files(),
			function ( string $file ): bool {
				return ! is_readable( $this->doc_path( $file ) );
			}
		);
		$this->assert_empty( array_values( $missing ), 'Code files without docs/code page:' );
	}

	public function test_every_function_is_documented(): void {
		$missing = array();
		foreach ( $this->code_files() as $file ) {
			$doc = is_readable( $this->doc_path( $file ) ) ? (string) file_get_contents( $this->doc_path( $file ) ) : '';
			foreach ( $this->function_names( $file ) as $name ) {
				if ( false === strpos( $doc, '`' . $name ) ) {
					$missing[] = $file . ' → ' . $name;
				}
			}
		}
		$this->assert_empty( $missing, 'Functions not mentioned in their doc page:' );
	}
}
