<?php

namespace ItCareerPathFinder\Admin;

use ItCareerPathFinder\Data\RoleRepository;
use ItCareerPathFinder\Data\RoleSanitizer;
use ItCareerPathFinder\Data\RoleSchema;
use ItCareerPathFinder\Support\View;

defined( 'ABSPATH' ) || exit;

final class RoleEditorPage {

	public const SLUG = 'itcp-role-edit';

	private RoleRepository $roles;

	private RoleSchema $schema;

	private RoleSanitizer $sanitizer;

	public function __construct( RoleRepository $roles, RoleSchema $schema, RoleSanitizer $sanitizer ) {
		$this->roles     = $roles;
		$this->schema    = $schema;
		$this->sanitizer = $sanitizer;
	}

	public static function url( string $role_id = '' ): string {
		$arguments = array( 'page' => self::SLUG );
		if ( '' !== $role_id ) {
			$arguments['role'] = $role_id;
		}
		return add_query_arg( $arguments, admin_url( 'admin.php' ) );
	}

	public function render(): void {
		if ( ! RequestGuard::current_user_can_manage() ) {
			return;
		}
		$requested_id = $this->requested_role_id();
		$draft        = Drafts::take();
		$stored_role  = '' === $requested_id ? null : $this->roles->find( $requested_id );
		View::display(
			'admin/role-editor',
			array(
				'role'     => $draft['role'] ?? $stored_role ?? $this->sanitizer->sanitize( array() ),
				'errors'   => $draft['errors'] ?? array(),
				'is_new'   => '' === $requested_id,
				'missing'  => '' !== $requested_id && null === $stored_role,
				'schema'   => $this->schema,
				'list_url' => RolesPage::url(),
			)
		);
	}

	private function requested_role_id(): string {
		return isset( $_GET['role'] ) ? sanitize_key( wp_unslash( $_GET['role'] ) ) : '';
	}
}
