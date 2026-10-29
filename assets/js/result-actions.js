(function (ns) {
	'use strict';

	var F = ns.Format;
	var Dom = ns.Dom;
	var PRINT_HIDE_CLASS = 'itcp-print-hide';
	var PRINT_DELAY_MS = 50;
	var RESTORE_LISTENER_DELAY_MS = 400;

	function pageAddress() {
		return F.safely(function () {
			return window.location.href.split('#')[0];
		}, '');
	}

	function summaryLines(finder, ranking) {
		var texts = finder.text.results;
		var lines = [texts.summary_text_header];
		ranking.primary.forEach(function (result, position) {
			lines.push(F.fillTemplate(texts.summary_text_line, {
				number: F.toPersianDigits(position + 1),
				title: result.role.title_fa,
				title_en: result.role.title_en,
				score: F.toPersianDigits(result.score)
			}));
		});
		if (ranking.compromise.length > 0) {
			lines.push('');
			lines.push(texts.summary_text_compromise_header);
			ranking.compromise.forEach(function (result) {
				lines.push(F.fillTemplate(texts.summary_text_compromise_line, { title: result.role.title_fa, score: F.toPersianDigits(result.score) }));
			});
		}
		return lines;
	}

	function summaryText(finder, ranking) {
		var address = pageAddress();
		return summaryLines(finder, ranking).join('\n') + (address ? '\n\n' + address : '');
	}

	function copyWithTextarea(text) {
		var area = document.createElement('textarea');
		area.value = text;
		area.setAttribute('readonly', '');
		area.className = 'itcp-copy-buffer';
		document.body.appendChild(area);
		area.select();
		var copied = F.safely(function () {
			area.setSelectionRange(0, text.length);
			return document.execCommand('copy');
		}, false);
		document.body.removeChild(area);
		return copied;
	}

	function copyText(finder, text) {
		var texts = finder.text.results;
		var fallback = function () {
			finder.showToast(copyWithTextarea(text) ? texts.copied : texts.copy_failed);
		};
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(function () {
				finder.showToast(texts.copied);
			}, fallback);
			return;
		}
		fallback();
	}

	function shareSummary(finder, ranking) {
		F.safely(function () {
			navigator.share({
				title: finder.text.results.share_title,
				text: summaryLines(finder, ranking).join('\n'),
				url: pageAddress()
			}).catch(function () {
				return null;
			});
			return true;
		}, false);
	}

	function hideSiblingsForPrint(root) {
		var hidden = [];
		for (var node = root; node && node.parentElement && node !== document.body; node = node.parentElement) {
			Array.prototype.forEach.call(node.parentElement.children, function (sibling) {
				if (sibling !== node && !sibling.classList.contains(PRINT_HIDE_CLASS)) {
					sibling.classList.add(PRINT_HIDE_CLASS);
					hidden.push(sibling);
				}
			});
		}
		return hidden;
	}

	function openClosedDetails(container) {
		var opened = [];
		Array.prototype.forEach.call(container.querySelectorAll('details.itcp-more'), function (details) {
			if (!details.open && !details.classList.contains('itcp-noprint')) {
				details.open = true;
				opened.push(details);
			}
		});
		return opened;
	}

	function restoreAfterPrint(hidden, opened) {
		hidden.forEach(function (element) {
			element.classList.remove(PRINT_HIDE_CLASS);
		});
		opened.forEach(function (details) {
			details.open = false;
		});
	}

	function printResults(finder) {
		var hidden = hideSiblingsForPrint(finder.root);
		var opened = openClosedDetails(finder.parts.results);
		var restored = false;
		var restoreOnce = function () {
			if (restored) {
				return;
			}
			restored = true;
			restoreAfterPrint(hidden, opened);
			window.removeEventListener('afterprint', restoreOnce);
			document.removeEventListener('pointerdown', restoreOnce, true);
			window.removeEventListener('focus', restoreOnce);
		};
		window.addEventListener('afterprint', restoreOnce);
		window.setTimeout(function () {
			F.safely(function () {
				window.print();
				return true;
			}, false);
			window.setTimeout(function () {
				document.addEventListener('pointerdown', restoreOnce, true);
				window.addEventListener('focus', restoreOnce);
			}, RESTORE_LISTENER_DELAY_MS);
		}, PRINT_DELAY_MS);
	}

	function addAction(bar, className, iconName, label, onClick) {
		var button = Dom.createButton('itcp-btn ' + className, iconName, label);
		button.addEventListener('click', onClick);
		bar.appendChild(button);
	}

	function buildActionBar(finder, ranking) {
		var texts = finder.text.results;
		var bar = Dom.createElement('div', 'itcp-actions itcp-noprint');
		addAction(bar, 'itcp-btn-p is-wide', 'copy', texts.copy, function () {
			copyText(finder, summaryText(finder, ranking));
		});
		if (navigator.share) {
			addAction(bar, 'itcp-btn-s', 'share', texts.share, function () {
				shareSummary(finder, ranking);
			});
		}
		addAction(bar, 'itcp-btn-s', 'print', texts.print, function () {
			printResults(finder);
		});
		addAction(bar, 'itcp-btn-g', 'redo', texts.restart_quiz, function () {
			finder.restart();
		});
		return bar;
	}

	ns.ResultActions = {
		summaryText: summaryText,
		buildActionBar: buildActionBar
	};
})(window.ItcpFinder = window.ItcpFinder || {});
