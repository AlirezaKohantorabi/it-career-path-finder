<?php

const ITCP_BUILD_SLUG    = 'it-career-path-finder';
const ITCP_BUILD_ENTRIES = array(
	'it-career-path-finder.php',
	'uninstall.php',
	'index.php',
	'readme.txt',
	'README.md',
	'CHANGELOG.md',
	'LICENSE',
	'assets',
	'data',
	'languages',
	'src',
	'templates',
);

function itcp_build_root(): string {
	return dirname( __DIR__ ) . '/';
}

function itcp_build_version(): string {
	$header = (string) file_get_contents( itcp_build_root() . ITCP_BUILD_SLUG . '.php' );
	preg_match( '/^\s*\*\s*Version:\s*(\S+)/m', $header, $found );
	return $found[1] ?? 'dev';
}

function itcp_build_files_in( string $relative ): array {
	$path = itcp_build_root() . $relative;
	if ( is_file( $path ) ) {
		return array( $relative );
	}
	$files    = array();
	$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $path, FilesystemIterator::SKIP_DOTS ) );
	foreach ( $iterator as $file ) {
		$files[] = $relative . '/' . str_replace( '\\', '/', substr( $file->getPathname(), strlen( $path ) + 1 ) );
	}
	return $files;
}

function itcp_build_files(): array {
	$files = array();
	foreach ( ITCP_BUILD_ENTRIES as $entry ) {
		if ( ! file_exists( itcp_build_root() . $entry ) ) {
			throw new RuntimeException( 'Missing build entry: ' . $entry );
		}
		$files = array_merge( $files, itcp_build_files_in( $entry ) );
	}
	sort( $files );
	return $files;
}

function itcp_build_zip( string $target, array $files ): void {
	$zip = new ZipArchive();
	if ( true !== $zip->open( $target, ZipArchive::CREATE | ZipArchive::OVERWRITE ) ) {
		throw new RuntimeException( 'Cannot create ' . $target );
	}
	foreach ( $files as $file ) {
		$zip->addFile( itcp_build_root() . $file, ITCP_BUILD_SLUG . '/' . $file );
	}
	$zip->close();
}

function itcp_build_main(): int {
	if ( ! class_exists( 'ZipArchive' ) ) {
		fwrite( STDERR, "The PHP zip extension is required (enable extension=zip in php.ini).\n" );
		return 1;
	}
	$directory = itcp_build_root() . 'dist';
	if ( ! is_dir( $directory ) ) {
		mkdir( $directory, 0755, true );
	}
	$files  = itcp_build_files();
	$target = $directory . '/' . ITCP_BUILD_SLUG . '-' . itcp_build_version() . '.zip';
	itcp_build_zip( $target, $files );
	echo 'Built ' . $target . ' (' . count( $files ) . " files)\n";
	return 0;
}

exit( itcp_build_main() );
