<?php

namespace ItCareerPathFinder\Admin;

use ItCareerPathFinder\Data\RoleRepository;
use ItCareerPathFinder\Data\RoleSanitizer;

defined( 'ABSPATH' ) || exit;

final class RoleActions {

	public const SAVE_ACTION    = 'itcp_save_role';
	public const DELETE_ACTION  = 'itcp_delete_role';
	public const RESTORE_ACTION = 'itcp_restore_role';

	private RoleRepository $roles;

	private RoleSanitizer $sanitizer;

	public function __construct( RoleRepository $roles, RoleSanitizer $sanitizer ) {
		$this->roles     = $roles;
		$this->sanitizer = $sanitizer;
	}

	public static function nonce_action_for( string $action, string $role_id ): string {
		return $action . '_' . $role_id;
	}

	public function register(): void {
		add_action( 'admin_post_' . self::SAVE_ACTION, array( $this, 'save' ) );
		add_action( 'admin_post_' . self::DELETE_ACTION, array( $this, 'delete' ) );
		add_action( 'admin_post_' . self::RESTORE_ACTION, array( $this, 'restore' ) );
	}

	public function save(): void {
		RequestGuard::verify_action( self::SAVE_ACTION );
		$original_id = $this->posted_key( 'original_id' );
		$role        = $this->sanitizer->sanitize( $this->posted_role( $original_id ) );
		$errors      = $this->validation_errors( $role, $original_id );
		if ( array() !== $errors ) {
			Drafts::keep( $role, $errors );
			Notices::redirect( RoleEditorPage::SLUG, 'invalid', $this->editor_arguments( $original_id ) );
		}
		$this->roles->save( $role );
		Notices::redirect( RolesPage::SLUG, 'saved' );
	}

	public function delete(): void {
		$role_id = $this->posted_key( 'role_id' );
		RequestGuard::verify_action( self::nonce_action_for( self::DELETE_ACTION, $role_id ) );
		if ( ! $this->roles->exists( $role_id ) ) {
			Notices::redirect( RolesPage::SLUG, 'missing' );
		}
		$this->roles->delete( $role_id );
		Notices::redirect( RolesPage::SLUG, 'deleted' );
	}

	public function restore(): void {
		$role_id = $this->posted_key( 'role_id' );
		RequestGuard::verify_action( self::nonce_action_for( self::RESTORE_ACTION, $role_id ) );
		if ( ! $this->roles->is_bundled( $role_id ) ) {
			Notices::redirect( RolesPage::SLUG, 'missing' );
		}
		$this->roles->restore_default( $role_id );
		Notices::redirect( RolesPage::SLUG, 'restored' );
	}

	private function posted_role( string $original_id ): array {
		$role = isset( $_POST['role'] ) && is_array( $_POST['role'] ) ? wp_unslash( $_POST['role'] ) : array();
		if ( '' !== $original_id ) {
			$role['id'] = $original_id;
		}
		return $role;
	}

	private function posted_key( string $field ): string {
		return isset( $_POST[ $field ] ) && is_string( $_POST[ $field ] ) ? sanitize_key( wp_unslash( $_POST[ $field ] ) ) : '';
	}

	private function validation_errors( array $role, string $original_id ): array {
		$errors = $this->sanitizer->missing_required_fields( $role );
		if ( '' === $original_id && '' !== $role['id'] && $this->is_taken( $role['id'] ) ) {
			$errors[] = 'duplicate_id';
		}
		if ( '' !== $original_id && ! $this->roles->exists( $original_id ) ) {
			$errors[] = 'missing_role';
		}
		return $errors;
	}

	private function is_taken( string $role_id ): bool {
		return $this->roles->exists( $role_id ) || $this->roles->is_bundled( $role_id );
	}

	private function editor_arguments( string $original_id ): array {
		return '' === $original_id ? array() : array( 'role' => $original_id );
	}
}
