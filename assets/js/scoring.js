(function (ns) {
	'use strict';

	var F = ns.Format;

	var NEUTRAL = 3;
	var STRONG_FIT = 4;
	var WEAK_FIT = 2;
	var MAX_RATING_PLUS_ONE = 6;
	var NO_PREFERENCE = 'no_preference';

	var INTEREST = { MATCH_BASE: 0.45, MATCH_RANGE: 0.55, NO_MATCH: 0.18, CONTACT_BONUS: 0.12, SOLO_BONUS: 0.08, SOLO_PENALTY: 0.12, SOLO_FLOOR: 0.1, LABEL_LIMIT: 3 };
	var ARRANGEMENT = { MISSING_FIT: 2, FREEDOM_BONUS: 0.1, LABEL_LIMIT: 2 };
	var CLIENT = { MISSING_FIT: 2, INDUSTRY_BONUS: 0.12, INDUSTRY_PENALTY: 0.1, INDUSTRY_FLOOR: 0.15 };
	var WORK_MODE = { MISSING_REMOTE: 2, REMOTE_REQUIRED: 3, FIELD_ROLE_FIT: 5, NON_FIELD_ROLE_FIT: 2, FLEXIBLE_FIT: 4 };
	var ENTRY = { EXPERIENCED_HARD: 0.9, EXPERIENCED_EASY: 0.75, EASY: 0.85, MEDIUM: 0.65, HARD: 0.45, PORTFOLIO_PENALTY: 0.15, PORTFOLIO_FLOOR: 0.1, ENGLISH_PENALTY: 0.08, ENGLISH_FLOOR: 0.05 };
	var INCOME = { HIGH: 0.95, MEDIUM: 0.65, LOW: 0.3, RELAXED: 0.85 };
	var STABILITY = { RELAXED: 0.75 };
	var INSURANCE = { RELAXED: 0.8, PREFERRED_BASE: 0.4, PREFERRED_RANGE: 0.6 };
	var STRESS = { STEP_PENALTY: 0.3, FLOOR: 0.1 };
	var DISTANCE = { RESPONSIBILITY_STEP: 0.25, ROUTINE_STEP: 0.22, FLOOR: 0.2 };
	var MEANING_MIDPOINT = 0.5;
	var ROUTINE_REASON_THRESHOLD = 0.7;

	var NOVICE_LEVELS = ['complete_beginner', 'career_switcher', 'learning'];
	var PORTFOLIO_CATEGORIES = ['software_development', 'design_product'];
	var ON_CALL_CRITICAL_CATEGORIES = ['infrastructure_operations', 'cybersecurity'];
	var URGENT_LABEL = 'high_stress_or_urgency_possible';
	var STRESS_TOLERANCE = { low: 2, medium: 3, high: 4, very_high: 5 };
	var RESPONSIBILITY_PREFERENCE = { low: 2, medium: 3, high: 4, leadership: 5 };
	var ROUTINE_PREFERENCE = { routine: 5, mixed: 3, varied: 2, exploratory: 1 };

	function lookup(map, key) {
		return Object.prototype.hasOwnProperty.call(map, key) ? map[key] : undefined;
	}

	function ratingsOf(role) {
		return role.ratings_1_to_5 || {};
	}

	function fitGroupOf(role, group) {
		return ratingsOf(role)[group] || {};
	}

	function contains(list, value) {
		return F.listOrEmpty(list).indexOf(value) !== -1;
	}

	function invertedDifficulty(difficulty) {
		return F.normalizeRating(MAX_RATING_PLUS_ONE - difficulty);
	}

	function interestBaseValue(knownCount, matchedCount) {
		if (matchedCount === 0) {
			return INTEREST.NO_MATCH;
		}
		return Math.min(1, INTEREST.MATCH_BASE + INTEREST.MATCH_RANGE * (matchedCount / knownCount));
	}

	function adjustForCustomerContact(value, roleTags, contact) {
		var communicative = contains(roleTags, 'communication');
		if (contact === 'high' || contact === 'frequent') {
			return communicative || contains(roleTags, 'business') ? Math.min(1, value + INTEREST.CONTACT_BONUS) : value;
		}
		if (contact === 'minimal') {
			return communicative ? Math.max(INTEREST.SOLO_FLOOR, value - INTEREST.SOLO_PENALTY) : Math.min(1, value + INTEREST.SOLO_BONUS);
		}
		return value;
	}

	function interestFactor(role, answers, assessment) {
		var index = assessment.context.index;
		var roleTags = F.listOrEmpty(role.interest_tags);
		var chosen = F.listOrEmpty(answers.interests);
		var known = roleTags.filter(function (tag) {
			return index.knownInterests[tag] === true;
		});
		var matched = known.filter(function (tag) {
			return contains(chosen, tag);
		});
		if (matched.length > 0) {
			assessment.addReason('interest_match', { interests: interestLabels(matched, assessment.context) });
		} else {
			assessment.addRisk('interest_weak');
		}
		return adjustForCustomerContact(interestBaseValue(known.length, matched.length), roleTags, answers.customer_contact);
	}

	function interestLabels(tags, context) {
		return tags.slice(0, INTEREST.LABEL_LIMIT).map(function (tag) {
			return context.index.interestLabels[tag] || tag;
		}).join(context.text.separators.comma);
	}

	function arrangementFitFor(arrangement, role) {
		var fit = fitGroupOf(role, 'work_arrangement_fit');
		if (arrangement === 'internship') {
			var difficulty = F.ratingOf(ratingsOf(role), 'entry_difficulty', NEUTRAL);
			return Math.round((F.ratingOf(fit, 'employee', NEUTRAL) + (MAX_RATING_PLUS_ONE - difficulty)) / 2);
		}
		return F.ratingOf(fit, arrangement, ARRANGEMENT.MISSING_FIT);
	}

	function bestArrangementFit(chosen, role) {
		return chosen.reduce(function (best, arrangement) {
			return Math.max(best, arrangementFitFor(arrangement, role));
		}, 1);
	}

	function arrangementFactor(role, answers, assessment) {
		var context = assessment.context;
		var chosen = F.listOrFallback(answers.work_arrangement, ['employee']);
		var best = bestArrangementFit(chosen, role);
		var value = F.normalizeRating(best);
		if (answers.primary_goal === 'freedom' && F.ratingOf(fitGroupOf(role, 'work_arrangement_fit'), 'freelance', 1) >= STRONG_FIT) {
			value = Math.min(1, value + ARRANGEMENT.FREEDOM_BONUS);
		}
		if (best >= STRONG_FIT) {
			var labels = ns.Dataset.optionLabels(context.index, 'work_arrangement', chosen).slice(0, ARRANGEMENT.LABEL_LIMIT);
			assessment.addReason('arrangement_match', { arrangements: labels.join(context.text.separators.slash) });
		} else if (best <= WEAK_FIT) {
			assessment.addCompromise('arrangement_compromise');
			assessment.addRisk('arrangement_uncommon');
		}
		return value;
	}

	function clientFitFor(client, role) {
		var fit = fitGroupOf(role, 'client_type_fit');
		if (client !== NO_PREFERENCE) {
			return F.ratingOf(fit, client, CLIENT.MISSING_FIT);
		}
		return Math.max(
			F.ratingOf(fit, 'individual', NEUTRAL),
			F.ratingOf(fit, 'small_business', NEUTRAL),
			F.ratingOf(fit, 'medium_large_organization', NEUTRAL)
		);
	}

	function expandIndustries(chosen, aliases) {
		var accepted = Object.create(null);
		chosen.forEach(function (industry) {
			accepted[industry] = true;
			F.listOrEmpty(lookup(aliases, industry)).forEach(function (alias) {
				accepted[alias] = true;
			});
		});
		return accepted;
	}

	function adjustForIndustries(value, role, answers, context) {
		var chosen = F.listOrEmpty(answers.industry_preferences);
		if (chosen.length === 0 || contains(chosen, NO_PREFERENCE)) {
			return value;
		}
		var accepted = expandIndustries(chosen, context.index.industryAliases);
		var matches = F.listOrEmpty(role.typical_workplaces).some(function (place) {
			return accepted[place] === true;
		});
		return matches ? Math.min(1, value + CLIENT.INDUSTRY_BONUS) : Math.max(CLIENT.INDUSTRY_FLOOR, value - CLIENT.INDUSTRY_PENALTY);
	}

	function clientFactor(role, answers, assessment) {
		var context = assessment.context;
		var client = answers.client_type || NO_PREFERENCE;
		var fit = clientFitFor(client, role);
		if (client !== NO_PREFERENCE && fit >= STRONG_FIT) {
			assessment.addReason('client_match', { client: ns.Dataset.optionLabel(context.index, 'client_type', client) });
		}
		if (client === 'individual' && fit <= WEAK_FIT) {
			assessment.addPenalty(fit === 1 ? 'individual_client_no_fit' : 'individual_client_poor_fit');
			assessment.addCompromise('client_compromise');
			assessment.addRisk('client_organizational');
		}
		return adjustForIndustries(F.normalizeRating(fit), role, answers, context);
	}

	function isFieldRole(fit) {
		return fit.onsite === 5 && F.ratingOf(fit, 'remote', 1) <= WEAK_FIT;
	}

	function modeFitFor(mode, fit) {
		if (mode === 'remote') {
			return F.ratingOf(fit, 'remote', WORK_MODE.MISSING_REMOTE);
		}
		if (mode === 'hybrid' || mode === 'onsite') {
			return F.ratingOf(fit, mode, NEUTRAL);
		}
		if (mode === 'field_work') {
			return isFieldRole(fit) ? WORK_MODE.FIELD_ROLE_FIT : WORK_MODE.NON_FIELD_ROLE_FIT;
		}
		return mode === 'flexible' ? WORK_MODE.FLEXIBLE_FIT : NEUTRAL;
	}

	function bestModeFit(modes, fit) {
		return modes.reduce(function (best, mode) {
			return Math.max(best, modeFitFor(mode, fit));
		}, 1);
	}

	function requiresRemoteOnly(modes, answers) {
		var onlyRemote = modes.length === 1 && modes[0] === 'remote';
		return onlyRemote || answers.location_scope === 'anywhere_iran_remote' || answers.primary_goal === 'remote';
	}

	function wantsRemote(modes, answers) {
		return contains(modes, 'remote') || answers.primary_goal === 'remote';
	}

	function workModeFactor(role, answers, assessment) {
		var fit = fitGroupOf(role, 'work_mode_fit');
		var modes = F.listOrFallback(answers.work_mode, ['flexible']);
		var remoteFit = F.ratingOf(fit, 'remote', 1);
		if (requiresRemoteOnly(modes, answers) && remoteFit < WORK_MODE.REMOTE_REQUIRED) {
			assessment.addPenalty('remote_only_mismatch');
			assessment.addCompromise('remote_compromise');
			assessment.addRisk('remote_unlikely');
		} else if (wantsRemote(modes, answers) && remoteFit >= STRONG_FIT) {
			assessment.addReason('remote_match');
		}
		return F.normalizeRating(bestModeFit(modes, fit));
	}

	function isNovice(answers) {
		return contains(NOVICE_LEVELS, answers.current_level);
	}

	function urgentNoviceEntry(difficulty, assessment) {
		if (difficulty > NEUTRAL) {
			assessment.addPenalty(difficulty === 5 ? 'novice_urgent_very_hard' : 'novice_urgent_hard');
			assessment.addCompromise('entry_more_time');
			assessment.addRisk('entry_too_hard_for_urgent', { difficulty: F.toPersianDigits(difficulty) });
		} else {
			assessment.addReason('entry_accessible');
		}
		return invertedDifficulty(difficulty);
	}

	function shortNoviceEntry(difficulty, assessment) {
		if (difficulty === 5) {
			assessment.addPenalty('novice_short_very_hard');
			assessment.addCompromise('entry_more_foundation');
			assessment.addRisk('entry_not_junior');
		}
		return invertedDifficulty(difficulty);
	}

	function relaxedEntry(difficulty, answers) {
		if (answers.current_level === 'experienced' || answers.time_to_entry === 'over_12_months') {
			return difficulty >= NEUTRAL ? ENTRY.EXPERIENCED_HARD : ENTRY.EXPERIENCED_EASY;
		}
		if (difficulty <= NEUTRAL) {
			return ENTRY.EASY;
		}
		return difficulty === STRONG_FIT ? ENTRY.MEDIUM : ENTRY.HARD;
	}

	function entryBaseValue(difficulty, answers, assessment) {
		if (isNovice(answers) && answers.time_to_entry === 'under_3_months') {
			return urgentNoviceEntry(difficulty, assessment);
		}
		if (isNovice(answers) && answers.time_to_entry === '3_to_6_months') {
			return shortNoviceEntry(difficulty, assessment);
		}
		return relaxedEntry(difficulty, answers);
	}

	function blendWithFastEntryGoal(value, difficulty, answers) {
		var blends = answers.primary_goal === 'fast_entry' && answers.time_to_entry !== 'under_3_months' && answers.current_level !== 'experienced';
		return blends ? (value + invertedDifficulty(difficulty)) / 2 : value;
	}

	function adjustForPortfolio(value, role, answers, assessment) {
		if (answers.must_have_portfolio !== 'no' || !contains(PORTFOLIO_CATEGORIES, role.category_id)) {
			return value;
		}
		assessment.addCompromise('portfolio_needed');
		return Math.max(ENTRY.PORTFOLIO_FLOOR, value - ENTRY.PORTFOLIO_PENALTY);
	}

	function adjustForEnglish(value, difficulty, answers, assessment) {
		if (answers.english_level !== 'basic' || difficulty < STRONG_FIT) {
			return value;
		}
		assessment.addGap('english_gap');
		return Math.max(ENTRY.ENGLISH_FLOOR, value - ENTRY.ENGLISH_PENALTY);
	}

	function entryFactor(role, answers, assessment) {
		var difficulty = F.ratingOf(ratingsOf(role), 'entry_difficulty', NEUTRAL);
		var value = entryBaseValue(difficulty, answers, assessment);
		value = blendWithFastEntryGoal(value, difficulty, answers);
		value = adjustForPortfolio(value, role, answers, assessment);
		return adjustForEnglish(value, difficulty, answers, assessment);
	}

	function ambitiousIncome(ceiling, assessment) {
		if (ceiling >= STRONG_FIT) {
			assessment.addReason('income_high_ceiling');
		} else if (ceiling <= WEAK_FIT) {
			assessment.addRisk('income_limited_ceiling');
		}
		return F.normalizeRating(ceiling);
	}

	function aboveMarketIncome(ceiling) {
		if (ceiling >= STRONG_FIT) {
			return INCOME.HIGH;
		}
		return ceiling === NEUTRAL ? INCOME.MEDIUM : INCOME.LOW;
	}

	function incomeBaseValue(ceiling, answers, assessment) {
		if (answers.income_target === 'very_high' || answers.primary_goal === 'income_growth') {
			return ambitiousIncome(ceiling, assessment);
		}
		return answers.income_target === 'above_market' ? aboveMarketIncome(ceiling) : INCOME.RELAXED;
	}

	function incomeFactor(role, answers, assessment) {
		var ceiling = F.ratingOf(ratingsOf(role), 'income_ceiling', NEUTRAL);
		var value = incomeBaseValue(ceiling, answers, assessment);
		return answers.primary_goal === 'career_growth' ? (value + F.normalizeRating(ceiling)) / 2 : value;
	}

	function caresAboutStability(answers) {
		return answers.security_priority === 'critical' || answers.security_priority === 'high' || answers.primary_goal === 'stability';
	}

	function stabilityFactor(role, answers, assessment) {
		if (!caresAboutStability(answers)) {
			return STABILITY.RELAXED;
		}
		var stability = F.ratingOf(ratingsOf(role), 'job_stability', NEUTRAL);
		if (stability <= WEAK_FIT) {
			assessment.addCompromise('stability_compromise');
			assessment.addRisk('stability_low');
		}
		return F.normalizeRating(stability);
	}

	function insuranceLikelihood(role) {
		return F.ratingOf(ratingsOf(role), 'insurance_likelihood_in_employee_roles', NEUTRAL);
	}

	function mandatoryInsurance(likelihood, assessment) {
		if (likelihood < NEUTRAL) {
			assessment.addPenalty('insurance_unlikely');
			assessment.addCompromise('insurance_compromise');
			assessment.addRisk('insurance_low');
		} else if (likelihood >= STRONG_FIT) {
			assessment.addReason('insurance_likely');
		}
		return F.normalizeRating(likelihood);
	}

	function insuranceFactor(role, answers, assessment) {
		var likelihood = insuranceLikelihood(role);
		if (answers.insurance_requirement === 'mandatory') {
			return mandatoryInsurance(likelihood, assessment);
		}
		if (answers.insurance_requirement === 'preferred') {
			return INSURANCE.PREFERRED_BASE + INSURANCE.PREFERRED_RANGE * F.normalizeRating(likelihood);
		}
		return INSURANCE.RELAXED;
	}

	function stressLevel(role) {
		return F.ratingOf(ratingsOf(role), 'stress_level', NEUTRAL);
	}

	function mayNeedUrgentResponse(role) {
		return stressLevel(role) >= STRONG_FIT || contains(role.labels, URGENT_LABEL);
	}

	function recordOnCallConflict(role, answers, assessment) {
		if (answers.on_call_tolerance !== 'never' || !mayNeedUrgentResponse(role)) {
			return;
		}
		assessment.addPenalty(contains(ON_CALL_CRITICAL_CATEGORIES, role.category_id) ? 'no_on_call_critical' : 'no_on_call_other');
		assessment.addCompromise('on_call_compromise');
		assessment.addRisk('on_call_risk');
	}

	function stressFactor(role, answers) {
		var stress = stressLevel(role);
		var tolerance = lookup(STRESS_TOLERANCE, answers.stress_tolerance) || NEUTRAL;
		return stress <= tolerance ? 1 : Math.max(STRESS.FLOOR, 1 - STRESS.STEP_PENALTY * (stress - tolerance));
	}

	function distanceFactor(level, preferred, step) {
		return Math.max(DISTANCE.FLOOR, 1 - step * Math.abs(level - preferred));
	}

	function responsibilityFactor(role, answers) {
		var level = F.ratingOf(ratingsOf(role), 'responsibility_level', NEUTRAL);
		var preferred = lookup(RESPONSIBILITY_PREFERENCE, answers.responsibility_preference) || NEUTRAL;
		return distanceFactor(level, preferred, DISTANCE.RESPONSIBILITY_STEP);
	}

	function routineFactor(role, answers) {
		var level = F.ratingOf(ratingsOf(role), 'routine_level', NEUTRAL);
		var preferred = lookup(ROUTINE_PREFERENCE, answers.routine_preference) || NEUTRAL;
		return distanceFactor(level, preferred, DISTANCE.ROUTINE_STEP);
	}

	function recordLocalMarketGap(role, answers, assessment) {
		var limitedRemote = F.ratingOf(fitGroupOf(role, 'work_mode_fit'), 'remote', 1) < WORK_MODE.REMOTE_REQUIRED;
		var smallMarket = F.ratingOf(ratingsOf(role), 'relative_market_opportunity', NEUTRAL) <= WEAK_FIT;
		if (answers.location_scope === 'own_city_only' && limitedRemote && smallMarket) {
			assessment.addGap('local_market_gap');
		}
	}

	function addFallbackReasons(role, answers, routineValue, assessment) {
		if (assessment.reasonCount() < 3 && F.ratingOf(ratingsOf(role), 'relative_market_opportunity', NEUTRAL) >= NEUTRAL) {
			assessment.addReason('reason_market_demand');
		}
		if (assessment.reasonCount() < 3 && routineValue >= ROUTINE_REASON_THRESHOLD) {
			assessment.addReason('reason_routine_fit', { routine: ns.Dataset.optionLabel(assessment.context.index, 'routine_preference', answers.routine_preference) });
		}
		if (assessment.reasonCount() < 3) {
			assessment.addReason('reason_balance');
		}
	}

	function weightedTotal(factors, weights) {
		return Object.keys(factors).reduce(function (total, key) {
			return total + factors[key] * (Number(weights[key]) || 0);
		}, 0);
	}

	function finalScore(factors, answers, penalty, scoring) {
		var total = weightedTotal(factors, scoring.weights);
		if (answers.primary_goal === 'meaning_interest') {
			total += (Number(scoring.meaning_interest_bonus) || 0) * (factors.interest_match - MEANING_MIDPOINT);
		}
		return Math.round(F.clamp(total - penalty, scoring.score_bounds.min, scoring.score_bounds.max));
	}

	function isStrictlyExcluded(role, answers, context) {
		return context.strictInsuranceFilter && answers.insurance_requirement === 'mandatory' && insuranceLikelihood(role) < NEUTRAL;
	}

	function scoreRole(role, answers, context) {
		var assessment = new ns.Assessment(context);
		var factors = {};
		factors.interest_match = interestFactor(role, answers, assessment);
		factors.work_arrangement_match = arrangementFactor(role, answers, assessment);
		factors.client_type_match = clientFactor(role, answers, assessment);
		factors.work_mode_match = workModeFactor(role, answers, assessment);
		factors.entry_accessibility_match = entryFactor(role, answers, assessment);
		factors.income_match = incomeFactor(role, answers, assessment);
		factors.stability_match = stabilityFactor(role, answers, assessment);
		factors.insurance_match = insuranceFactor(role, answers, assessment);
		recordOnCallConflict(role, answers, assessment);
		factors.stress_match = stressFactor(role, answers);
		factors.responsibility_match = responsibilityFactor(role, answers);
		factors.routine_match = routineFactor(role, answers);
		recordLocalMarketGap(role, answers, assessment);
		addFallbackReasons(role, answers, factors.routine_match, assessment);
		var score = finalScore(factors, answers, assessment.penalty, context.scoring);
		return assessment.toResult(role, score, isStrictlyExcluded(role, answers, context));
	}

	ns.Scoring = {
		scoreRole: scoreRole
	};
})(window.ItcpFinder = window.ItcpFinder || {});
