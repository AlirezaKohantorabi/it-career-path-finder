<?php

namespace ItCareerPathFinder\Tests;

use ItCareerPathFinder\Data\BundledData;
use ItCareerPathFinder\Data\RoleChanges;
use ItCareerPathFinder\Data\RoleImporter;
use ItCareerPathFinder\Data\RoleRepository;
use ItCareerPathFinder\Data\RoleSanitizer;
use ItCareerPathFinder\Data\RoleSchema;
use ItCareerPathFinder\Tests\Support\TestCase;

final class RoleImporterTest extends TestCase {

	private RoleRepository $repository;

	private RoleImporter $importer;

	public function set_up(): void {
		parent::set_up();
		$bundled          = new BundledData( ITCP_TESTS_ROOT . 'data/' );
		$this->repository = new RoleRepository( $bundled, new RoleChanges() );
		$this->importer   = new RoleImporter( new RoleSanitizer( new RoleSchema( $bundled->vocabulary() ) ), $this->repository );
	}

	private function new_role( string $role_id ): array {
		return array(
			'id'                => $role_id,
			'title_fa'          => 'شغل ' . $role_id,
			'category_id'       => 'data_ai',
			'plain_description' => 'توضیح',
		);
	}

	public function test_merges_roles_from_export_document(): void {
		$json   = json_encode( array( 'roles' => array( $this->new_role( 'imported_one' ) ) ) );
		$result = $this->importer->import( $json, RoleImporter::MODE_MERGE );
		$this->assert_same( array( 'imported' => 1, 'skipped' => 0 ), $result );
		$this->assert_true( $this->repository->exists( 'imported_one' ) );
		$this->assert_true( $this->repository->exists( 'frontend_developer' ) );
	}

	public function test_accepts_a_plain_list_of_roles(): void {
		$result = $this->importer->import( json_encode( array( $this->new_role( 'listed_role' ) ) ), RoleImporter::MODE_MERGE );
		$this->assert_same( 1, $result['imported'] );
	}

	public function test_skips_invalid_and_duplicate_roles(): void {
		$roles  = array( $this->new_role( 'good_role' ), array( 'id' => 'no_title' ), 'not an object', $this->new_role( 'good_role' ) );
		$result = $this->importer->import( json_encode( array( 'roles' => $roles ) ), RoleImporter::MODE_MERGE );
		$this->assert_same( array( 'imported' => 1, 'skipped' => 3 ), $result );
	}

	public function test_replace_mode_keeps_only_imported_roles(): void {
		$this->importer->import( json_encode( array( 'roles' => array( $this->new_role( 'only_role' ) ) ) ), RoleImporter::MODE_REPLACE );
		$this->assert_same( array( 'only_role' ), array_column( $this->repository->all(), 'id' ) );
	}

	public function test_changes_nothing_when_no_role_is_valid(): void {
		$before = count( $this->repository->all() );
		$this->assert_same( null, $this->importer->import( '{"roles":[{"id":"x"}]}', RoleImporter::MODE_REPLACE ) );
		$this->assert_same( null, $this->importer->import( 'not json at all', RoleImporter::MODE_REPLACE ) );
		$this->assert_same( null, $this->importer->import( '{"id":"single_object"}', RoleImporter::MODE_REPLACE ) );
		$this->assert_same( $before, count( $this->repository->all() ) );
	}
}
