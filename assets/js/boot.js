(function (ns) {
	'use strict';

	var ROOT_SELECTOR = '[data-itcp-app]';

	function reportError(error) {
		window.setTimeout(function () {
			throw error;
		}, 0);
	}

	function showDataError(root) {
		root.classList.add('is-error');
		var message = root.querySelector('[data-itcp="data-error"]');
		var loading = root.querySelector('[data-itcp="loading"]');
		if (message) {
			message.hidden = false;
		}
		if (loading) {
			loading.hidden = true;
		}
	}

	function instanceNumberOf(root, position) {
		var number = parseInt(root.getAttribute('data-itcp-instance'), 10);
		return number > 0 ? number : position + 1;
	}

	function startFinder(root, dataset, position) {
		try {
			new ns.Finder(root, dataset, instanceNumberOf(root, position)).start();
		} catch (error) {
			showDataError(root);
			reportError(error);
		}
	}

	function mountRoot(root, dataset, position) {
		if (root.getAttribute('data-itcp-ready') === '1') {
			return;
		}
		if (!dataset || !ns.Finder.hasRequiredParts(root)) {
			showDataError(root);
			return;
		}
		startFinder(root, dataset, position);
	}

	function mountAll() {
		var roots = document.querySelectorAll(ROOT_SELECTOR);
		if (roots.length === 0) {
			return;
		}
		var dataset = ns.Dataset.readDataset();
		var usable = ns.Dataset.isUsable(dataset) ? dataset : null;
		Array.prototype.forEach.call(roots, function (root, position) {
			mountRoot(root, usable, position);
		});
	}

	ns.mountAll = mountAll;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', mountAll);
	} else {
		mountAll();
	}
})(window.ItcpFinder = window.ItcpFinder || {});
