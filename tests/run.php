<?php

require_once __DIR__ . '/bootstrap.php';

use ItCareerPathFinder\Tests\Support\AssertionFailed;

function itcp_test_files(): array {
	$files = glob( __DIR__ . '/*Test.php' );
	sort( $files );
	return $files;
}

function itcp_test_class_for( string $file ): string {
	return 'ItCareerPathFinder\\Tests\\' . basename( $file, '.php' );
}

function itcp_test_methods( string $class_name ): array {
	return array_values(
		array_filter(
			get_class_methods( $class_name ),
			static function ( string $method ): bool {
				return 0 === strpos( $method, 'test_' );
			}
		)
	);
}

function itcp_run_test( string $class_name, string $method ): ?string {
	$test = new $class_name();
	$test->set_up();
	try {
		$test->$method();
		return null;
	} catch ( AssertionFailed $failure ) {
		return $failure->getMessage();
	} catch ( Throwable $error ) {
		return get_class( $error ) . ': ' . $error->getMessage() . ' (' . basename( $error->getFile() ) . ':' . $error->getLine() . ')';
	}
}

function itcp_run_all_tests(): int {
	$passed   = 0;
	$failures = array();
	foreach ( itcp_test_files() as $file ) {
		require_once $file;
		$class_name = itcp_test_class_for( $file );
		foreach ( itcp_test_methods( $class_name ) as $method ) {
			$failure = itcp_run_test( $class_name, $method );
			if ( null === $failure ) {
				++$passed;
				echo '.';
				continue;
			}
			$failures[] = basename( $file, '.php' ) . '::' . $method . "\n  " . $failure;
			echo 'F';
		}
	}
	echo "\n\n" . $passed . ' passed, ' . count( $failures ) . " failed\n";
	foreach ( $failures as $failure ) {
		echo "\nFAIL " . $failure . "\n";
	}
	return array() === $failures ? 0 : 1;
}

exit( itcp_run_all_tests() );
