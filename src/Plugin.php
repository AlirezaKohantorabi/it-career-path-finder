<?php

namespace ItCareerPathFinder;

use ItCareerPathFinder\Admin\AboutPage;
use ItCareerPathFinder\Admin\AdminAssets;
use ItCareerPathFinder\Admin\AdminMenu;
use ItCareerPathFinder\Admin\RoleActions;
use ItCareerPathFinder\Admin\RoleEditorPage;
use ItCareerPathFinder\Admin\RolesPage;
use ItCareerPathFinder\Admin\TransferActions;
use ItCareerPathFinder\Admin\TransferPage;
use ItCareerPathFinder\Data\BundledData;
use ItCareerPathFinder\Data\RoleChanges;
use ItCareerPathFinder\Data\RoleImporter;
use ItCareerPathFinder\Data\RoleRepository;
use ItCareerPathFinder\Data\RoleSanitizer;
use ItCareerPathFinder\Data\RoleSchema;
use ItCareerPathFinder\Frontend\Assets;
use ItCareerPathFinder\Frontend\DatasetPrinter;
use ItCareerPathFinder\Frontend\FrontendDataset;
use ItCareerPathFinder\Frontend\Shortcode;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	public const TEXT_DOMAIN = 'it-career-path-finder';

	public static function boot(): void {
		add_action( 'plugins_loaded', array( new self(), 'register' ) );
	}

	public function register(): void {
		add_action( 'init', array( $this, 'load_translations' ) );
		$bundled = new BundledData( $this->data_directory() );
		$roles   = new RoleRepository( $bundled, new RoleChanges() );
		$this->register_frontend( $bundled, $roles );
		if ( is_admin() ) {
			$this->register_admin( $bundled, $roles );
		}
	}

	public function load_translations(): void {
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( ITCP_PLUGIN_FILE ) ) . '/languages' );
	}

	private function data_directory(): string {
		return trailingslashit( (string) apply_filters( 'itcp_data_directory', ITCP_PLUGIN_DIR . 'data/' ) );
	}

	private function register_frontend( BundledData $bundled, RoleRepository $roles ): void {
		$assets  = new Assets();
		$printer = new DatasetPrinter( new FrontendDataset( $bundled, $roles ) );
		$assets->register();
		$printer->register();
		( new Shortcode( $assets, $printer, $bundled ) )->register();
	}

	private function register_admin( BundledData $bundled, RoleRepository $roles ): void {
		$schema    = new RoleSchema( $bundled->vocabulary() );
		$sanitizer = new RoleSanitizer( $schema );
		$menu      = new AdminMenu(
			new RolesPage( $roles, $schema ),
			new RoleEditorPage( $roles, $schema, $sanitizer ),
			new TransferPage(),
			new AboutPage()
		);
		$menu->register();
		( new AdminAssets() )->register();
		( new RoleActions( $roles, $sanitizer ) )->register();
		( new TransferActions( $roles, new RoleImporter( $sanitizer, $roles ) ) )->register();
	}
}
