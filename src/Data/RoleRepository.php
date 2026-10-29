<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class RoleRepository {

	public const ORIGIN_DEFAULT = 'default';
	public const ORIGIN_EDITED  = 'edited';
	public const ORIGIN_CUSTOM  = 'custom';

	private BundledData $bundled;

	private RoleChanges $changes;

	public function __construct( BundledData $bundled, RoleChanges $changes ) {
		$this->bundled = $bundled;
		$this->changes = $changes;
	}

	public function all(): array {
		return array_merge( $this->effective_bundled_roles(), $this->custom_roles() );
	}

	public function find( string $role_id ): ?array {
		foreach ( $this->all() as $role ) {
			if ( ( $role['id'] ?? '' ) === $role_id ) {
				return $role;
			}
		}
		return null;
	}

	public function exists( string $role_id ): bool {
		return null !== $this->find( $role_id );
	}

	public function is_bundled( string $role_id ): bool {
		return null !== $this->bundled_role( $role_id );
	}

	public function origin_of( string $role_id ): string {
		if ( ! $this->is_bundled( $role_id ) ) {
			return self::ORIGIN_CUSTOM;
		}
		return isset( $this->changes->saved_roles()[ $role_id ] ) ? self::ORIGIN_EDITED : self::ORIGIN_DEFAULT;
	}

	public function deleted_defaults(): array {
		$deleted = $this->changes->deleted_ids();
		return array_values(
			array_filter(
				$this->bundled->roles(),
				static function ( array $role ) use ( $deleted ): bool {
					return in_array( $role['id'] ?? '', $deleted, true );
				}
			)
		);
	}

	public function save( array $role ): void {
		if ( $this->matches_bundled_version( $role ) ) {
			$this->restore_default( $role['id'] );
			return;
		}
		$this->changes->save_role( $role );
	}

	public function delete( string $role_id ): void {
		if ( $this->is_bundled( $role_id ) ) {
			$this->changes->mark_deleted( $role_id );
			return;
		}
		$this->changes->forget_role( $role_id );
	}

	public function restore_default( string $role_id ): void {
		$this->changes->forget_role( $role_id );
		$this->changes->unmark_deleted( $role_id );
	}

	public function merge( array $roles ): void {
		foreach ( $roles as $role ) {
			$this->save( $role );
		}
	}

	public function replace_with( array $roles ): void {
		$saved = array();
		foreach ( $roles as $role ) {
			if ( ! $this->matches_bundled_version( $role ) ) {
				$saved[ $role['id'] ] = $role;
			}
		}
		$this->changes->replace_all( $saved, $this->bundled_ids_missing_from( $roles ) );
	}

	public function reset(): void {
		$this->changes->clear();
	}

	private function effective_bundled_roles(): array {
		$saved   = $this->changes->saved_roles();
		$deleted = $this->changes->deleted_ids();
		$roles   = array();
		foreach ( $this->bundled->roles() as $role ) {
			$role_id = (string) ( $role['id'] ?? '' );
			if ( '' !== $role_id && ! in_array( $role_id, $deleted, true ) ) {
				$roles[] = $saved[ $role_id ] ?? $role;
			}
		}
		return $roles;
	}

	private function custom_roles(): array {
		$roles = array();
		foreach ( $this->changes->saved_roles() as $role_id => $role ) {
			if ( ! $this->is_bundled( (string) $role_id ) ) {
				$roles[] = $role;
			}
		}
		return $roles;
	}

	private function bundled_role( string $role_id ): ?array {
		foreach ( $this->bundled->roles() as $role ) {
			if ( ( $role['id'] ?? '' ) === $role_id ) {
				return $role;
			}
		}
		return null;
	}

	private function matches_bundled_version( array $role ): bool {
		$bundled = $this->bundled_role( (string) ( $role['id'] ?? '' ) );
		return null !== $bundled && $bundled == $role;
	}

	private function bundled_ids_missing_from( array $roles ): array {
		$kept = array_column( $roles, 'id' );
		$ids  = array();
		foreach ( $this->bundled->roles() as $role ) {
			$role_id = (string) ( $role['id'] ?? '' );
			if ( '' !== $role_id && ! in_array( $role_id, $kept, true ) ) {
				$ids[] = $role_id;
			}
		}
		return $ids;
	}
}
