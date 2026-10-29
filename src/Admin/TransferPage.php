<?php

namespace ItCareerPathFinder\Admin;

use ItCareerPathFinder\Support\View;

defined( 'ABSPATH' ) || exit;

final class TransferPage {

	public const SLUG = 'itcp-transfer';

	public function render(): void {
		if ( ! RequestGuard::current_user_can_manage() ) {
			return;
		}
		View::display( 'admin/transfer' );
	}
}
