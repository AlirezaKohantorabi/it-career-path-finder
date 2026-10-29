<?php

namespace ItCareerPathFinder\Frontend;

use ItCareerPathFinder\Data\BundledData;
use ItCareerPathFinder\Support\View;

defined( 'ABSPATH' ) || exit;

final class Shortcode {

	public const TAG = 'it_career_path_finder';

	private Assets $assets;

	private DatasetPrinter $printer;

	private BundledData $bundled;

	private int $instances = 0;

	public function __construct( Assets $assets, DatasetPrinter $printer, BundledData $bundled ) {
		$this->assets  = $assets;
		$this->printer = $printer;
		$this->bundled = $bundled;
	}

	public function register(): void {
		add_shortcode( self::TAG, array( $this, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_for_current_post' ), 20 );
	}

	public function render( $attributes = array() ): string {
		$this->assets->enqueue();
		++$this->instances;
		$markup = View::render(
			'shortcode-app',
			array(
				'instance' => $this->instances,
				'text'     => $this->app_text( $attributes ),
			)
		);
		return $this->without_whitespace_between_tags( $markup ) . $this->printer->request();
	}

	public function enqueue_for_current_post(): void {
		if ( $this->current_post_uses_shortcode() ) {
			$this->assets->enqueue();
		}
	}

	private function current_post_uses_shortcode(): bool {
		$post = get_post();
		return is_singular() && $post instanceof \WP_Post && has_shortcode( $post->post_content, self::TAG );
	}

	private function app_text( $attributes ): array {
		$text   = $this->bundled->interface_text()['app'] ?? array();
		$chosen = shortcode_atts(
			array(
				'title'    => $text['title'] ?? '',
				'subtitle' => $text['subtitle'] ?? '',
			),
			is_array( $attributes ) ? $attributes : array(),
			self::TAG
		);
		$text['title']    = sanitize_text_field( (string) $chosen['title'] );
		$text['subtitle'] = sanitize_text_field( (string) $chosen['subtitle'] );
		return $text;
	}

	private function without_whitespace_between_tags( string $markup ): string {
		return trim( (string) preg_replace( '/>\s+</', '><', $markup ) );
	}
}
