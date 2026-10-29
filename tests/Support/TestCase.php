<?php

namespace ItCareerPathFinder\Tests\Support;

class AssertionFailed extends \RuntimeException {
}

abstract class TestCase {

	public function set_up(): void {
		itcp_test_reset_options();
	}

	protected function assert_same( $expected, $actual, string $message = '' ): void {
		if ( $expected !== $actual ) {
			$this->fail( $message . ' Expected ' . var_export( $expected, true ) . ' but got ' . var_export( $actual, true ) );
		}
	}

	protected function assert_true( bool $condition, string $message = '' ): void {
		if ( ! $condition ) {
			$this->fail( '' !== $message ? $message : 'Expected true.' );
		}
	}

	protected function assert_false( bool $condition, string $message = '' ): void {
		$this->assert_true( ! $condition, '' !== $message ? $message : 'Expected false.' );
	}

	protected function assert_contains( $needle, array $haystack, string $message = '' ): void {
		if ( ! in_array( $needle, $haystack, true ) ) {
			$this->fail( $message . ' Missing ' . var_export( $needle, true ) );
		}
	}

	protected function assert_empty( array $list, string $message = '' ): void {
		if ( array() !== $list ) {
			$this->fail( $message . ' ' . implode( "\n  - ", array_map( 'strval', $list ) ) );
		}
	}

	protected function fail( string $message ): void {
		throw new AssertionFailed( trim( $message ) );
	}
}
