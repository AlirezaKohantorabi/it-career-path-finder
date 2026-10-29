(function (ns) {
	'use strict';

	var REASON_LIMIT = 3;
	var RISK_LIMIT = 2;

	function Assessment(context) {
		this.context = context;
		this.reasons = [];
		this.risks = [];
		this.gaps = [];
		this.compromises = [];
		this.penalty = 0;
	}

	Assessment.prototype.message = function (key, values) {
		return ns.Format.fillTemplate(this.context.text.messages[key], values);
	};

	Assessment.prototype.addReason = function (key, values) {
		this.reasons.push(this.message(key, values));
	};

	Assessment.prototype.addRisk = function (key, values) {
		this.risks.push(this.message(key, values));
	};

	Assessment.prototype.addGap = function (key, values) {
		this.gaps.push(this.message(key, values));
	};

	Assessment.prototype.addCompromise = function (key, values) {
		this.compromises.push(this.message(key, values));
	};

	Assessment.prototype.addPenalty = function (key) {
		this.penalty += Number(this.context.scoring.penalties[key]) || 0;
	};

	Assessment.prototype.reasonCount = function () {
		return this.reasons.length;
	};

	Assessment.prototype.finalRisks = function () {
		var risks = ns.Format.uniqueValues(this.risks.concat(this.gaps));
		return risks.length ? risks : [this.message('risk_default')];
	};

	Assessment.prototype.toResult = function (role, score, excluded) {
		return {
			role: role,
			score: score,
			reasons: ns.Format.uniqueValues(this.reasons).slice(0, REASON_LIMIT),
			risks: this.finalRisks().slice(0, RISK_LIMIT),
			compromises: ns.Format.uniqueValues(this.compromises),
			penalty: this.penalty,
			excluded: excluded
		};
	};

	ns.Assessment = Assessment;
})(window.ItcpFinder = window.ItcpFinder || {});
