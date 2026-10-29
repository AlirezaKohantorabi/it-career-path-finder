<?php

namespace ItCareerPathFinder\Data;

defined( 'ABSPATH' ) || exit;

final class BundledData {

	public const QUESTIONNAIRE_FILE = 'questionnaire.json';
	public const VOCABULARY_FILE    = 'vocabulary.json';
	public const SCORING_FILE       = 'scoring.json';
	public const INTERFACE_FILE     = 'interface-text.json';
	public const ROLES_FILE         = 'roles.json';

	private string $directory;

	private array $cache = array();

	public function __construct( string $directory ) {
		$this->directory = $directory;
	}

	public function questionnaire(): array {
		return $this->read( self::QUESTIONNAIRE_FILE );
	}

	public function vocabulary(): array {
		return $this->read( self::VOCABULARY_FILE );
	}

	public function scoring(): array {
		return $this->read( self::SCORING_FILE );
	}

	public function interface_text(): array {
		return $this->read( self::INTERFACE_FILE );
	}

	public function roles(): array {
		$document = $this->read( self::ROLES_FILE );
		return isset( $document['roles'] ) && is_array( $document['roles'] ) ? $document['roles'] : array();
	}

	private function read( string $file_name ): array {
		if ( ! isset( $this->cache[ $file_name ] ) ) {
			$this->cache[ $file_name ] = JsonFile::decode_file( $this->directory . $file_name );
		}
		return $this->cache[ $file_name ];
	}
}
