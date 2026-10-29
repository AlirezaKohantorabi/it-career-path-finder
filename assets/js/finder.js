(function (ns) {
	'use strict';

	var AUTO_ADVANCE_WINDOW_MS = 1200;
	var AUTO_ADVANCE_DELAY_MS = 380;
	var TOAST_DURATION_MS = 2600;
	var CARD_HIGHLIGHT_MS = 1600;
	var PART_NAMES = ['shell', 'phase', 'time', 'restart', 'step', 'left', 'bar', 'fill', 'dots', 'form', 'question', 'previous', 'next', 'next-label', 'results', 'toast'];

	function camelCase(name) {
		return name.replace(/-(\w)/g, function (match, letter) {
			return letter.toUpperCase();
		});
	}

	function collectParts(root) {
		var parts = {};
		PART_NAMES.forEach(function (name) {
			parts[camelCase(name)] = root.querySelector('[data-itcp="' + name + '"]');
		});
		return parts;
	}

	function hasRequiredParts(root) {
		var parts = collectParts(root);
		return PART_NAMES.every(function (name) {
			return Boolean(parts[camelCase(name)]);
		});
	}

	function Finder(root, dataset, instanceNumber) {
		this.root = root;
		this.dataset = dataset;
		this.text = dataset.text;
		this.index = ns.Dataset.buildIndex(dataset);
		this.instanceNumber = instanceNumber;
		this.parts = collectParts(root);
		this.storageKey = ns.Storage.keyFor(instanceNumber);
		this.state = ns.Storage.restore(this.storageKey, this.index.questions) || ns.State.createState();
		this.advanceTimer = null;
		this.toastTimer = null;
		this.lastPointerAt = 0;
	}

	Finder.prototype.start = function () {
		this.bindControls();
		ns.ProgressView.renderTimeChip(this);
		if (this.state.showingResults) {
			this.showResults(false);
		} else {
			this.showQuestion(false);
		}
		this.root.setAttribute('data-itcp-ready', '1');
	};

	Finder.prototype.bindControls = function () {
		var finder = this;
		this.parts.previous.addEventListener('click', function () {
			finder.goPrevious();
		});
		this.parts.next.addEventListener('click', function () {
			finder.goNext();
		});
		this.parts.restart.addEventListener('click', function () {
			finder.restart();
		});
		this.parts.form.addEventListener('submit', function (event) {
			event.preventDefault();
		});
	};

	Finder.prototype.currentQuestion = function () {
		return this.index.questions[this.state.currentIndex];
	};

	Finder.prototype.isLastQuestion = function () {
		return this.state.currentIndex === this.index.questions.length - 1;
	};

	Finder.prototype.scoringContext = function () {
		return {
			index: this.index,
			text: this.text,
			scoring: this.dataset.scoring,
			strictInsuranceFilter: this.state.strictInsuranceFilter
		};
	};

	Finder.prototype.persist = function () {
		ns.Storage.save(this.storageKey, this.state);
	};

	Finder.prototype.updateFooter = function () {
		var last = this.isLastQuestion();
		this.parts.previous.disabled = this.state.currentIndex === 0;
		this.parts.nextLabel.textContent = last ? this.text.app.show_results : this.text.app.next;
		this.parts.next.classList.toggle('is-final', last);
	};

	Finder.prototype.showQuestion = function (moveFocus) {
		this.parts.form.style.display = '';
		this.parts.results.classList.remove('is-on');
		ns.ProgressView.renderQuestionProgress(this);
		ns.ProgressView.renderDots(this);
		ns.QuestionView.render(this, moveFocus);
		this.updateFooter();
	};

	Finder.prototype.showResults = function (moveFocus) {
		this.cancelAutoAdvance();
		this.state.showingResults = true;
		this.parts.form.style.display = 'none';
		this.parts.results.classList.add('is-on');
		ns.ProgressView.renderCompletedProgress(this);
		ns.ProgressView.renderDots(this);
		ns.ResultsView.render(this, moveFocus);
	};

	Finder.prototype.moveTo = function (position) {
		this.state.currentIndex = position;
		this.state.maxReached = Math.max(this.state.maxReached, position);
		this.showQuestion(true);
		this.persist();
	};

	Finder.prototype.finishQuestionnaire = function () {
		var gap = ns.State.firstIncompleteIndex(this.index.questions, this.state.answers);
		if (gap !== -1) {
			this.state.currentIndex = gap;
			this.showQuestion(true);
			ns.QuestionView.showError(this, this.text.question.error_missing);
			return;
		}
		this.showResults(true);
		this.persist();
	};

	Finder.prototype.goNext = function () {
		this.cancelAutoAdvance();
		if (!ns.State.isComplete(this.currentQuestion(), this.state.answers)) {
			ns.QuestionView.showError(this, this.text.question.error_unanswered);
			return;
		}
		if (this.isLastQuestion()) {
			this.finishQuestionnaire();
			return;
		}
		this.moveTo(this.state.currentIndex + 1);
	};

	Finder.prototype.goPrevious = function () {
		this.cancelAutoAdvance();
		if (this.state.currentIndex === 0) {
			return;
		}
		this.state.currentIndex -= 1;
		this.showQuestion(true);
		this.persist();
	};

	Finder.prototype.goToQuestion = function (position) {
		if (position > this.state.maxReached) {
			return;
		}
		this.cancelAutoAdvance();
		this.state.showingResults = false;
		this.state.currentIndex = position;
		this.showQuestion(true);
		this.persist();
	};

	Finder.prototype.selectOption = function (question, value, checked) {
		if (ns.State.isMultiSelect(question)) {
			ns.State.setAnswer(this.state, question.id, ns.State.toggledSelection(this.state.answers[question.id], value, checked));
		} else {
			ns.State.setAnswer(this.state, question.id, value);
			this.advanceIfPointerSelected();
		}
		ns.QuestionView.clearError(this);
		ns.ProgressView.refreshDoneDots(this);
		this.persist();
	};

	Finder.prototype.reselectOption = function (question, value) {
		if (!ns.State.isMultiSelect(question) && this.state.answers[question.id] === value) {
			this.advanceIfPointerSelected();
		}
	};

	Finder.prototype.recordPointer = function () {
		this.lastPointerAt = Date.now();
	};

	Finder.prototype.advanceIfPointerSelected = function () {
		if (Date.now() - this.lastPointerAt < AUTO_ADVANCE_WINDOW_MS) {
			this.scheduleAutoAdvance();
		}
	};

	Finder.prototype.scheduleAutoAdvance = function () {
		if (this.isLastQuestion()) {
			return;
		}
		var finder = this;
		var position = this.state.currentIndex;
		this.cancelAutoAdvance();
		this.advanceTimer = window.setTimeout(function () {
			if (finder.state.currentIndex === position && !finder.state.showingResults) {
				finder.goNext();
			}
		}, AUTO_ADVANCE_DELAY_MS);
	};

	Finder.prototype.cancelAutoAdvance = function () {
		window.clearTimeout(this.advanceTimer);
		this.advanceTimer = null;
	};

	Finder.prototype.restart = function () {
		if (ns.State.hasAnyAnswers(this.state) && window.confirm && !window.confirm(this.text.app.restart_confirm)) {
			return;
		}
		this.cancelAutoAdvance();
		ns.State.resetState(this.state);
		ns.Storage.clear(this.storageKey);
		ns.Dom.clearChildren(this.parts.results);
		this.showQuestion(true);
	};

	Finder.prototype.editAnswers = function () {
		this.state.showingResults = false;
		this.state.currentIndex = 0;
		this.showQuestion(true);
		this.persist();
	};

	Finder.prototype.applyTweak = function (change) {
		if (change.toggleStrictInsurance) {
			this.state.strictInsuranceFilter = !this.state.strictInsuranceFilter;
		} else {
			this.state.answers[change.answer] = change.value;
		}
		this.persist();
		this.showResults(true);
		this.showToast(this.text.results.tweak_updated);
	};

	Finder.prototype.revealRoleCard = function (role) {
		var card = document.getElementById(ns.RoleCard.cardElementId(this, role));
		if (!card) {
			return;
		}
		ns.Scroll.scrollToElement(card, this.root, false);
		card.classList.add('is-focus');
		window.setTimeout(function () {
			card.classList.remove('is-focus');
		}, CARD_HIGHLIGHT_MS);
	};

	Finder.prototype.showToast = function (message) {
		var toast = this.parts.toast;
		toast.textContent = message;
		toast.classList.add('is-on');
		window.clearTimeout(this.toastTimer);
		this.toastTimer = window.setTimeout(function () {
			toast.classList.remove('is-on');
		}, TOAST_DURATION_MS);
	};

	Finder.hasRequiredParts = hasRequiredParts;

	ns.Finder = Finder;
})(window.ItcpFinder = window.ItcpFinder || {});
