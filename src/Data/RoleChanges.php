<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class RoleChanges {

	public const OPTION_NAME = 'itcp_role_changes';

	public function saved_roles(): array {
		return $this->read()['saved'];
	}

	public function deleted_ids(): array {
		return $this->read()['deleted'];
	}

	public function save_role( array $role ): void {
		$changes                         = $this->read();
		$changes['saved'][ $role['id'] ] = $role;
		$changes['deleted']              = $this->without( $changes['deleted'], $role['id'] );
		$this->write( $changes );
	}

	public function forget_role( string $role_id ): void {
		$changes = $this->read();
		unset( $changes['saved'][ $role_id ] );
		$this->write( $changes );
	}

	public function mark_deleted( string $role_id ): void {
		$changes = $this->read();
		unset( $changes['saved'][ $role_id ] );
		$changes['deleted']   = $this->without( $changes['deleted'], $role_id );
		$changes['deleted'][] = $role_id;
		$this->write( $changes );
	}

	public function unmark_deleted( string $role_id ): void {
		$changes            = $this->read();
		$changes['deleted'] = $this->without( $changes['deleted'], $role_id );
		$this->write( $changes );
	}

	public function replace_all( array $saved_roles, array $deleted_ids ): void {
		$this->write(
			array(
				'saved'   => $saved_roles,
				'deleted' => array_values( array_unique( $deleted_ids ) ),
			)
		);
	}

	public function clear(): void {
		delete_option( self::OPTION_NAME );
	}

	private function read(): array {
		$stored  = get_option( self::OPTION_NAME, array() );
		$saved   = is_array( $stored ) && isset( $stored['saved'] ) && is_array( $stored['saved'] ) ? $stored['saved'] : array();
		$deleted = is_array( $stored ) && isset( $stored['deleted'] ) && is_array( $stored['deleted'] ) ? $stored['deleted'] : array();
		return array(
			'saved'   => $saved,
			'deleted' => array_values( array_filter( $deleted, 'is_string' ) ),
		);
	}

	private function write( array $changes ): void {
		update_option( self::OPTION_NAME, $changes, false );
	}

	private function without( array $role_ids, string $role_id ): array {
		return array_values( array_diff( $role_ids, array( $role_id ) ) );
	}
}
