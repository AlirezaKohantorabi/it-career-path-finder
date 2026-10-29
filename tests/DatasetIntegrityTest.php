<?php

namespace ItCareerPathFinder\Tests;

use ItCareerPathFinder\Tests\Support\TestCase;

final class DatasetIntegrityTest extends TestCase {

	private const DATA_FILES      = array( 'questionnaire.json', 'vocabulary.json', 'scoring.json', 'interface-text.json', 'roles.json' );
	private const ROLE_ID_PATTERN = '/^[a-z0-9_\-]{1,64}$/';
	private const REQUIRED_TEXT   = array( 'id', 'title_fa', 'category_id', 'plain_description' );
	private const TEXT_ALIASES    = array( 'planText' => 'plan' );

	private function data( string $file ): array {
		return json_decode( (string) file_get_contents( ITCP_TESTS_ROOT . 'data/' . $file ), true );
	}

	private function roles(): array {
		return $this->data( 'roles.json' )['roles'];
	}

	private function script( string $name ): string {
		return (string) file_get_contents( ITCP_TESTS_ROOT . 'assets/js/' . $name . '.js' );
	}

	private function all_scripts(): array {
		$scripts = array();
		foreach ( glob( ITCP_TESTS_ROOT . 'assets/js/*.js' ) as $file ) {
			$scripts[ basename( $file ) ] = (string) file_get_contents( $file );
		}
		return $scripts;
	}

	private function matches( string $pattern, string $subject ): array {
		preg_match_all( $pattern, $subject, $found );
		return array_values( array_unique( $found[1] ) );
	}

	private function quoted_words( string $code ): array {
		return $this->matches( "/'(\\w+)'/", $code );
	}

	public function test_every_data_file_is_valid_json(): void {
		foreach ( self::DATA_FILES as $file ) {
			json_decode( (string) file_get_contents( ITCP_TESTS_ROOT . 'data/' . $file ), true );
			$this->assert_same( JSON_ERROR_NONE, json_last_error(), $file . ' is not valid JSON:' );
		}
	}

	public function test_role_ids_are_unique_and_safe(): void {
		$ids = array_column( $this->roles(), 'id' );
		$this->assert_same( count( $ids ), count( array_unique( $ids ) ), 'Duplicate role ids.' );
		$this->assert_empty( preg_grep( self::ROLE_ID_PATTERN, $ids, PREG_GREP_INVERT ), 'Unsafe role ids:' );
	}

	public function test_every_role_has_required_text(): void {
		$problems = array();
		foreach ( $this->roles() as $role ) {
			foreach ( self::REQUIRED_TEXT as $field ) {
				if ( '' === trim( (string) ( $role[ $field ] ?? '' ) ) ) {
					$problems[] = ( $role['id'] ?? '?' ) . ' has no ' . $field;
				}
			}
		}
		$this->assert_empty( $problems, 'Roles missing required text:' );
	}

	public function test_every_role_rating_is_complete_and_between_one_and_five(): void {
		$fields   = $this->data( 'vocabulary.json' )['role_fields'];
		$problems = array();
		foreach ( $this->roles() as $role ) {
			$ratings = $role['ratings_1_to_5'];
			foreach ( array_column( $fields['ratings'], 'key' ) as $key ) {
				$problems = array_merge( $problems, $this->rating_problems( $role['id'] . '.' . $key, $ratings[ $key ] ?? null ) );
			}
			foreach ( $fields['fit_groups'] as $group ) {
				foreach ( array_column( $group['options'], 'key' ) as $key ) {
					$problems = array_merge( $problems, $this->rating_problems( $role['id'] . '.' . $group['key'] . '.' . $key, $ratings[ $group['key'] ][ $key ] ?? null ) );
				}
			}
		}
		$this->assert_empty( $problems, 'Bad ratings:' );
	}

	private function rating_problems( string $path, $value ): array {
		return is_int( $value ) && $value >= 1 && $value <= 5 ? array() : array( $path . ' = ' . var_export( $value, true ) );
	}

	public function test_role_references_exist_in_vocabulary(): void {
		$vocabulary = $this->data( 'vocabulary.json' );
		$known      = array(
			'category_id'        => array_column( $vocabulary['categories'], 'id' ),
			'interest_tags'      => array_column( $vocabulary['interest_tags'], 'value' ),
			'typical_workplaces' => array_column( $vocabulary['workplace_types'], 'value' ),
			'labels'             => array_column( $vocabulary['role_labels'], 'value' ),
		);
		$problems   = array();
		foreach ( $this->roles() as $role ) {
			foreach ( $known as $field => $allowed ) {
				foreach ( array_diff( (array) $role[ $field ], $allowed ) as $unknown ) {
					$problems[] = $role['id'] . '.' . $field . ' has unknown value ' . $unknown;
				}
			}
		}
		$this->assert_empty( $problems, 'Unknown references:' );
	}

	public function test_every_question_has_a_known_section_and_unique_options(): void {
		$questionnaire = $this->data( 'questionnaire.json' );
		$sections      = array_column( $questionnaire['sections'], 'id' );
		$problems      = array();
		foreach ( $questionnaire['questions'] as $question ) {
			$values = array_column( $question['options'], 'value' );
			if ( ! in_array( $question['section'], $sections, true ) ) {
				$problems[] = $question['id'] . ' has unknown section ' . $question['section'];
			}
			if ( array() === $values || count( $values ) !== count( array_unique( $values ) ) ) {
				$problems[] = $question['id'] . ' has no options or duplicate option values';
			}
			if ( ! in_array( $question['type'], array( 'single_select', 'multi_select' ), true ) ) {
				$problems[] = $question['id'] . ' has unknown type ' . $question['type'];
			}
		}
		$question_ids = array_column( $questionnaire['questions'], 'id' );
		$this->assert_same( count( $question_ids ), count( array_unique( $question_ids ) ), 'Duplicate question ids.' );
		$this->assert_empty( $problems, 'Question problems:' );
	}

	public function test_summary_questions_exist(): void {
		$questionnaire = $this->data( 'questionnaire.json' );
		$this->assert_empty( array_diff( $questionnaire['summary_questions'], array_column( $questionnaire['questions'], 'id' ) ), 'Unknown summary questions:' );
	}

	public function test_industry_aliases_use_known_options_and_workplaces(): void {
		$vocabulary = $this->data( 'vocabulary.json' );
		$questions  = array_column( $this->data( 'questionnaire.json' )['questions'], null, 'id' );
		$options    = array_column( $questions['industry_preferences']['options'], 'value' );
		$workplaces = array_column( $vocabulary['workplace_types'], 'value' );
		$problems   = array();
		foreach ( $vocabulary['industry_aliases'] as $industry => $aliases ) {
			if ( ! in_array( $industry, $options, true ) ) {
				$problems[] = 'Alias key ' . $industry . ' is not an industry option';
			}
			foreach ( array_diff( $aliases, $workplaces ) as $unknown ) {
				$problems[] = $industry . ' points to unknown workplace ' . $unknown;
			}
		}
		$this->assert_empty( $problems, 'Alias problems:' );
	}

	public function test_weights_cover_every_scoring_factor(): void {
		$factors = $this->matches( '/factors\.(\w+)\s*=/', $this->script( 'scoring' ) );
		$weights = array_keys( $this->data( 'scoring.json' )['weights'] );
		sort( $factors );
		sort( $weights );
		$this->assert_same( $factors, $weights, 'scoring.json weights must match the factors in scoring.js.' );
	}

	public function test_penalty_keys_used_in_scoring_exist(): void {
		$calls     = $this->matches( '/addPenalty\(([^;]*)\);/', $this->script( 'scoring' ) );
		$used      = $this->quoted_words( implode( ' ', $calls ) );
		$penalties = array_keys( $this->data( 'scoring.json' )['penalties'] );
		$this->assert_empty( array_diff( $used, $penalties ), 'Penalties missing from scoring.json:' );
	}

	public function test_message_keys_used_in_scripts_exist(): void {
		$code     = implode( "\n", $this->all_scripts() );
		$used     = array_merge(
			$this->matches( "/add(?:Reason|Risk|Gap|Compromise)\\(\\s*'(\\w+)'/", $code ),
			$this->matches( "/message:\\s*'(\\w+)'/", $code ),
			$this->matches( "/this\\.message\\('(\\w+)'/", $code )
		);
		$messages = array_keys( $this->data( 'interface-text.json' )['messages'] );
		$this->assert_empty( array_diff( $used, $messages ), 'Messages missing from interface-text.json:' );
	}

	public function test_interface_text_keys_used_in_scripts_exist(): void {
		$text    = $this->data( 'interface-text.json' );
		$missing = array();
		foreach ( $this->all_scripts() as $file => $code ) {
			foreach ( $this->text_references( $code ) as $reference ) {
				if ( ! isset( $text[ $reference[0] ][ $reference[1] ] ) ) {
					$missing[] = $file . ': ' . $reference[0] . '.' . $reference[1];
				}
			}
		}
		$this->assert_empty( array_values( array_unique( $missing ) ), 'Interface text missing:' );
	}

	private function text_references( string $code ): array {
		preg_match_all( '/text\.(\w+)\.(\w+)/', $code, $direct, PREG_SET_ORDER );
		$references = array_map(
			static function ( array $match ): array {
				return array( $match[1], $match[2] );
			},
			$direct
		);
		foreach ( $this->text_aliases( $code ) as $alias => $group ) {
			foreach ( $this->matches( '/\b' . $alias . '\.(\w+)/', $code ) as $key ) {
				$references[] = array( $group, $key );
			}
		}
		return array_values( array_filter( $references, array( $this, 'is_text_group_reference' ) ) );
	}

	private function text_aliases( string $code ): array {
		preg_match_all( '/var (\w+) = (?:finder|this|assessment\.context)\.text\.(\w+);/', $code, $found, PREG_SET_ORDER );
		$aliases = self::TEXT_ALIASES;
		foreach ( $found as $match ) {
			$aliases[ $match[1] ] = $match[2];
		}
		return $aliases;
	}

	private function is_text_group_reference( array $reference ): bool {
		return in_array( $reference[0], array( 'app', 'question', 'results', 'card', 'plan', 'separators', 'messages' ), true )
			&& ! in_array( $reference[1], array( 'length', 'push' ), true );
	}
}
