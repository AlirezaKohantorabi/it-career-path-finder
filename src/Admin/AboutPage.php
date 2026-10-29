<?php

namespace ItCareerPathFinder\Admin;

use ItCareerPathFinder\Frontend\Shortcode;
use ItCareerPathFinder\Support\View;

defined( 'ABSPATH' ) || exit;

final class AboutPage {

	public const SLUG = 'itcp-about';

	public const REPOSITORY_URL = 'https://github.com/AlirezaKohantorabi/it-career-path-finder';

	public function render(): void {
		if ( ! RequestGuard::current_user_can_manage() ) {
			return;
		}
		View::display(
			'admin/about',
			array(
				'shortcode'  => Shortcode::TAG,
				'version'    => ITCP_VERSION,
				'repository' => self::REPOSITORY_URL,
				'developers' => $this->developers(),
			)
		);
	}

	private function developers(): array {
		return array(
			array(
				'name'    => __( 'خسرو عظیمی', 'it-career-path-finder' ),
				'website' => 'https://khosro-azimi.ir',
				'github'  => 'https://github.com/khosro-web',
			),
			array(
				'name'    => __( 'علیرضا کهن‌ترابی', 'it-career-path-finder' ),
				'website' => '',
				'github'  => 'https://github.com/AlirezaKohantorabi',
			),
		);
	}
}
