(function (ns) {
	'use strict';

	var F = ns.Format;
	var Dom = ns.Dom;

	var KIND_PRIMARY = 'primary';
	var KIND_COMPROMISE = 'compromise';
	var KIND_LOW = 'low';
	var KIND_CLASSES = { primary: 'is-p', compromise: 'is-c', low: 'is-l' };
	var RECHECK_SKILL_COUNT = 3;
	var METERS = [
		{ key: 'entry_difficulty', text: 'meter_entry' },
		{ key: 'income_ceiling', text: 'meter_income' },
		{ key: 'job_stability', text: 'meter_stability' },
		{ key: 'insurance_likelihood_in_employee_roles', text: 'meter_insurance' }
	];
	var METER_STEPS = 5;
	var NEUTRAL_RATING = 3;

	function cardElementId(finder, role) {
		return 'itcp-' + finder.instanceNumber + '-role-' + role.id;
	}

	function buildBox(modifier, iconName, title) {
		var box = Dom.createElement('div', 'itcp-box' + (modifier ? ' ' + modifier : ''));
		var heading = Dom.createElement('p', 'itcp-bt');
		if (iconName) {
			heading.appendChild(Dom.createIcon(iconName));
		}
		heading.appendChild(Dom.createElement('span', '', title));
		box.appendChild(heading);
		return box;
	}

	function buildTitleBlock(finder, role, number) {
		var block = Dom.createElement('div', 'itcp-role-ti');
		block.appendChild(Dom.createElement('h4', '', (number ? F.toPersianDigits(number) + '. ' : '') + role.title_fa));
		var subtitle = Dom.createElement('div', 'itcp-role-sub');
		var category = finder.index.categoryById[role.category_id];
		subtitle.appendChild(Dom.createElement('span', 'itcp-cat', category ? category.title : role.category_id));
		if (role.title_en) {
			var english = Dom.createElement('span', 'itcp-en', role.title_en);
			english.setAttribute('dir', 'ltr');
			english.setAttribute('lang', 'en');
			subtitle.appendChild(english);
		}
		block.appendChild(subtitle);
		return block;
	}

	function buildScoreRing(finder, score, kind) {
		var texts = finder.text.card;
		var ring = Dom.createElement('div', 'itcp-ring ' + KIND_CLASSES[kind]);
		ring.style.setProperty('--v', String(score));
		ring.setAttribute('role', 'img');
		ring.setAttribute('aria-label', F.fillTemplate(texts.score_aria, { score: F.toPersianDigits(score) }));
		var inner = Dom.createElement('div', 'itcp-ring-in');
		inner.appendChild(Dom.createElement('span', 'itcp-ring-n', F.fillTemplate(texts.score_value, { score: F.toPersianDigits(score) })));
		inner.appendChild(Dom.createElement('span', 'itcp-ring-l', texts.score_label));
		ring.appendChild(inner);
		return ring;
	}

	function buildHead(finder, result, kind, number) {
		var head = Dom.createElement('div', 'itcp-role-head');
		head.appendChild(buildTitleBlock(finder, result.role, number));
		head.appendChild(buildScoreRing(finder, result.score, kind));
		return head;
	}

	function buildPlainDescription(finder, role) {
		var box = buildBox('is-plain', 'bulb', finder.text.card.plain_title);
		box.appendChild(Dom.createElement('p', 'itcp-plain-tx', role.plain_description));
		return box;
	}

	function buildLowMatchExplanation(finder, result) {
		var texts = finder.text.card;
		var separators = finder.text.separators;
		var blockers = F.uniqueValues(result.risks.concat(result.compromises)).join(separators.pipe);
		var skills = F.listOrEmpty(result.role.essential_skills).slice(0, RECHECK_SKILL_COUNT).join(separators.comma);
		var box = buildBox('', 'info', texts.low_title);
		box.appendChild(Dom.createElement('p', '', F.fillTemplate(texts.low_blocker, { blockers: blockers || texts.low_blocker_default })));
		box.appendChild(Dom.createElement('p', '', F.fillTemplate(texts.low_recheck, { skills: skills })));
		return box;
	}

	function buildCompromiseBox(finder, result) {
		var box = buildBox('is-cm', 'tune', finder.text.card.compromise_title);
		var compromises = result.compromises.join(finder.text.separators.plus);
		box.appendChild(Dom.createElement('p', '', F.fillTemplate(finder.text.card.compromise_text, { compromises: compromises })));
		return box;
	}

	function needsCompromiseBox(result, kind) {
		return result.compromises.length > 0 && (kind === KIND_COMPROMISE || result.penalty > 0);
	}

	function buildReasonsAndRisks(finder, result) {
		var grid = Dom.createElement('div', 'itcp-grid');
		var reasons = buildBox('is-ok', 'check', finder.text.card.reasons_title);
		reasons.appendChild(Dom.createList(result.reasons, 'itcp-list', false));
		var risks = buildBox('is-wr', 'warn', finder.text.card.risks_title);
		risks.appendChild(Dom.createList(result.risks, 'itcp-list', false));
		grid.appendChild(reasons);
		grid.appendChild(risks);
		return grid;
	}

	function buildChips(values) {
		var chips = Dom.createElement('div', 'itcp-sk');
		values.forEach(function (value) {
			chips.appendChild(Dom.createElement('span', '', value));
		});
		return chips;
	}

	function buildSkillsBox(finder, role) {
		var box = buildBox('', 'flag', finder.text.card.skills_title);
		box.appendChild(buildChips(F.listOrEmpty(role.essential_skills)));
		box.appendChild(Dom.createElement('p', 'itcp-route', finder.text.card.route_prefix + (role.recommended_entry_route || '')));
		return box;
	}

	function buildMeter(finder, label, value) {
		var texts = finder.text.card;
		var meter = Dom.createElement('div', 'itcp-mt');
		meter.appendChild(Dom.createElement('span', '', label));
		var bar = Dom.createElement('span', 'itcp-mt-b');
		bar.setAttribute('role', 'img');
		bar.setAttribute('aria-label', F.fillTemplate(texts.meter_aria, { label: label, value: F.toPersianDigits(value) }));
		for (var step = 1; step <= METER_STEPS; step++) {
			bar.appendChild(Dom.createElement('i', step <= value ? 'on' : ''));
		}
		var reading = Dom.createElement('span', 'itcp-mt-r');
		reading.appendChild(bar);
		reading.appendChild(Dom.createElement('span', 'itcp-mt-v', F.fillTemplate(texts.meter_value, { value: F.toPersianDigits(value) })));
		meter.appendChild(reading);
		return meter;
	}

	function buildMeters(finder, role) {
		var ratings = role.ratings_1_to_5 || {};
		var meters = Dom.createElement('div', 'itcp-meters');
		METERS.forEach(function (definition) {
			meters.appendChild(buildMeter(finder, finder.text.card[definition.text], F.ratingOf(ratings, definition.key, NEUTRAL_RATING)));
		});
		return meters;
	}

	function buildWorkplacesBox(finder, role) {
		var labels = F.listOrEmpty(role.typical_workplaces).map(function (place) {
			return finder.index.workplaceLabels[place] || place;
		});
		var box = buildBox('', 'list', finder.text.card.workplaces_title);
		box.appendChild(buildChips(labels));
		box.appendChild(buildMeters(finder, role));
		box.appendChild(Dom.createElement('p', 'itcp-note', finder.text.card.meters_note));
		return box;
	}

	function buildPlanBox(finder, role) {
		var plan = ns.Plan.buildPlan(role, finder.text.plan);
		var box = buildBox('is-pl', 'up', finder.text.card.plan_title);
		box.appendChild(Dom.createList(plan.steps, 'itcp-steps', true));
		box.appendChild(Dom.createElement('p', 'itcp-exp', plan.experiment));
		return box;
	}

	function buildDetails(finder, role, openDetails) {
		var details = Dom.createElement('details', 'itcp-more');
		details.open = Boolean(openDetails);
		var summary = Dom.createElement('summary', '');
		summary.appendChild(Dom.createElement('span', '', finder.text.card.more_title));
		summary.appendChild(Dom.createIcon('chevron'));
		var inner = Dom.createElement('div', 'itcp-more-in');
		inner.appendChild(buildSkillsBox(finder, role));
		inner.appendChild(buildWorkplacesBox(finder, role));
		inner.appendChild(buildPlanBox(finder, role));
		details.appendChild(summary);
		details.appendChild(inner);
		return details;
	}

	function caveatText(finder, role) {
		var texts = finder.text.card;
		return role.market_caveat ? F.fillTemplate(texts.caveat, { caveat: role.market_caveat }) : texts.caveat_generic;
	}

	function buildCard(finder, result, kind, number, openDetails) {
		var role = result.role;
		var card = Dom.createElement('article', 'itcp-role ' + KIND_CLASSES[kind]);
		card.id = cardElementId(finder, role);
		card.appendChild(buildHead(finder, result, kind, number));
		if (role.plain_description) {
			card.appendChild(buildPlainDescription(finder, role));
		}
		if (kind === KIND_LOW) {
			card.appendChild(buildLowMatchExplanation(finder, result));
			return card;
		}
		if (needsCompromiseBox(result, kind)) {
			card.appendChild(buildCompromiseBox(finder, result));
		}
		card.appendChild(buildReasonsAndRisks(finder, result));
		card.appendChild(buildDetails(finder, role, openDetails));
		card.appendChild(Dom.createElement('div', 'itcp-cav', caveatText(finder, role)));
		return card;
	}

	ns.RoleCard = {
		KIND_PRIMARY: KIND_PRIMARY,
		KIND_COMPROMISE: KIND_COMPROMISE,
		KIND_LOW: KIND_LOW,
		cardElementId: cardElementId,
		buildCard: buildCard
	};
})(window.ItcpFinder = window.ItcpFinder || {});
