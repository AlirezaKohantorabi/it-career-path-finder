<?php

namespace ItCareerPathFinder\Admin;

defined( 'ABSPATH' ) || exit;

final class RequestGuard {

	private const DEFAULT_CAPABILITY = 'manage_options';

	public static function capability(): string {
		return (string) apply_filters( 'itcp_manage_capability', self::DEFAULT_CAPABILITY );
	}

	public static function current_user_can_manage(): bool {
		return current_user_can( self::capability() );
	}

	public static function verify_action( string $nonce_action ): void {
		if ( ! self::current_user_can_manage() ) {
			wp_die(
				esc_html__( 'شما اجازه انجام این کار را ندارید.', 'it-career-path-finder' ),
				'',
				array( 'response' => 403 )
			);
		}
		check_admin_referer( $nonce_action );
	}
}
