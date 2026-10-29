<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class RoleSanitizer {

	public const RATING_MIN     = 1;
	public const RATING_MAX     = 5;
	public const RATING_NEUTRAL = 3;

	private const ID_MAX_LENGTH       = 64;
	private const LINE_MAX_LENGTH     = 200;
	private const PARAGRAPH_MAX_LENGTH = 2000;
	private const LIST_ITEM_MAX_LENGTH = 120;
	private const LIST_MAX_ITEMS      = 20;
	private const REQUIRED_FIELDS     = array( 'id', 'title_fa', 'category_id', 'plain_description' );

	private RoleSchema $schema;

	public function __construct( RoleSchema $schema ) {
		$this->schema = $schema;
	}

	public function sanitize( array $input ): array {
		return array(
			'id'                      => $this->clean_id( $input['id'] ?? '' ),
			'title_fa'                => $this->clean_line( $input['title_fa'] ?? '' ),
			'title_en'                => $this->clean_line( $input['title_en'] ?? '' ),
			'category_id'             => $this->clean_choice( $input['category_id'] ?? '', $this->schema->category_ids() ),
			'plain_description'       => $this->clean_paragraph( $input['plain_description'] ?? '' ),
			'ratings_1_to_5'          => $this->clean_ratings( $input['ratings_1_to_5'] ?? array() ),
			'interest_tags'           => $this->clean_choices( $input['interest_tags'] ?? array(), $this->schema->interest_tag_values() ),
			'typical_workplaces'      => $this->clean_choices( $input['typical_workplaces'] ?? array(), $this->schema->workplace_values() ),
			'essential_skills'        => $this->clean_lines( $input['essential_skills'] ?? array() ),
			'recommended_entry_route' => $this->clean_paragraph( $input['recommended_entry_route'] ?? '' ),
			'market_caveat'           => $this->clean_paragraph( $input['market_caveat'] ?? '' ),
			'labels'                  => $this->clean_choices( $input['labels'] ?? array(), $this->schema->role_label_values() ),
		);
	}

	public function missing_required_fields( array $role ): array {
		return array_values(
			array_filter(
				self::REQUIRED_FIELDS,
				static function ( string $field ) use ( $role ): bool {
					return '' === ( $role[ $field ] ?? '' );
				}
			)
		);
	}

	private function clean_id( $value ): string {
		return substr( sanitize_key( $this->as_string( $value ) ), 0, self::ID_MAX_LENGTH );
	}

	private function clean_line( $value ): string {
		return $this->limit( sanitize_text_field( $this->as_string( $value ) ), self::LINE_MAX_LENGTH );
	}

	private function clean_paragraph( $value ): string {
		return $this->limit( sanitize_textarea_field( $this->as_string( $value ) ), self::PARAGRAPH_MAX_LENGTH );
	}

	private function clean_choice( $value, array $allowed ): string {
		$choice = $this->as_string( $value );
		return in_array( $choice, $allowed, true ) ? $choice : '';
	}

	private function clean_choices( $values, array $allowed ): array {
		$choices = array();
		foreach ( $this->as_list( $values ) as $value ) {
			$choice = $this->clean_choice( $value, $allowed );
			if ( '' !== $choice && ! in_array( $choice, $choices, true ) ) {
				$choices[] = $choice;
			}
		}
		return $choices;
	}

	private function clean_lines( $values ): array {
		$lines = is_string( $values ) ? preg_split( '/\r\n|\r|\n/', $values ) : $this->as_list( $values );
		$clean = array();
		foreach ( (array) $lines as $line ) {
			$text = $this->limit( sanitize_text_field( $this->as_string( $line ) ), self::LIST_ITEM_MAX_LENGTH );
			if ( '' !== $text ) {
				$clean[] = $text;
			}
		}
		return array_slice( $clean, 0, self::LIST_MAX_ITEMS );
	}

	private function clean_ratings( $input ): array {
		$source  = $this->as_map( $input );
		$ratings = array();
		foreach ( $this->schema->rating_keys() as $key ) {
			$ratings[ $key ] = $this->clean_rating( $source[ $key ] ?? null );
		}
		foreach ( $this->schema->fit_groups() as $group ) {
			$group_key             = (string) ( $group['key'] ?? '' );
			$ratings[ $group_key ] = $this->clean_fit_group( $source[ $group_key ] ?? array(), $this->schema->fit_group_keys( $group ) );
		}
		return $ratings;
	}

	private function clean_fit_group( $input, array $keys ): array {
		$source = $this->as_map( $input );
		$fit    = array();
		foreach ( $keys as $key ) {
			$fit[ $key ] = $this->clean_rating( $source[ $key ] ?? null );
		}
		return $fit;
	}

	private function clean_rating( $value ): int {
		$rating = is_numeric( $value ) ? (int) round( (float) $value ) : self::RATING_NEUTRAL;
		return max( self::RATING_MIN, min( self::RATING_MAX, $rating ) );
	}

	private function as_string( $value ): string {
		return is_scalar( $value ) ? trim( (string) $value ) : '';
	}

	private function as_list( $value ): array {
		return is_array( $value ) ? array_values( $value ) : array();
	}

	private function as_map( $value ): array {
		return is_array( $value ) ? $value : array();
	}

	private function limit( string $text, int $max_length ): string {
		return mb_substr( $text, 0, $max_length );
	}
}
