(function (ns) {
	'use strict';

	var F = ns.Format;
	var SECONDS_SINGLE = 8;
	var SECONDS_MULTI = 13;
	var SECONDS_LONG_MULTI = 16;
	var LONG_OPTION_COUNT = 8;
	var SHORT_REMAINING_SECONDS = 45;
	var MIN_FILL_PERCENT = 4;

	function secondsFor(question) {
		if (!ns.State.isMultiSelect(question)) {
			return SECONDS_SINGLE;
		}
		return F.listOrEmpty(question.options).length > LONG_OPTION_COUNT ? SECONDS_LONG_MULTI : SECONDS_MULTI;
	}

	function secondsFrom(questions, startIndex) {
		return questions.slice(startIndex).reduce(function (total, question) {
			return total + secondsFor(question);
		}, 0);
	}

	function wholeMinutes(seconds) {
		return Math.max(1, Math.round(seconds / 60));
	}

	function timeLeftText(finder, startIndex) {
		var seconds = secondsFrom(finder.index.questions, startIndex);
		if (seconds < SHORT_REMAINING_SECONDS) {
			return finder.text.app.time_left_short;
		}
		return F.fillTemplate(finder.text.app.time_left, { minutes: F.toPersianDigits(wholeMinutes(seconds)) });
	}

	function renderTimeChip(finder) {
		var questions = finder.index.questions;
		finder.parts.time.textContent = F.fillTemplate(finder.text.app.time_chip, {
			count: F.toPersianDigits(questions.length),
			minutes: F.toPersianDigits(wholeMinutes(secondsFrom(questions, 0)))
		});
	}

	function updateProgressBar(finder, current, total, valueText) {
		finder.parts.bar.setAttribute('aria-valuemax', String(total));
		finder.parts.bar.setAttribute('aria-valuenow', String(current));
		finder.parts.bar.setAttribute('aria-valuetext', valueText);
	}

	function renderQuestionProgress(finder) {
		var app = finder.text.app;
		var number = finder.state.currentIndex + 1;
		var total = finder.index.questions.length;
		var section = ns.Dataset.sectionOf(finder.index, finder.currentQuestion());
		finder.parts.phase.textContent = F.fillTemplate(app.section_label, {
			current: F.toPersianDigits(section.number),
			total: F.toPersianDigits(section.total),
			title: section.title
		});
		finder.parts.step.textContent = F.fillTemplate(app.step_label, { current: F.toPersianDigits(number), total: F.toPersianDigits(total) });
		finder.parts.left.textContent = timeLeftText(finder, finder.state.currentIndex);
		finder.parts.fill.style.width = Math.max(MIN_FILL_PERCENT, Math.round(((number - 1) / total) * 100)) + '%';
		updateProgressBar(finder, number, total, finder.parts.step.textContent);
	}

	function renderCompletedProgress(finder) {
		var app = finder.text.app;
		var total = finder.index.questions.length;
		finder.parts.phase.textContent = app.results_phase;
		finder.parts.step.textContent = F.fillTemplate(app.results_step, { total: F.toPersianDigits(total) });
		finder.parts.left.textContent = app.results_left;
		finder.parts.fill.style.width = '100%';
		updateProgressBar(finder, total, total, app.progress_done);
	}

	function buildDot(finder, question, position) {
		var number = F.toPersianDigits(position + 1);
		var dot = ns.Dom.createElement('button', 'itcp-dot', number);
		dot.type = 'button';
		dot.title = question.title;
		dot.setAttribute('aria-label', F.fillTemplate(finder.text.app.dot_label, { number: number, title: question.title }));
		if (ns.State.hasAnswer(question, finder.state.answers)) {
			dot.classList.add('is-done');
		}
		if (position > finder.state.maxReached) {
			dot.setAttribute('aria-disabled', 'true');
		}
		dot.addEventListener('click', function () {
			finder.goToQuestion(position);
		});
		return dot;
	}

	function markCurrentDot(finder, dot) {
		dot.classList.add('is-cur');
		dot.setAttribute('aria-current', 'step');
		ns.Scroll.centerHorizontally(finder.parts.dots, dot);
	}

	function renderDots(finder) {
		var dots = finder.parts.dots;
		ns.Dom.clearChildren(dots);
		finder.index.questions.forEach(function (question, position) {
			var dot = buildDot(finder, question, position);
			dots.appendChild(dot);
			if (position === finder.state.currentIndex && !finder.state.showingResults) {
				markCurrentDot(finder, dot);
			}
		});
	}

	function refreshDoneDots(finder) {
		var dots = finder.parts.dots.children;
		finder.index.questions.forEach(function (question, position) {
			if (dots[position]) {
				dots[position].classList.toggle('is-done', ns.State.hasAnswer(question, finder.state.answers));
			}
		});
	}

	ns.ProgressView = {
		timeLeftText: timeLeftText,
		renderTimeChip: renderTimeChip,
		renderQuestionProgress: renderQuestionProgress,
		renderCompletedProgress: renderCompletedProgress,
		renderDots: renderDots,
		refreshDoneDots: refreshDoneDots
	};
})(window.ItcpFinder = window.ItcpFinder || {});
