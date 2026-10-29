(function (ns) {
	'use strict';

	var BEGINNER_LEVELS = ['complete_beginner', 'learning', 'career_switcher'];
	var FRESH_START_LEVELS = ['complete_beginner', 'career_switcher'];
	var ENTERPRISE_INTERESTS = ['systems', 'security', 'data'];
	var HIGH_INCOME_TARGETS = ['very_high', 'above_market'];
	var LIMITED_ENGLISH = ['basic', 'reading'];

	function chose(answers, questionId, value) {
		var answer = answers[questionId];
		return Array.isArray(answer) ? answer.indexOf(value) !== -1 : answer === value;
	}

	function choseAny(answers, questionId, values) {
		return values.some(function (value) {
			return chose(answers, questionId, value);
		});
	}

	function freelanceWithMandatoryInsurance(answers) {
		return choseAny(answers, 'work_arrangement', ['freelance', 'self_employed']) && answers.insurance_requirement === 'mandatory';
	}

	function individualClientWithEnterpriseInterests(answers) {
		return answers.client_type === 'individual' && choseAny(answers, 'interests', ENTERPRISE_INTERESTS);
	}

	function freshStartWithHighIncome(answers) {
		return answers.time_to_entry === 'under_3_months' &&
			FRESH_START_LEVELS.indexOf(answers.current_level) !== -1 &&
			HIGH_INCOME_TARGETS.indexOf(answers.income_target) !== -1;
	}

	function noOnCallWithSystemsInterest(answers) {
		return answers.on_call_tolerance === 'never' && chose(answers, 'interests', 'systems');
	}

	function beginnerWithoutPortfolio(answers) {
		return answers.must_have_portfolio === 'no' && BEGINNER_LEVELS.indexOf(answers.current_level) !== -1;
	}

	function internationalRemoteWithLimitedEnglish(answers) {
		return answers.location_scope === 'international_remote_interest' && LIMITED_ENGLISH.indexOf(answers.english_level) !== -1;
	}

	var CHECKS = [
		{ message: 'conflict_freelance_insurance', applies: freelanceWithMandatoryInsurance },
		{ message: 'conflict_individual_enterprise', applies: individualClientWithEnterpriseInterests },
		{ message: 'conflict_fast_high_income', applies: freshStartWithHighIncome },
		{ message: 'conflict_no_on_call_systems', applies: noOnCallWithSystemsInterest },
		{ message: 'conflict_no_portfolio', applies: beginnerWithoutPortfolio },
		{ message: 'conflict_international_english', applies: internationalRemoteWithLimitedEnglish }
	];

	function listConflicts(answers, messages) {
		return CHECKS
			.filter(function (check) {
				return check.applies(answers);
			})
			.map(function (check) {
				return messages[check.message];
			});
	}

	ns.Conflicts = {
		listConflicts: listConflicts
	};
})(window.ItcpFinder = window.ItcpFinder || {});
