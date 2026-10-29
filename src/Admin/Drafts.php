<?php

namespace ItCareerPathFinder\Admin;

defined( 'ABSPATH' ) || exit;

final class Drafts {

	private const KEY_PREFIX       = 'itcp_draft_';
	private const LIFETIME_SECONDS = 600;

	public static function keep( array $role, array $errors ): void {
		set_transient(
			self::key(),
			array(
				'role'   => $role,
				'errors' => $errors,
			),
			self::LIFETIME_SECONDS
		);
	}

	public static function take(): ?array {
		$draft = get_transient( self::key() );
		delete_transient( self::key() );
		return is_array( $draft ) ? $draft : null;
	}

	private static function key(): string {
		return self::KEY_PREFIX . get_current_user_id();
	}
}
