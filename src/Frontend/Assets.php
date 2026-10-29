<?php

namespace ItCareerPathFinder\Frontend;

defined( 'ABSPATH' ) || exit;

final class Assets {

	public const STYLE_HANDLE = 'itcp-app';

	private const SCRIPT_PREFIX = 'itcp-';

	private const SCRIPT_MODULES = array(
		'format',
		'dom',
		'scroll',
		'dataset',
		'state',
		'storage',
		'assessment',
		'scoring',
		'ranking',
		'conflicts',
		'plan',
		'progress-view',
		'question-view',
		'role-card',
		'result-actions',
		'results-view',
		'finder',
		'boot',
	);

	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ), 5 );
	}

	public function register_assets(): void {
		wp_register_style( self::STYLE_HANDLE, ITCP_PLUGIN_URL . 'assets/css/app.css', array(), ITCP_VERSION );
		$this->register_scripts();
	}

	public function enqueue(): void {
		if ( ! wp_style_is( self::STYLE_HANDLE, 'registered' ) ) {
			$this->register_assets();
		}
		wp_enqueue_style( self::STYLE_HANDLE );
		wp_enqueue_script( $this->entry_script_handle() );
	}

	private function register_scripts(): void {
		$dependencies = array();
		foreach ( self::SCRIPT_MODULES as $module ) {
			$handle = self::SCRIPT_PREFIX . $module;
			wp_register_script( $handle, ITCP_PLUGIN_URL . 'assets/js/' . $module . '.js', $dependencies, ITCP_VERSION, true );
			$dependencies = array( $handle );
		}
	}

	private function entry_script_handle(): string {
		return self::SCRIPT_PREFIX . self::SCRIPT_MODULES[ count( self::SCRIPT_MODULES ) - 1 ];
	}
}
