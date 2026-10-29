<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class RoleImporter {

	public const MODE_MERGE   = 'merge';
	public const MODE_REPLACE = 'replace';

	private const MAX_ROLES = 500;

	private RoleSanitizer $sanitizer;

	private RoleRepository $roles;

	public function __construct( RoleSanitizer $sanitizer, RoleRepository $roles ) {
		$this->sanitizer = $sanitizer;
		$this->roles     = $roles;
	}

	public function import( string $json, string $mode ): ?array {
		$candidates = $this->role_candidates( $json );
		$valid      = $this->valid_roles( $candidates );
		if ( array() === $valid ) {
			return null;
		}
		if ( self::MODE_REPLACE === $mode ) {
			$this->roles->replace_with( $valid );
		} else {
			$this->roles->merge( $valid );
		}
		return array(
			'imported' => count( $valid ),
			'skipped'  => count( $candidates ) - count( $valid ),
		);
	}

	private function role_candidates( string $json ): array {
		$document = JsonFile::decode_text( $json );
		$roles    = isset( $document['roles'] ) && is_array( $document['roles'] ) ? $document['roles'] : $document;
		$is_list  = array_values( $roles ) === $roles;
		return $is_list ? array_slice( $roles, 0, self::MAX_ROLES ) : array();
	}

	private function valid_roles( array $candidates ): array {
		$valid = array();
		foreach ( $candidates as $candidate ) {
			$role = $this->sanitizer->sanitize( is_array( $candidate ) ? $candidate : array() );
			if ( $this->is_complete( $role ) && ! isset( $valid[ $role['id'] ] ) ) {
				$valid[ $role['id'] ] = $role;
			}
		}
		return array_values( $valid );
	}

	private function is_complete( array $role ): bool {
		return array() === $this->sanitizer->missing_required_fields( $role );
	}
}
