<?php

namespace ItCareerPathFinder\Frontend;

use ItCareerPathFinder\Data\BundledData;
use ItCareerPathFinder\Data\RoleRepository;

defined( 'ABSPATH' ) || exit;

final class FrontendDataset {

	private BundledData $bundled;

	private RoleRepository $roles;

	public function __construct( BundledData $bundled, RoleRepository $roles ) {
		$this->bundled = $bundled;
		$this->roles   = $roles;
	}

	public function build(): array {
		$questionnaire = $this->bundled->questionnaire();
		$vocabulary    = $this->bundled->vocabulary();
		$dataset       = array(
			'sections'          => $questionnaire['sections'] ?? array(),
			'questions'         => $questionnaire['questions'] ?? array(),
			'summary_questions' => $questionnaire['summary_questions'] ?? array(),
			'categories'        => $vocabulary['categories'] ?? array(),
			'interest_tags'     => $vocabulary['interest_tags'] ?? array(),
			'workplace_types'   => $vocabulary['workplace_types'] ?? array(),
			'industry_aliases'  => (object) ( $vocabulary['industry_aliases'] ?? array() ),
			'scoring'           => $this->bundled->scoring(),
			'text'              => $this->bundled->interface_text(),
			'roles'             => $this->roles->all(),
		);
		return (array) apply_filters( 'itcp_frontend_dataset', $dataset );
	}
}
