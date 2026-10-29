<?php

namespace ItCareerPathFinder\Admin;

use ItCareerPathFinder\Data\RoleImporter;
use ItCareerPathFinder\Data\RoleRepository;

defined( 'ABSPATH' ) || exit;

final class TransferActions {

	public const EXPORT_ACTION = 'itcp_export_roles';
	public const IMPORT_ACTION = 'itcp_import_roles';
	public const RESET_ACTION  = 'itcp_reset_roles';

	private const MAX_JSON_BYTES   = 2097152;
	private const FILE_FIELD       = 'itcp_file';
	private const TEXT_FIELD       = 'itcp_json';
	private const MODE_FIELD       = 'itcp_mode';
	private const EXPORT_FLAGS     = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
	private const SCHEMA_VERSION   = '2.0.0';

	private RoleRepository $roles;

	private RoleImporter $importer;

	public function __construct( RoleRepository $roles, RoleImporter $importer ) {
		$this->roles    = $roles;
		$this->importer = $importer;
	}

	public function register(): void {
		add_action( 'admin_post_' . self::EXPORT_ACTION, array( $this, 'export' ) );
		add_action( 'admin_post_' . self::IMPORT_ACTION, array( $this, 'import' ) );
		add_action( 'admin_post_' . self::RESET_ACTION, array( $this, 'reset' ) );
	}

	public function export(): void {
		RequestGuard::verify_action( self::EXPORT_ACTION );
		$this->send_download_headers();
		echo wp_json_encode(
			array(
				'schema_version' => self::SCHEMA_VERSION,
				'roles'          => $this->roles->all(),
			),
			self::EXPORT_FLAGS
		);
		exit;
	}

	public function import(): void {
		RequestGuard::verify_action( self::IMPORT_ACTION );
		$json   = $this->submitted_json();
		$result = '' === $json ? null : $this->importer->import( $json, $this->submitted_mode() );
		if ( null === $result ) {
			Notices::redirect( TransferPage::SLUG, 'import_failed' );
		}
		Notices::redirect( TransferPage::SLUG, 'imported', $result );
	}

	public function reset(): void {
		RequestGuard::verify_action( self::RESET_ACTION );
		$this->roles->reset();
		Notices::redirect( RolesPage::SLUG, 'reset' );
	}

	private function send_download_headers(): void {
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="it-career-roles-' . gmdate( 'Y-m-d' ) . '.json"' );
		header( 'X-Content-Type-Options: nosniff' );
	}

	private function submitted_json(): string {
		$uploaded = $this->uploaded_file_contents();
		return '' !== $uploaded ? $uploaded : $this->pasted_json();
	}

	private function uploaded_file_contents(): string {
		$file = isset( $_FILES[ self::FILE_FIELD ] ) && is_array( $_FILES[ self::FILE_FIELD ] ) ? $_FILES[ self::FILE_FIELD ] : array();
		if ( ! $this->is_acceptable_upload( $file ) ) {
			return '';
		}
		$contents = file_get_contents( (string) $file['tmp_name'] );
		return false === $contents ? '' : $contents;
	}

	private function is_acceptable_upload( array $file ): bool {
		$path = (string) ( $file['tmp_name'] ?? '' );
		$name = (string) ( $file['name'] ?? '' );
		return UPLOAD_ERR_OK === ( $file['error'] ?? UPLOAD_ERR_NO_FILE )
			&& is_uploaded_file( $path )
			&& filesize( $path ) <= self::MAX_JSON_BYTES
			&& 'json' === strtolower( pathinfo( $name, PATHINFO_EXTENSION ) );
	}

	private function pasted_json(): string {
		$text = isset( $_POST[ self::TEXT_FIELD ] ) && is_string( $_POST[ self::TEXT_FIELD ] ) ? wp_unslash( $_POST[ self::TEXT_FIELD ] ) : '';
		return strlen( $text ) > self::MAX_JSON_BYTES ? '' : $text;
	}

	private function submitted_mode(): string {
		$mode = isset( $_POST[ self::MODE_FIELD ] ) && is_string( $_POST[ self::MODE_FIELD ] ) ? sanitize_key( wp_unslash( $_POST[ self::MODE_FIELD ] ) ) : '';
		return RoleImporter::MODE_REPLACE === $mode ? RoleImporter::MODE_REPLACE : RoleImporter::MODE_MERGE;
	}
}
