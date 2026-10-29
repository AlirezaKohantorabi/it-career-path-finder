<?php

namespace ItCareerPathFinder\Frontend;

defined( 'ABSPATH' ) || exit;

final class DatasetPrinter {

	public const ELEMENT_ID = 'itcp-dataset';

	private const JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE;

	private FrontendDataset $dataset;

	private bool $requested = false;

	private bool $printed = false;

	public function __construct( FrontendDataset $dataset ) {
		$this->dataset = $dataset;
	}

	public function register(): void {
		add_action( 'wp_footer', array( $this, 'print_if_requested' ), 5 );
	}

	public function request(): string {
		if ( did_action( 'wp_footer' ) ) {
			return $this->take_markup();
		}
		$this->requested = true;
		return '';
	}

	public function print_if_requested(): void {
		if ( $this->requested ) {
			echo $this->take_markup();
		}
	}

	private function take_markup(): string {
		if ( $this->printed ) {
			return '';
		}
		$this->printed = true;
		return $this->markup();
	}

	private function markup(): string {
		return sprintf(
			'<script type="application/json" id="%1$s">%2$s</script>',
			esc_attr( self::ELEMENT_ID ),
			$this->encoded_dataset()
		);
	}

	private function encoded_dataset(): string {
		$json = wp_json_encode( $this->dataset->build(), self::JSON_FLAGS );
		return false === $json ? '{}' : $json;
	}
}
