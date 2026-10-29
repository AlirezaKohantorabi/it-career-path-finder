(function (ns) {
	'use strict';

	var DATASET_ELEMENT_ID = 'itcp-dataset';

	function readDataset() {
		var source = document.getElementById(DATASET_ELEMENT_ID);
		if (!source) {
			return null;
		}
		return ns.Format.safely(function () {
			return JSON.parse(source.textContent);
		}, null);
	}

	function isUsable(dataset) {
		return Boolean(
			dataset &&
			Array.isArray(dataset.questions) &&
			dataset.questions.length > 0 &&
			Array.isArray(dataset.roles) &&
			dataset.text &&
			dataset.scoring
		);
	}

	function createLookup() {
		return Object.create(null);
	}

	function indexByKey(list, key) {
		var lookup = createLookup();
		ns.Format.listOrEmpty(list).forEach(function (item) {
			lookup[item[key]] = item;
		});
		return lookup;
	}

	function labelsByValue(list) {
		var lookup = createLookup();
		ns.Format.listOrEmpty(list).forEach(function (item) {
			lookup[item.value] = item.label;
		});
		return lookup;
	}

	function optionValueSet(question) {
		var lookup = createLookup();
		ns.Format.listOrEmpty(question && question.options).forEach(function (option) {
			lookup[option.value] = true;
		});
		return lookup;
	}

	function positionsById(list) {
		var lookup = createLookup();
		ns.Format.listOrEmpty(list).forEach(function (item, position) {
			lookup[item.id] = position;
		});
		return lookup;
	}

	function buildIndex(dataset) {
		var questionById = indexByKey(dataset.questions, 'id');
		return {
			questions: dataset.questions,
			sections: ns.Format.listOrEmpty(dataset.sections),
			sectionPositions: positionsById(dataset.sections),
			questionById: questionById,
			categoryById: indexByKey(dataset.categories, 'id'),
			workplaceLabels: labelsByValue(dataset.workplace_types),
			interestLabels: labelsByValue(dataset.interest_tags),
			knownInterests: optionValueSet(questionById.interests),
			industryAliases: dataset.industry_aliases || {},
			summaryQuestions: ns.Format.listOrEmpty(dataset.summary_questions)
		};
	}

	function optionLabel(index, questionId, value) {
		var question = index.questionById[questionId];
		var options = ns.Format.listOrEmpty(question && question.options);
		for (var position = 0; position < options.length; position++) {
			if (options[position].value === value) {
				return options[position].label;
			}
		}
		return value;
	}

	function optionLabels(index, questionId, values) {
		return values.map(function (value) {
			return optionLabel(index, questionId, value);
		});
	}

	function sectionOf(index, question) {
		var position = index.sectionPositions[question.section];
		var section = typeof position === 'number' ? index.sections[position] : null;
		return {
			number: typeof position === 'number' ? position + 1 : 1,
			total: Math.max(1, index.sections.length),
			title: section ? section.title : ''
		};
	}

	ns.Dataset = {
		readDataset: readDataset,
		isUsable: isUsable,
		buildIndex: buildIndex,
		optionLabel: optionLabel,
		optionLabels: optionLabels,
		sectionOf: sectionOf
	};
})(window.ItcpFinder = window.ItcpFinder || {});
