(function (ns) {
	'use strict';

	var NO_PREFERENCE = 'no_preference';

	function createState() {
		return {
			currentIndex: 0,
			maxReached: 0,
			answers: {},
			strictInsuranceFilter: false,
			showingResults: false
		};
	}

	function isMultiSelect(question) {
		return question.type === 'multi_select';
	}

	function hasAnswer(question, answers) {
		var answer = answers[question.id];
		return Array.isArray(answer) ? answer.length > 0 : Boolean(answer);
	}

	function isComplete(question, answers) {
		return !question.required || hasAnswer(question, answers);
	}

	function firstIncompleteIndex(questions, answers) {
		for (var position = 0; position < questions.length; position++) {
			if (!isComplete(questions[position], answers)) {
				return position;
			}
		}
		return -1;
	}

	function toggledSelection(current, value, checked) {
		if (value === NO_PREFERENCE && checked) {
			return [NO_PREFERENCE];
		}
		var selection = ns.Format.listOrEmpty(current).filter(function (item) {
			return item !== NO_PREFERENCE && item !== value;
		});
		if (checked) {
			selection.push(value);
		}
		return selection;
	}

	function setAnswer(state, questionId, value) {
		if (Array.isArray(value) && value.length === 0) {
			delete state.answers[questionId];
			return;
		}
		state.answers[questionId] = value;
	}

	function hasAnyAnswers(state) {
		return Object.keys(state.answers).length > 0;
	}

	function resetState(state) {
		var fresh = createState();
		Object.keys(fresh).forEach(function (key) {
			state[key] = fresh[key];
		});
	}

	ns.State = {
		createState: createState,
		isMultiSelect: isMultiSelect,
		hasAnswer: hasAnswer,
		isComplete: isComplete,
		firstIncompleteIndex: firstIncompleteIndex,
		toggledSelection: toggledSelection,
		setAnswer: setAnswer,
		hasAnyAnswers: hasAnyAnswers,
		resetState: resetState
	};
})(window.ItcpFinder = window.ItcpFinder || {});
