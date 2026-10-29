(function (ns) {
	'use strict';

	var WEEK_KEYS = ['week_1', 'week_2', 'week_3', 'week_4'];

	function withoutFinalPeriod(text) {
		return String(text).replace(/[.。]\s*$/, '');
	}

	function planValues(role, planText) {
		var skills = Array.isArray(role.essential_skills) ? role.essential_skills : [planText.default_skill];
		return {
			skill: skills[0] || planText.default_tool,
			second_skill: skills[1] || planText.default_second_skill,
			route: withoutFinalPeriod(role.recommended_entry_route || planText.default_route),
			title: role.title_fa
		};
	}

	function buildPlan(role, planText) {
		var values = planValues(role, planText);
		return {
			steps: WEEK_KEYS.map(function (key) {
				return ns.Format.fillTemplate(planText[key], values);
			}),
			experiment: ns.Format.fillTemplate(planText.experiment, values)
		};
	}

	ns.Plan = {
		buildPlan: buildPlan
	};
})(window.ItcpFinder = window.ItcpFinder || {});
