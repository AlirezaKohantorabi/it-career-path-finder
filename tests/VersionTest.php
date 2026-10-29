<?php

namespace ItCareerPathFinder\Tests;

use ItCareerPathFinder\Tests\Support\TestCase;

final class VersionTest extends TestCase {

	private function read( string $file ): string {
		return (string) file_get_contents( ITCP_TESTS_ROOT . $file );
	}

	private function header_version(): string {
		preg_match( '/^\s*\*\s*Version:\s*(\S+)/m', $this->read( 'it-career-path-finder.php' ), $found );
		return $found[1] ?? '';
	}

	public function test_constant_matches_plugin_header(): void {
		preg_match( "/define\\( 'ITCP_VERSION', '([^']+)' \\);/", $this->read( 'it-career-path-finder.php' ), $found );
		$this->assert_same( $this->header_version(), $found[1] ?? '', 'ITCP_VERSION differs from the plugin header.' );
	}

	public function test_readme_stable_tag_matches_plugin_header(): void {
		preg_match( '/^Stable tag:\s*(\S+)/m', $this->read( 'readme.txt' ), $found );
		$this->assert_same( $this->header_version(), $found[1] ?? '', 'readme.txt Stable tag differs from the plugin header.' );
	}

	public function test_changelog_mentions_current_version(): void {
		$this->assert_true( false !== strpos( $this->read( 'CHANGELOG.md' ), '[' . $this->header_version() . ']' ), 'CHANGELOG.md has no entry for ' . $this->header_version() );
	}
}
