(function (ns) {
	'use strict';

	var F = ns.Format;
	var Dom = ns.Dom;
	var TWO_COLUMN_OPTION_COUNT = 6;

	function elementIds(finder, question) {
		var prefix = 'itcp-' + finder.instanceNumber + '-' + question.id;
		return {
			prefix: prefix,
			title: prefix + '-title',
			hint: prefix + '-hint',
			error: prefix + '-error',
			inputName: prefix + '-choice'
		};
	}

	function buildTags(finder, question) {
		var texts = finder.text.question;
		var multi = ns.State.isMultiSelect(question);
		var tags = Dom.createElement('div', 'itcp-q-tags');
		tags.appendChild(Dom.createElement('span', 'itcp-tag', multi ? texts.multi_tag : texts.single_tag));
		tags.appendChild(Dom.createElement('span', 'itcp-tag ' + (question.required ? 'is-req' : 'is-opt'), question.required ? texts.required_tag : texts.optional_tag));
		tags.appendChild(Dom.createElement('span', 'itcp-tag is-time', ns.ProgressView.timeLeftText(finder, finder.state.currentIndex)));
		return tags;
	}

	function buildTitle(finder, question, ids) {
		var title = Dom.createElement('h3', 'itcp-q-title');
		title.id = ids.title;
		title.setAttribute('tabindex', '-1');
		title.appendChild(Dom.createElement('span', 'itcp-q-num', F.toPersianDigits(finder.state.currentIndex + 1)));
		title.appendChild(Dom.createElement('span', '', question.title));
		return title;
	}

	function buildHint(finder, question, ids) {
		var hint = Dom.createElement('div', 'itcp-hint');
		hint.id = ids.hint;
		hint.appendChild(Dom.createIcon('info'));
		hint.appendChild(Dom.createElement('span', '', finder.text.question.hint_prefix + question.hint));
		return hint;
	}

	function buildHeader(finder, question, ids) {
		var header = Dom.createElement('div', 'itcp-q-header');
		header.appendChild(buildTags(finder, question));
		header.appendChild(buildTitle(finder, question, ids));
		if (question.hint) {
			header.appendChild(buildHint(finder, question, ids));
		}
		return header;
	}

	function buildOptionGroup(question, ids) {
		var group = Dom.createElement('div', 'itcp-opts');
		group.setAttribute('role', ns.State.isMultiSelect(question) ? 'group' : 'radiogroup');
		group.setAttribute('aria-labelledby', ids.title);
		if (question.hint) {
			group.setAttribute('aria-describedby', ids.hint);
		}
		if (F.listOrEmpty(question.options).length >= TWO_COLUMN_OPTION_COUNT) {
			group.classList.add('is-2');
		}
		return group;
	}

	function buildOptionControl(question, option, position, ids) {
		var inputId = ids.prefix + '-' + position;
		var label = Dom.createElement('label', 'itcp-opt');
		label.setAttribute('for', inputId);
		var input = document.createElement('input');
		input.className = 'itcp-inp';
		input.id = inputId;
		input.name = ids.inputName;
		input.value = option.value;
		input.type = ns.State.isMultiSelect(question) ? 'checkbox' : 'radio';
		var indicator = Dom.createElement('span', 'itcp-ind ' + (ns.State.isMultiSelect(question) ? 'is-check' : 'is-radio'));
		indicator.setAttribute('aria-hidden', 'true');
		indicator.appendChild(Dom.createIcon('check'));
		label.appendChild(input);
		label.appendChild(indicator);
		label.appendChild(Dom.createElement('span', 'itcp-opt-tx', option.label));
		return { label: label, input: input, value: option.value };
	}

	function isSelected(question, answer, value) {
		return ns.State.isMultiSelect(question) ? F.listOrEmpty(answer).indexOf(value) !== -1 : answer === value;
	}

	function counterText(finder, question, answer) {
		var texts = finder.text.question;
		var count = F.listOrEmpty(answer).length;
		if (count > 0) {
			return F.fillTemplate(texts.selected_count, { count: F.toPersianDigits(count) });
		}
		return question.required ? texts.select_at_least_one : texts.can_skip;
	}

	function syncSelection(finder, question, controls, counter) {
		var answer = finder.state.answers[question.id];
		controls.forEach(function (control) {
			var selected = isSelected(question, answer, control.value);
			control.input.checked = selected;
			control.label.classList.toggle('is-sel', selected);
		});
		if (counter) {
			counter.textContent = counterText(finder, question, answer);
		}
	}

	function listenToOption(finder, question, control, onChange) {
		var recordPointer = function () {
			finder.recordPointer();
		};
		control.label.addEventListener('pointerdown', recordPointer);
		control.label.addEventListener('mousedown', recordPointer);
		control.label.addEventListener('touchstart', recordPointer, { passive: true });
		control.label.addEventListener('click', function () {
			finder.reselectOption(question, control.value);
		});
		control.input.addEventListener('change', function () {
			finder.selectOption(question, control.value, control.input.checked);
			onChange();
		});
	}

	function buildErrorBox(ids) {
		var box = Dom.createElement('div', 'itcp-err');
		box.id = ids.error;
		box.setAttribute('role', 'alert');
		box.setAttribute('data-itcp', 'error-box');
		return box;
	}

	function focusTitle(finder, title) {
		F.safely(function () {
			title.focus({ preventScroll: true });
			return true;
		}, false);
		ns.Scroll.scrollToElement(finder.parts.dots, finder.root, true);
	}

	function render(finder, moveFocus) {
		var question = finder.currentQuestion();
		var body = finder.parts.question;
		var ids = elementIds(finder, question);
		var header = buildHeader(finder, question, ids);
		var group = buildOptionGroup(question, ids);
		var counter = ns.State.isMultiSelect(question) ? Dom.createElement('p', 'itcp-count') : null;
		var controls = F.listOrEmpty(question.options).map(function (option, position) {
			return buildOptionControl(question, option, position, ids);
		});
		var refresh = function () {
			syncSelection(finder, question, controls, counter);
		};
		controls.forEach(function (control) {
			listenToOption(finder, question, control, refresh);
			group.appendChild(control.label);
		});
		Dom.clearChildren(body);
		body.appendChild(header);
		body.appendChild(group);
		if (counter) {
			counter.setAttribute('aria-live', 'polite');
			body.appendChild(counter);
		}
		body.appendChild(buildErrorBox(ids));
		refresh();
		if (moveFocus) {
			focusTitle(finder, header.querySelector('.itcp-q-title'));
		}
	}

	function showError(finder, message) {
		var box = finder.parts.question.querySelector('[data-itcp="error-box"]');
		if (!box) {
			return;
		}
		box.textContent = message;
		box.classList.remove('is-on');
		box.getBoundingClientRect();
		box.classList.add('is-on');
		var rect = box.getBoundingClientRect();
		if (rect.bottom > window.innerHeight - 90 || rect.top < 0) {
			ns.Scroll.scrollToElement(box, finder.root, false);
		}
	}

	function clearError(finder) {
		var box = finder.parts.question.querySelector('[data-itcp="error-box"]');
		if (box) {
			box.textContent = '';
			box.classList.remove('is-on');
		}
	}

	ns.QuestionView = {
		render: render,
		showError: showError,
		clearError: clearError
	};
})(window.ItcpFinder = window.ItcpFinder || {});
