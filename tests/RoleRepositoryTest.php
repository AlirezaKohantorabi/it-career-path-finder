<?php

namespace ItCareerPathFinder\Tests;

use ItCareerPathFinder\Data\BundledData;
use ItCareerPathFinder\Data\RoleChanges;
use ItCareerPathFinder\Data\RoleRepository;
use ItCareerPathFinder\Tests\Support\TestCase;

final class RoleRepositoryTest extends TestCase {

	private function repository(): RoleRepository {
		return new RoleRepository( new BundledData( ITCP_TESTS_ROOT . 'data/' ), new RoleChanges() );
	}

	private function bundled_count(): int {
		return count( ( new BundledData( ITCP_TESTS_ROOT . 'data/' ) )->roles() );
	}

	private function custom_role(): array {
		return array(
			'id'                => 'custom_test_role',
			'title_fa'          => 'شغل سفارشی',
			'category_id'       => 'software_development',
			'plain_description' => 'توضیح ساده',
		);
	}

	private function ids( array $roles ): array {
		return array_column( $roles, 'id' );
	}

	public function test_returns_every_bundled_role_without_changes(): void {
		$repository = $this->repository();
		$this->assert_same( $this->bundled_count(), count( $repository->all() ) );
		$this->assert_same( RoleRepository::ORIGIN_DEFAULT, $repository->origin_of( 'frontend_developer' ) );
	}

	public function test_edited_role_replaces_bundled_version_in_place(): void {
		$repository        = $this->repository();
		$role              = $repository->find( 'backend_developer' );
		$role['title_fa'] .= ' ویرایش';
		$repository->save( $role );
		$this->assert_same( 1, array_search( 'backend_developer', $this->ids( $repository->all() ), true ) );
		$this->assert_same( $role['title_fa'], $repository->find( 'backend_developer' )['title_fa'] );
		$this->assert_same( RoleRepository::ORIGIN_EDITED, $repository->origin_of( 'backend_developer' ) );
	}

	public function test_saving_bundled_content_again_returns_to_default(): void {
		$repository        = $this->repository();
		$original          = $repository->find( 'backend_developer' );
		$edited            = $original;
		$edited['title_fa'] = 'چیز دیگر';
		$repository->save( $edited );
		$repository->save( $original );
		$this->assert_same( RoleRepository::ORIGIN_DEFAULT, $repository->origin_of( 'backend_developer' ) );
		$this->assert_same( array(), ( new RoleChanges() )->saved_roles() );
	}

	public function test_custom_role_is_appended_and_marked_custom(): void {
		$repository = $this->repository();
		$repository->save( $this->custom_role() );
		$ids = $this->ids( $repository->all() );
		$this->assert_same( 'custom_test_role', end( $ids ) );
		$this->assert_same( RoleRepository::ORIGIN_CUSTOM, $repository->origin_of( 'custom_test_role' ) );
	}

	public function test_deleting_bundled_role_hides_it_until_restored(): void {
		$repository = $this->repository();
		$repository->delete( 'manual_qa' );
		$this->assert_false( $repository->exists( 'manual_qa' ) );
		$this->assert_same( array( 'manual_qa' ), $this->ids( $repository->deleted_defaults() ) );
		$repository->restore_default( 'manual_qa' );
		$this->assert_true( $repository->exists( 'manual_qa' ) );
		$this->assert_same( array(), $repository->deleted_defaults() );
	}

	public function test_deleting_custom_role_removes_it_completely(): void {
		$repository = $this->repository();
		$repository->save( $this->custom_role() );
		$repository->delete( 'custom_test_role' );
		$this->assert_false( $repository->exists( 'custom_test_role' ) );
		$this->assert_same( array(), $repository->deleted_defaults() );
	}

	public function test_replace_with_keeps_only_given_roles(): void {
		$repository = $this->repository();
		$keep       = array( $repository->find( 'data_analyst' ), $this->custom_role() );
		$repository->replace_with( $keep );
		$this->assert_same( array( 'data_analyst', 'custom_test_role' ), $this->ids( $repository->all() ) );
		$this->assert_same( RoleRepository::ORIGIN_DEFAULT, $repository->origin_of( 'data_analyst' ) );
	}

	public function test_reset_forgets_every_change(): void {
		$repository = $this->repository();
		$repository->save( $this->custom_role() );
		$repository->delete( 'manual_qa' );
		$repository->reset();
		$this->assert_same( $this->bundled_count(), count( $repository->all() ) );
	}
}
