<?php

namespace ItCareerPathFinder\Admin;

defined( 'ABSPATH' ) || exit;

final class AdminMenu {

	private const MENU_ICON     = 'dashicons-welcome-learn-more';
	private const MENU_POSITION = 58;

	private RolesPage $roles_page;

	private RoleEditorPage $editor_page;

	private TransferPage $transfer_page;

	private AboutPage $about_page;

	public function __construct( RolesPage $roles_page, RoleEditorPage $editor_page, TransferPage $transfer_page, AboutPage $about_page ) {
		$this->roles_page    = $roles_page;
		$this->editor_page   = $editor_page;
		$this->transfer_page = $transfer_page;
		$this->about_page    = $about_page;
	}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_pages' ) );
	}

	public function add_pages(): void {
		$capability = RequestGuard::capability();
		$menu_title = __( 'مسیر شغلی IT', 'it-career-path-finder' );
		add_menu_page( $menu_title, $menu_title, $capability, RolesPage::SLUG, array( $this->roles_page, 'render' ), self::MENU_ICON, self::MENU_POSITION );
		$this->add_subpage( RolesPage::SLUG, __( 'همه شغل‌ها', 'it-career-path-finder' ), array( $this->roles_page, 'render' ) );
		$this->add_subpage( RoleEditorPage::SLUG, __( 'افزودن شغل', 'it-career-path-finder' ), array( $this->editor_page, 'render' ) );
		$this->add_subpage( TransferPage::SLUG, __( 'درون‌ریزی و برون‌بری', 'it-career-path-finder' ), array( $this->transfer_page, 'render' ) );
		$this->add_subpage( AboutPage::SLUG, __( 'راهنما و سازندگان', 'it-career-path-finder' ), array( $this->about_page, 'render' ) );
	}

	private function add_subpage( string $slug, string $title, callable $callback ): void {
		add_submenu_page( RolesPage::SLUG, $title, $title, RequestGuard::capability(), $slug, $callback );
	}
}
