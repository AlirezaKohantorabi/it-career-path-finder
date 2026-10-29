(function (ns) {
	'use strict';

	var F = ns.Format;
	var Dom = ns.Dom;
	var CARD = ns.RoleCard;
	var GROUP_CLASSES = { primary: 'is-p', compromise: 'is-c' };

	function buildSummaryChips(finder) {
		var chips = Dom.createElement('div', 'itcp-chips');
		finder.index.summaryQuestions.forEach(function (questionId) {
			var answer = finder.state.answers[questionId];
			if (typeof answer === 'string' && answer) {
				chips.appendChild(Dom.createElement('span', 'itcp-chip', ns.Dataset.optionLabel(finder.index, questionId, answer)));
			}
		});
		return chips;
	}

	function buildJumpButton(finder, result, position) {
		var texts = finder.text.results;
		var score = F.toPersianDigits(result.score);
		var button = Dom.createElement('button', 'itcp-jump');
		button.type = 'button';
		button.appendChild(Dom.createElement('span', 'itcp-jump-n', F.toPersianDigits(position + 1)));
		button.appendChild(Dom.createElement('span', 'itcp-jump-t', result.role.title_fa));
		button.appendChild(Dom.createElement('span', 'itcp-jump-s', F.fillTemplate(finder.text.card.score_value, { score: score })));
		button.setAttribute('aria-label', F.fillTemplate(texts.jump_label, { title: result.role.title_fa, score: score }));
		button.addEventListener('click', function () {
			finder.revealRoleCard(result.role);
		});
		return button;
	}

	function buildSummary(finder, ranking) {
		var texts = finder.text.results;
		var summary = Dom.createElement('div', 'itcp-sum');
		var title = Dom.createElement('h3', 'itcp-sum-t');
		title.setAttribute('tabindex', '-1');
		title.setAttribute('data-itcp', 'results-title');
		title.appendChild(Dom.createIcon('check'));
		title.appendChild(Dom.createElement('span', '', texts.summary_title));
		summary.appendChild(title);
		summary.appendChild(Dom.createElement('p', '', F.fillTemplate(texts.summary_intro, {
			roles: F.toPersianDigits(finder.dataset.roles.length),
			questions: F.toPersianDigits(finder.index.questions.length)
		})));
		summary.appendChild(Dom.createElement('p', 'itcp-sum-note', texts.general_note));
		summary.appendChild(buildSummaryChips(finder));
		var jumps = Dom.createElement('div', 'itcp-top');
		ranking.primary.forEach(function (result, position) {
			jumps.appendChild(buildJumpButton(finder, result, position));
		});
		summary.appendChild(jumps);
		return summary;
	}

	function conflictMessages(finder, ranking) {
		var messages = ns.Conflicts.listConflicts(finder.state.answers, finder.text.messages);
		if (ranking.promoted) {
			messages.push(finder.text.results.promoted_note);
		}
		return messages;
	}

	function buildConflicts(finder, messages) {
		var box = Dom.createElement('div', 'itcp-box-warn');
		var heading = Dom.createElement('h4', '');
		heading.appendChild(Dom.createIcon('warn'));
		heading.appendChild(Dom.createElement('span', '', finder.text.results.conflicts_title));
		box.appendChild(heading);
		box.appendChild(Dom.createList(messages, '', false));
		return box;
	}

	function buildTweakButton(finder, label, iconName, change, pressed) {
		var button = Dom.createButton('itcp-tweak' + (pressed ? ' is-on' : ''), iconName, label);
		if (pressed !== undefined) {
			button.setAttribute('aria-pressed', pressed ? 'true' : 'false');
		}
		button.addEventListener('click', function () {
			finder.applyTweak(change);
		});
		return button;
	}

	function availableTweaks(finder) {
		var texts = finder.text.results;
		var answers = finder.state.answers;
		var strict = finder.state.strictInsuranceFilter;
		var tweaks = [];
		if (answers.time_to_entry === 'under_3_months') {
			tweaks.push({ label: texts.tweak_more_time, change: { answer: 'time_to_entry', value: '3_to_6_months' } });
		}
		if (answers.client_type === 'individual') {
			tweaks.push({ label: texts.tweak_all_clients, change: { answer: 'client_type', value: 'no_preference' } });
		}
		if (answers.on_call_tolerance === 'never') {
			tweaks.push({ label: texts.tweak_rare_on_call, change: { answer: 'on_call_tolerance', value: 'rare' } });
		}
		if (answers.insurance_requirement === 'mandatory') {
			tweaks.push({ label: strict ? texts.tweak_strict_insurance_on : texts.tweak_strict_insurance_off, change: { toggleStrictInsurance: true }, pressed: strict });
		}
		return tweaks;
	}

	function buildTweaks(finder) {
		var panel = Dom.createElement('div', 'itcp-tweaks');
		panel.appendChild(Dom.createElement('p', 'itcp-tweaks-t', finder.text.results.tweaks_title));
		var row = Dom.createElement('div', 'itcp-tweak-row');
		availableTweaks(finder).forEach(function (tweak) {
			row.appendChild(buildTweakButton(finder, tweak.label, tweak.pressed ? 'check' : 'tune', tweak.change, tweak.pressed));
		});
		var edit = Dom.createButton('itcp-tweak', 'edit', finder.text.results.tweak_edit_answers);
		edit.addEventListener('click', function () {
			finder.editAnswers();
		});
		row.appendChild(edit);
		panel.appendChild(row);
		return panel;
	}

	function buildSectionHeading(modifier, title, intro) {
		var section = Dom.createElement('div', 'itcp-sec' + (modifier ? ' ' + modifier : ''));
		var heading = Dom.createElement('h3', '');
		heading.appendChild(Dom.createElement('i', ''));
		heading.appendChild(Dom.createElement('span', '', title));
		section.appendChild(heading);
		section.appendChild(Dom.createElement('p', '', intro));
		return section;
	}

	function appendCardGroup(finder, container, results, kind, numbered) {
		results.forEach(function (result, position) {
			var openDetails = numbered && position === 0;
			container.appendChild(CARD.buildCard(finder, result, kind, numbered ? position + 1 : 0, openDetails));
		});
	}

	function appendCardSections(finder, container, ranking) {
		var texts = finder.text.results;
		container.appendChild(buildSectionHeading('', texts.primary_title, texts.primary_intro));
		appendCardGroup(finder, container, ranking.primary, CARD.KIND_PRIMARY, true);
		if (ranking.compromise.length > 0) {
			container.appendChild(buildSectionHeading('is-c', texts.compromise_title, texts.compromise_intro));
			appendCardGroup(finder, container, ranking.compromise, CARD.KIND_COMPROMISE, false);
		}
		if (ranking.low.length > 0) {
			container.appendChild(buildSectionHeading('is-l', texts.low_title, texts.low_intro));
			appendCardGroup(finder, container, ranking.low, CARD.KIND_LOW, false);
		}
	}

	function buildRankRow(finder, result, position, ranking) {
		var group = ranking.groupOf[result.role.id];
		var row = Dom.createElement('div', 'itcp-rk' + (group ? ' ' + GROUP_CLASSES[group] : ''));
		row.appendChild(Dom.createElement('span', 'itcp-rk-n', F.toPersianDigits(position + 1)));
		var title = Dom.createElement('span', 'itcp-rk-t');
		title.appendChild(Dom.createElement('b', '', result.role.title_fa));
		var bar = Dom.createElement('span', 'itcp-rk-bar');
		var fill = Dom.createElement('i', '');
		fill.style.width = result.score + '%';
		bar.appendChild(fill);
		title.appendChild(bar);
		row.appendChild(title);
		row.appendChild(Dom.createElement('span', 'itcp-rk-s', F.fillTemplate(finder.text.card.score_value, { score: F.toPersianDigits(result.score) })));
		return row;
	}

	function buildFullRanking(finder, ranking) {
		var details = Dom.createElement('details', 'itcp-more itcp-rank itcp-noprint');
		var summary = Dom.createElement('summary', '');
		summary.appendChild(Dom.createElement('span', '', F.fillTemplate(finder.text.results.ranking_title, { count: F.toPersianDigits(ranking.all.length) })));
		summary.appendChild(Dom.createIcon('chevron'));
		details.appendChild(summary);
		var list = Dom.createElement('div', 'itcp-rank-l');
		ranking.all.forEach(function (result, position) {
			list.appendChild(buildRankRow(finder, result, position, ranking));
		});
		details.appendChild(list);
		return details;
	}

	function focusResultsTitle(finder) {
		var title = finder.parts.results.querySelector('[data-itcp="results-title"]');
		F.safely(function () {
			title.focus({ preventScroll: true });
			return true;
		}, false);
		ns.Scroll.scrollToElement(finder.parts.shell, finder.root, false);
	}

	function render(finder, moveFocus) {
		var ranking = ns.Ranking.rankRoles(finder.dataset.roles, finder.state.answers, finder.scoringContext());
		var container = finder.parts.results;
		var conflicts = conflictMessages(finder, ranking);
		Dom.clearChildren(container);
		container.appendChild(buildSummary(finder, ranking));
		if (conflicts.length > 0) {
			container.appendChild(buildConflicts(finder, conflicts));
		}
		container.appendChild(buildTweaks(finder));
		appendCardSections(finder, container, ranking);
		container.appendChild(buildFullRanking(finder, ranking));
		container.appendChild(ns.ResultActions.buildActionBar(finder, ranking));
		if (moveFocus) {
			focusResultsTitle(finder);
		}
	}

	ns.ResultsView = {
		render: render
	};
})(window.ItcpFinder = window.ItcpFinder || {});
