(function (ns) {
	'use strict';

	var PROBE_Y = 2;
	var MAX_HEADER_OFFSET = 180;
	var HEADER_GAP = 12;
	var VISIBLE_TOLERANCE = 4;
	var VISIBLE_ZONE_RATIO = 0.4;

	function prefersReducedMotion() {
		return ns.Format.safely(function () {
			return Boolean(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
		}, false);
	}

	function isPinned(element) {
		var position = window.getComputedStyle(element).position;
		return position === 'fixed' || position === 'sticky';
	}

	function isPageBoundary(node, root) {
		return !node || node === document.body || node === document.documentElement || node === root;
	}

	function pinnedBottomAbove(element, root) {
		var bottom = 0;
		for (var node = element; !isPageBoundary(node, root); node = node.parentElement) {
			if (isPinned(node)) {
				var rect = node.getBoundingClientRect();
				if (rect.top <= PROBE_Y && rect.bottom > bottom) {
					bottom = rect.bottom;
				}
			}
		}
		return bottom;
	}

	function measurePinnedHeaderHeight(root) {
		return ns.Format.safely(function () {
			if (!document.elementsFromPoint) {
				return 0;
			}
			var elements = document.elementsFromPoint(window.innerWidth / 2, PROBE_Y);
			return Array.prototype.reduce.call(elements, function (highest, element) {
				return Math.max(highest, pinnedBottomAbove(element, root));
			}, 0);
		}, 0);
	}

	function headerOffset(root) {
		return Math.min(measurePinnedHeaderHeight(root), MAX_HEADER_OFFSET) + HEADER_GAP;
	}

	function isComfortablyVisible(rect, offset) {
		return rect.top >= offset - VISIBLE_TOLERANCE && rect.top < VISIBLE_ZONE_RATIO * window.innerHeight;
	}

	function currentScrollTop() {
		return window.pageYOffset || document.documentElement.scrollTop || 0;
	}

	function scrollWindowTo(top) {
		var target = Math.max(0, top);
		var completed = ns.Format.safely(function () {
			window.scrollTo({ top: target, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
			return true;
		}, false);
		if (!completed) {
			window.scrollTo(0, target);
		}
	}

	function scrollToElement(element, root, onlyWhenHidden) {
		if (!element) {
			return;
		}
		var offset = headerOffset(root);
		var rect = element.getBoundingClientRect();
		if (onlyWhenHidden && isComfortablyVisible(rect, offset)) {
			return;
		}
		scrollWindowTo(currentScrollTop() + rect.top - offset);
	}

	function centerHorizontally(container, child) {
		ns.Format.safely(function () {
			var containerRect = container.getBoundingClientRect();
			var childRect = child.getBoundingClientRect();
			container.scrollLeft += childRect.left + childRect.width / 2 - (containerRect.left + containerRect.width / 2);
		}, null);
	}

	ns.Scroll = {
		prefersReducedMotion: prefersReducedMotion,
		scrollToElement: scrollToElement,
		centerHorizontally: centerHorizontally
	};
})(window.ItcpFinder = window.ItcpFinder || {});
