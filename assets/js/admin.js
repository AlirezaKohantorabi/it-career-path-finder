(function () {
	'use strict';

	var ARABIC_TO_PERSIAN = { 'ي': 'ی', 'ك': 'ک' };

	function normalized(text) {
		return String(text).toLowerCase().replace(/[يك]/g, function (letter) {
			return ARABIC_TO_PERSIAN[letter];
		});
	}

	function confirmBeforeSubmit(form) {
		form.addEventListener('submit', function (event) {
			if (!window.confirm(form.getAttribute('data-itcp-confirm'))) {
				event.preventDefault();
			}
		});
	}

	function filterRows(input) {
		var table = document.getElementById(input.getAttribute('data-itcp-filter'));
		if (!table) {
			return;
		}
		var query = normalized(input.value.trim());
		Array.prototype.forEach.call(table.querySelectorAll('[data-itcp-row]'), function (row) {
			row.hidden = query !== '' && normalized(row.textContent).indexOf(query) === -1;
		});
	}

	function bindFilter(input) {
		input.addEventListener('input', function () {
			filterRows(input);
		});
	}

	function start() {
		Array.prototype.forEach.call(document.querySelectorAll('form[data-itcp-confirm]'), confirmBeforeSubmit);
		Array.prototype.forEach.call(document.querySelectorAll('[data-itcp-filter]'), bindFilter);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
