<?php

namespace ItCareerPathFinder\Admin;

defined( 'ABSPATH' ) || exit;

final class AdminAssets {

	private const HANDLE = 'itcp-admin';

	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue(): void {
		if ( ! $this->is_plugin_page() ) {
			return;
		}
		wp_enqueue_style( self::HANDLE, ITCP_PLUGIN_URL . 'assets/css/admin.css', array(), ITCP_VERSION );
		wp_enqueue_script( self::HANDLE, ITCP_PLUGIN_URL . 'assets/js/admin.js', array(), ITCP_VERSION, true );
	}

	private function is_plugin_page(): bool {
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return in_array( $page, $this->plugin_pages(), true );
	}

	private function plugin_pages(): array {
		return array( RolesPage::SLUG, RoleEditorPage::SLUG, TransferPage::SLUG, AboutPage::SLUG );
	}
}
