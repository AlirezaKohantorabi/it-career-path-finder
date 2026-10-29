<?php

namespace ItCareerPathFinder\Admin;

use ItCareerPathFinder\Data\RoleRepository;
use ItCareerPathFinder\Data\RoleSchema;
use ItCareerPathFinder\Frontend\Shortcode;
use ItCareerPathFinder\Support\View;

defined( 'ABSPATH' ) || exit;

final class RolesPage {

	public const SLUG = 'itcp-roles';

	private RoleRepository $roles;

	private RoleSchema $schema;

	public function __construct( RoleRepository $roles, RoleSchema $schema ) {
		$this->roles  = $roles;
		$this->schema = $schema;
	}

	public static function url(): string {
		return add_query_arg( array( 'page' => self::SLUG ), admin_url( 'admin.php' ) );
	}

	public function render(): void {
		if ( ! RequestGuard::current_user_can_manage() ) {
			return;
		}
		View::display(
			'admin/roles-list',
			array(
				'rows'         => array_map( array( $this, 'row_for' ), $this->roles->all() ),
				'deleted_rows' => array_map( array( $this, 'deleted_row_for' ), $this->roles->deleted_defaults() ),
				'add_url'      => RoleEditorPage::url(),
				'shortcode'    => Shortcode::TAG,
			)
		);
	}

	public function row_for( array $role ): array {
		$role_id = (string) ( $role['id'] ?? '' );
		return array(
			'id'       => $role_id,
			'title_fa' => (string) ( $role['title_fa'] ?? '' ),
			'title_en' => (string) ( $role['title_en'] ?? '' ),
			'category' => $this->schema->category_title( (string) ( $role['category_id'] ?? '' ) ),
			'origin'   => $this->roles->origin_of( $role_id ),
			'edit_url' => RoleEditorPage::url( $role_id ),
		);
	}

	public function deleted_row_for( array $role ): array {
		return array(
			'id'       => (string) ( $role['id'] ?? '' ),
			'title_fa' => (string) ( $role['title_fa'] ?? '' ),
		);
	}
}
