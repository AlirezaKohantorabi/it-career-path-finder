<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class RoleSchema {

	private array $vocabulary;

	public function __construct( array $vocabulary ) {
		$this->vocabulary = $vocabulary;
	}

	public function categories(): array {
		return $this->list_of( 'categories' );
	}

	public function category_ids(): array {
		return $this->values_of( 'categories', 'id' );
	}

	public function category_title( string $category_id ): string {
		foreach ( $this->categories() as $category ) {
			if ( ( $category['id'] ?? '' ) === $category_id ) {
				return (string) ( $category['title'] ?? $category_id );
			}
		}
		return $category_id;
	}

	public function interest_tags(): array {
		return $this->list_of( 'interest_tags' );
	}

	public function interest_tag_values(): array {
		return $this->values_of( 'interest_tags', 'value' );
	}

	public function workplace_types(): array {
		return $this->list_of( 'workplace_types' );
	}

	public function workplace_values(): array {
		return $this->values_of( 'workplace_types', 'value' );
	}

	public function role_labels(): array {
		return $this->list_of( 'role_labels' );
	}

	public function role_label_values(): array {
		return $this->values_of( 'role_labels', 'value' );
	}

	public function rating_legend(): array {
		$legend = $this->vocabulary['rating_legend'] ?? array();
		return is_array( $legend ) ? $legend : array();
	}

	public function rating_fields(): array {
		$fields = $this->vocabulary['role_fields']['ratings'] ?? array();
		return is_array( $fields ) ? $fields : array();
	}

	public function rating_keys(): array {
		return $this->string_column( $this->rating_fields(), 'key' );
	}

	public function fit_groups(): array {
		$groups = $this->vocabulary['role_fields']['fit_groups'] ?? array();
		return is_array( $groups ) ? $groups : array();
	}

	public function fit_group_keys( array $group ): array {
		$options = $group['options'] ?? array();
		return $this->string_column( is_array( $options ) ? $options : array(), 'key' );
	}

	private function list_of( string $key ): array {
		$list = $this->vocabulary[ $key ] ?? array();
		return is_array( $list ) ? $list : array();
	}

	private function values_of( string $key, string $field ): array {
		return $this->string_column( $this->list_of( $key ), $field );
	}

	private function string_column( array $rows, string $field ): array {
		return array_values( array_filter( array_column( $rows, $field ), 'is_string' ) );
	}
}
