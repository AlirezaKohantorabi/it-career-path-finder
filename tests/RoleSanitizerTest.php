<?php

namespace ItCareerPathFinder\Tests;

use ItCareerPathFinder\Data\BundledData;
use ItCareerPathFinder\Data\RoleSanitizer;
use ItCareerPathFinder\Data\RoleSchema;
use ItCareerPathFinder\Tests\Support\TestCase;

final class RoleSanitizerTest extends TestCase {

	private function sanitizer(): RoleSanitizer {
		$bundled = new BundledData( ITCP_TESTS_ROOT . 'data/' );
		return new RoleSanitizer( new RoleSchema( $bundled->vocabulary() ) );
	}

	public function test_strips_html_and_unsafe_characters(): void {
		$role = $this->sanitizer()->sanitize(
			array(
				'id'                => 'My Role<script>',
				'title_fa'          => '<b>شغل</b> تستی',
				'plain_description' => "خط اول<img src=x onerror=alert(1)>\nخط دوم",
			)
		);
		$this->assert_same( 'myrolescript', $role['id'] );
		$this->assert_same( 'شغل تستی', $role['title_fa'] );
		$this->assert_same( "خط اول\nخط دوم", $role['plain_description'] );
	}

	public function test_drops_unknown_category_tags_workplaces_and_labels(): void {
		$role = $this->sanitizer()->sanitize(
			array(
				'category_id'        => 'not_a_category',
				'interest_tags'      => array( 'coding', 'hacking', 'coding' ),
				'typical_workplaces' => array( 'startup', 'moon_base' ),
				'labels'             => array( 'remote_compatible', 'fake_label' ),
			)
		);
		$this->assert_same( '', $role['category_id'] );
		$this->assert_same( array( 'coding' ), $role['interest_tags'] );
		$this->assert_same( array( 'startup' ), $role['typical_workplaces'] );
		$this->assert_same( array( 'remote_compatible' ), $role['labels'] );
	}

	public function test_clamps_ratings_and_fills_missing_with_neutral(): void {
		$role = $this->sanitizer()->sanitize(
			array(
				'ratings_1_to_5' => array(
					'entry_difficulty' => 9,
					'income_ceiling'   => '-4',
					'job_stability'    => 'abc',
					'work_mode_fit'    => array( 'remote' => 4.6 ),
				),
			)
		);
		$ratings = $role['ratings_1_to_5'];
		$this->assert_same( 5, $ratings['entry_difficulty'] );
		$this->assert_same( 1, $ratings['income_ceiling'] );
		$this->assert_same( 3, $ratings['job_stability'] );
		$this->assert_same( 3, $ratings['stress_level'] );
		$this->assert_same( 5, $ratings['work_mode_fit']['remote'] );
		$this->assert_same( 3, $ratings['work_mode_fit']['onsite'] );
		$this->assert_same( 3, $ratings['client_type_fit']['public_sector'] );
	}

	public function test_splits_skill_text_into_clean_lines(): void {
		$role = $this->sanitizer()->sanitize( array( 'essential_skills' => "Git\r\n\n <b>SQL</b> \nDocker" ) );
		$this->assert_same( array( 'Git', 'SQL', 'Docker' ), $role['essential_skills'] );
	}

	public function test_limits_text_length(): void {
		$role = $this->sanitizer()->sanitize( array( 'title_fa' => str_repeat( 'ا', 500 ) ) );
		$this->assert_same( 200, mb_strlen( $role['title_fa'] ) );
	}

	public function test_reports_missing_required_fields(): void {
		$sanitizer = $this->sanitizer();
		$missing   = $sanitizer->missing_required_fields( $sanitizer->sanitize( array( 'title_fa' => 'فقط عنوان' ) ) );
		$this->assert_same( array( 'id', 'category_id', 'plain_description' ), $missing );
	}

	public function test_bundled_roles_survive_sanitizing_unchanged(): void {
		$bundled   = new BundledData( ITCP_TESTS_ROOT . 'data/' );
		$sanitizer = $this->sanitizer();
		foreach ( $bundled->roles() as $role ) {
			$this->assert_true( $sanitizer->sanitize( $role ) == $role, 'Sanitizing changed bundled role ' . $role['id'] );
		}
	}
}
