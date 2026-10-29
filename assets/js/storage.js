(function (ns) {
	'use strict';

	var KEY_PREFIX = 'itcp_state_v3_';

	function keyFor(instanceNumber) {
		return KEY_PREFIX + instanceNumber;
	}

	function snapshotOf(state) {
		return {
			answers: state.answers,
			currentIndex: state.currentIndex,
			maxReached: state.maxReached,
			strictInsuranceFilter: state.strictInsuranceFilter,
			showingResults: state.showingResults
		};
	}

	function save(key, state) {
		ns.Format.safely(function () {
			window.sessionStorage.setItem(key, JSON.stringify(snapshotOf(state)));
			return true;
		}, false);
	}

	function clear(key) {
		ns.Format.safely(function () {
			window.sessionStorage.removeItem(key);
			return true;
		}, false);
	}

	function readSnapshot(key) {
		return ns.Format.safely(function () {
			var snapshot = JSON.parse(window.sessionStorage.getItem(key) || 'null');
			return snapshot && typeof snapshot.answers === 'object' && snapshot.answers ? snapshot : null;
		}, null);
	}

	function allowedValues(question) {
		var allowed = Object.create(null);
		ns.Format.listOrEmpty(question.options).forEach(function (option) {
			allowed[option.value] = true;
		});
		return allowed;
	}

	function validAnswer(question, value) {
		var allowed = allowedValues(question);
		if (ns.State.isMultiSelect(question)) {
			var kept = ns.Format.listOrEmpty(value).filter(function (item) {
				return typeof item === 'string' && allowed[item] === true;
			});
			return kept.length ? ns.Format.uniqueValues(kept) : undefined;
		}
		return typeof value === 'string' && allowed[value] === true ? value : undefined;
	}

	function restoredAnswers(questions, storedAnswers) {
		var answers = {};
		questions.forEach(function (question) {
			var answer = validAnswer(question, storedAnswers[question.id]);
			if (answer !== undefined) {
				answers[question.id] = answer;
			}
		});
		return answers;
	}

	function wholeNumber(value) {
		var number = Math.floor(Number(value));
		return isFinite(number) ? number : 0;
	}

	function restore(key, questions) {
		var snapshot = readSnapshot(key);
		if (!snapshot) {
			return null;
		}
		var state = ns.State.createState();
		state.answers = restoredAnswers(questions, snapshot.answers);
		var firstGap = ns.State.firstIncompleteIndex(questions, state.answers);
		var reachable = firstGap === -1 ? questions.length - 1 : firstGap;
		state.maxReached = ns.Format.clamp(wholeNumber(snapshot.maxReached), 0, reachable);
		state.currentIndex = ns.Format.clamp(wholeNumber(snapshot.currentIndex), 0, state.maxReached);
		state.strictInsuranceFilter = snapshot.strictInsuranceFilter === true;
		state.showingResults = snapshot.showingResults === true && firstGap === -1;
		return state;
	}

	ns.Storage = {
		keyFor: keyFor,
		save: save,
		clear: clear,
		restore: restore
	};
})(window.ItcpFinder = window.ItcpFinder || {});
