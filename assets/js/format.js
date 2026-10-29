(function (ns) {
	'use strict';

	var PERSIAN_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
	var RATING_MIN = 1;
	var RATING_SPAN = 4;
	var NEUTRAL_RATING = 3;

	function toPersianDigits(value) {
		return String(value).replace(/[0-9]/g, function (digit) {
			return PERSIAN_DIGITS.charAt(Number(digit));
		});
	}

	function fillTemplate(template, values) {
		var replacements = values || {};
		return String(template || '').replace(/\{(\w+)\}/g, function (placeholder, key) {
			return Object.prototype.hasOwnProperty.call(replacements, key) ? String(replacements[key]) : placeholder;
		});
	}

	function uniqueValues(list) {
		var seen = {};
		return list.filter(function (item) {
			if (seen[item]) {
				return false;
			}
			seen[item] = true;
			return true;
		});
	}

	function clamp(value, min, max) {
		return Math.max(min, Math.min(max, value));
	}

	function ratingOf(map, key, fallback) {
		var value = map ? map[key] : undefined;
		return typeof value === 'number' ? value : fallback;
	}

	function normalizeRating(rating) {
		var value = typeof rating === 'number' ? rating : NEUTRAL_RATING;
		return clamp((value - RATING_MIN) / RATING_SPAN, 0, 1);
	}

	function listOrFallback(value, fallback) {
		return Array.isArray(value) && value.length ? value : fallback;
	}

	function listOrEmpty(value) {
		return Array.isArray(value) ? value : [];
	}

	function safely(action, fallback) {
		try {
			return action();
		} catch (error) {
			return fallback;
		}
	}

	ns.Format = {
		toPersianDigits: toPersianDigits,
		fillTemplate: fillTemplate,
		uniqueValues: uniqueValues,
		clamp: clamp,
		ratingOf: ratingOf,
		normalizeRating: normalizeRating,
		listOrFallback: listOrFallback,
		listOrEmpty: listOrEmpty,
		safely: safely
	};
})(window.ItcpFinder = window.ItcpFinder || {});
