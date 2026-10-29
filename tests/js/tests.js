(function (ns) {
	'use strict';

	var DATA_FILES = ['questionnaire', 'vocabulary', 'scoring', 'interface-text', 'roles'];
	var STORAGE_TEST_KEY = 'itcp_state_v3_test';
	var results = [];

	function check(name, condition, detail) {
		results.push({ name: name, passed: Boolean(condition), detail: detail || '' });
	}

	function loadJson(name) {
		return fetch('../../data/' + name + '.json', { cache: 'no-store' }).then(function (response) {
			return response.json();
		});
	}

	function buildDataset(files) {
		return {
			sections: files[0].sections,
			questions: files[0].questions,
			summary_questions: files[0].summary_questions,
			categories: files[1].categories,
			interest_tags: files[1].interest_tags,
			workplace_types: files[1].workplace_types,
			industry_aliases: files[1].industry_aliases,
			scoring: files[2],
			text: files[3],
			roles: files[4].roles
		};
	}

	function contextFor(dataset, strictInsurance) {
		return {
			index: ns.Dataset.buildIndex(dataset),
			text: dataset.text,
			scoring: dataset.scoring,
			strictInsuranceFilter: Boolean(strictInsurance)
		};
	}

	function findRole(dataset, roleId) {
		return dataset.roles.filter(function (role) {
			return role.id === roleId;
		})[0];
	}

	function isCleanText(text) {
		return typeof text === 'string' && text.length > 0 && text.indexOf('undefined') === -1 && text.indexOf('{') === -1;
	}

	function completeAnswers(dataset) {
		var answers = {};
		dataset.questions.forEach(function (question) {
			var first = question.options[0].value;
			answers[question.id] = question.type === 'multi_select' ? [first] : first;
		});
		return answers;
	}

	function testFormat() {
		check('toPersianDigits converts every digit', ns.Format.toPersianDigits('2026') === '۲۰۲۶');
		check('fillTemplate keeps unknown placeholders', ns.Format.fillTemplate('{a} {b}', { a: 1 }) === '1 {b}');
		check('normalizeRating maps 1..5 to 0..1 and missing to the middle', ns.Format.normalizeRating(1) === 0 && ns.Format.normalizeRating(5) === 1 && ns.Format.normalizeRating(undefined) === 0.5);
	}

	function testState() {
		var State = ns.State;
		check('choosing no_preference clears other choices', JSON.stringify(State.toggledSelection(['a', 'b'], 'no_preference', true)) === '["no_preference"]');
		check('choosing a value removes no_preference', JSON.stringify(State.toggledSelection(['no_preference'], 'a', true)) === '["a"]');
		check('unchecking a value removes only that value', JSON.stringify(State.toggledSelection(['a', 'b'], 'a', false)) === '["b"]');
	}

	function testStorage(dataset) {
		window.sessionStorage.setItem(STORAGE_TEST_KEY, JSON.stringify({
			answers: { primary_goal: 'constructor', client_type: 'individual', interests: ['coding', '__proto__', 'nope'] },
			currentIndex: 99,
			maxReached: 99,
			showingResults: true
		}));
		var state = ns.Storage.restore(STORAGE_TEST_KEY, dataset.questions);
		window.sessionStorage.removeItem(STORAGE_TEST_KEY);
		check('stored answers that are not real options are dropped', state.answers.primary_goal === undefined && JSON.stringify(state.answers.interests) === '["coding"]' && state.answers.client_type === 'individual');
		check('stored position cannot jump past the first unanswered question', state.currentIndex === 0 && state.maxReached === 0);
		check('stored results are not reopened for incomplete answers', state.showingResults === false);
	}

	function testScoring(dataset) {
		var context = contextFor(dataset, false);
		var bounds = dataset.scoring.score_bounds;
		var remoteOnly = { work_mode: ['remote'], interests: ['hardware'] };
		var technician = ns.Scoring.scoreRole(findRole(dataset, 'data_center_technician'), remoteOnly, context);
		var frontend = ns.Scoring.scoreRole(findRole(dataset, 'frontend_developer'), remoteOnly, context);
		check('remote-only answer penalises an on-site job', technician.penalty === dataset.scoring.penalties.remote_only_mismatch, 'penalty ' + technician.penalty);
		check('remote-friendly job gets no remote penalty', frontend.penalty === 0, 'penalty ' + frontend.penalty);
		var everyResult = dataset.roles.map(function (role) {
			return ns.Scoring.scoreRole(role, completeAnswers(dataset), context);
		});
		check('every score stays inside score_bounds', everyResult.every(function (result) {
			return result.score >= bounds.min && result.score <= bounds.max;
		}));
		check('every result has 1-3 reasons and 1-2 risks', everyResult.every(function (result) {
			return result.reasons.length >= 1 && result.reasons.length <= 3 && result.risks.length >= 1 && result.risks.length <= 2;
		}));
		check('every reason, risk and compromise is filled text', everyResult.every(function (result) {
			return result.reasons.concat(result.risks, result.compromises).every(isCleanText);
		}));
	}

	function testRanking(dataset) {
		var tough = { primary_goal: 'remote', client_type: 'individual', work_mode: ['remote'], insurance_requirement: 'mandatory', on_call_tolerance: 'never', current_level: 'complete_beginner', time_to_entry: 'under_3_months', interests: ['security'] };
		var ranking = ns.Ranking.rankRoles(dataset.roles, tough, contextFor(dataset, false));
		var primaryIds = ranking.primary.map(function (result) {
			return result.role.id;
		});
		check('at least 3 jobs are always recommended', ranking.primary.length >= dataset.scoring.grouping.primary_min_count);
		check('recommended and compromise groups never overlap', ranking.compromise.every(function (result) {
			return primaryIds.indexOf(result.role.id) === -1;
		}));
		check('the full ranking is sorted by score', ranking.all.every(function (result, position) {
			return position === 0 || ranking.all[position - 1].score >= result.score;
		}));
		var strict = ns.Ranking.rankRoles(dataset.roles, { insurance_requirement: 'mandatory' }, contextFor(dataset, true));
		check('strict insurance filter removes unlikely-insurance jobs', strict.all.every(function (result) {
			return result.role.ratings_1_to_5.insurance_likelihood_in_employee_roles >= 3;
		}));
	}

	function testConflicts(dataset) {
		var messages = dataset.text.messages;
		var found = ns.Conflicts.listConflicts({ work_arrangement: ['freelance'], insurance_requirement: 'mandatory' }, messages);
		check('freelance plus mandatory insurance is reported once', found.length === 1 && found[0] === messages.conflict_freelance_insurance);
		check('no answers produce no conflicts', ns.Conflicts.listConflicts({}, messages).length === 0);
	}

	function testPlan(dataset) {
		var plan = ns.Plan.buildPlan(findRole(dataset, 'frontend_developer'), dataset.text.plan);
		check('plan has four filled weekly steps and an experiment', plan.steps.length === 4 && plan.steps.every(isCleanText) && isCleanText(plan.experiment));
	}

	function testRenderedResults(dataset) {
		var root = document.querySelector('[data-itcp-app-fixture]');
		window.sessionStorage.removeItem(ns.Storage.keyFor(9));
		var finder = new ns.Finder(root, dataset, 9);
		finder.start();
		finder.state.answers = completeAnswers(dataset);
		finder.showResults(false);
		var text = finder.parts.results.textContent;
		var firstCard = finder.parts.results.querySelector('.itcp-role');
		check('rendered results contain no "undefined"', text.indexOf('undefined') === -1);
		check('rendered results contain no unfilled {placeholders}', !/\{\w+\}/.test(text));
		check('the first card explains the job in plain words first', firstCard && firstCard.children[1].classList.contains('is-plain'));
		finder.showQuestion(false);
		check('question view renders options', root.querySelectorAll('.itcp-opt').length === dataset.questions[0].options.length);
		window.sessionStorage.removeItem(ns.Storage.keyFor(9));
	}

	function report() {
		var failed = results.filter(function (result) {
			return !result.passed;
		});
		var list = document.getElementById('results');
		results.forEach(function (result) {
			var item = document.createElement('li');
			item.className = result.passed ? 'pass' : 'fail';
			item.textContent = (result.passed ? 'PASS ' : 'FAIL ') + result.name + (result.detail ? ' (' + result.detail + ')' : '');
			list.appendChild(item);
		});
		var summary = (failed.length ? 'FAIL' : 'PASS') + ' — ' + (results.length - failed.length) + '/' + results.length;
		document.getElementById('summary').textContent = summary;
		document.title = summary;
		window.ItcpTestResults = results;
	}

	function runAll(dataset) {
		[testFormat, testState, testStorage, testScoring, testRanking, testConflicts, testPlan, testRenderedResults].forEach(function (test) {
			try {
				test(dataset);
			} catch (error) {
				check(test.name + ' crashed', false, error.message);
			}
		});
		report();
	}

	Promise.all(DATA_FILES.map(loadJson)).then(function (files) {
		runAll(buildDataset(files));
	}, function (error) {
		check('data files load', false, error.message);
		report();
	});
})(window.ItcpFinder = window.ItcpFinder || {});
