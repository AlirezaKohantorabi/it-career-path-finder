(function (ns) {
	'use strict';

	var GROUP_PRIMARY = 'primary';
	var GROUP_COMPROMISE = 'compromise';

	function byScoreDescending(first, second) {
		return second.score - first.score;
	}

	function scoreAllRoles(roles, answers, context) {
		return roles
			.map(function (role) {
				return ns.Scoring.scoreRole(role, answers, context);
			})
			.filter(function (result) {
				return !result.excluded;
			})
			.sort(byScoreDescending);
	}

	function qualifiesAsPrimary(result, groups, grouping) {
		return result.penalty === 0 && result.score >= grouping.primary_min_score && groups.primary.length < grouping.primary_max_count;
	}

	function qualifiesAsCompromise(result, groups, grouping) {
		var needsFlexibility = result.penalty > 0 || result.compromises.length > 0;
		return needsFlexibility && result.score >= grouping.compromise_min_score && groups.compromise.length < grouping.compromise_max_count;
	}

	function splitIntoGroups(results, grouping) {
		var groups = { primary: [], compromise: [], low: [] };
		results.forEach(function (result) {
			if (qualifiesAsPrimary(result, groups, grouping)) {
				groups.primary.push(result);
			} else if (qualifiesAsCompromise(result, groups, grouping)) {
				groups.compromise.push(result);
			} else {
				groups.low.push(result);
			}
		});
		return groups;
	}

	function fillPrimaryToMinimum(groups, minimum) {
		var promoted = false;
		while (groups.primary.length < minimum && groups.compromise.length > 0) {
			groups.primary.push(groups.compromise.shift());
			promoted = true;
		}
		while (groups.primary.length < minimum && groups.low.length > 0) {
			groups.primary.push(groups.low.shift());
			promoted = true;
		}
		return promoted;
	}

	function lowestResults(results, count) {
		return results.slice(-count).reverse();
	}

	function groupByRoleId(primary, compromise) {
		var groupOf = Object.create(null);
		primary.forEach(function (result) {
			groupOf[result.role.id] = GROUP_PRIMARY;
		});
		compromise.forEach(function (result) {
			groupOf[result.role.id] = GROUP_COMPROMISE;
		});
		return groupOf;
	}

	function rankRoles(roles, answers, context) {
		var grouping = context.scoring.grouping;
		var all = scoreAllRoles(roles, answers, context);
		var groups = splitIntoGroups(all, grouping);
		var promoted = fillPrimaryToMinimum(groups, grouping.primary_min_count);
		groups.primary.sort(byScoreDescending);
		return {
			all: all,
			primary: groups.primary,
			compromise: groups.compromise,
			low: lowestResults(groups.low, grouping.low_shown_count),
			promoted: promoted,
			groupOf: groupByRoleId(groups.primary, groups.compromise)
		};
	}

	ns.Ranking = {
		GROUP_PRIMARY: GROUP_PRIMARY,
		GROUP_COMPROMISE: GROUP_COMPROMISE,
		rankRoles: rankRoles
	};
})(window.ItcpFinder = window.ItcpFinder || {});
