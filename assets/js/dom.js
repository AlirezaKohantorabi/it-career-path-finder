(function (ns) {
	'use strict';

	var SVG_NAMESPACE = 'http://www.w3.org/2000/svg';

	var ICON_SHAPES = {
		check: [['path', { d: 'M5 12.5l4.5 4.5L19 7.5' }]],
		info: [['circle', { cx: '12', cy: '12', r: '9' }], ['path', { d: 'M12 11v6M12 7.5v.5' }]],
		up: [['path', { d: 'M7 17l10-10M9 7h8v8' }]],
		warn: [['path', { d: 'M12 3l9.5 16.5h-19z' }], ['path', { d: 'M12 10v4M12 17v.5' }]],
		chevron: [['path', { d: 'M6 9l6 6 6-6' }]],
		copy: [['rect', { x: '9', y: '9', width: '11', height: '11', rx: '2' }], ['path', { d: 'M5 15V5a2 2 0 0 1 2-2h8' }]],
		share: [
			['circle', { cx: '18', cy: '5', r: '2.5' }],
			['circle', { cx: '6', cy: '12', r: '2.5' }],
			['circle', { cx: '18', cy: '19', r: '2.5' }],
			['path', { d: 'M8.2 10.9l7.6-4.6M8.2 13.1l7.6 4.6' }]
		],
		print: [['path', { d: 'M6 9V3h12v6' }], ['rect', { x: '3', y: '9', width: '18', height: '8', rx: '2' }], ['path', { d: 'M7 14h10v7H7z' }]],
		redo: [['path', { d: 'M3 12a9 9 0 1 0 3-6.7' }], ['path', { d: 'M3 4v5h5' }]],
		edit: [['path', { d: 'M4 20h4L19 9l-4-4L4 16z' }], ['path', { d: 'M13.5 6.5l4 4' }]],
		tune: [
			['path', { d: 'M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0' }],
			['circle', { cx: '16', cy: '6', r: '2' }],
			['circle', { cx: '10', cy: '12', r: '2' }],
			['circle', { cx: '18', cy: '18', r: '2' }]
		],
		flag: [['path', { d: 'M5 21V4M5 4h11l-2 4 2 4H5' }]],
		list: [
			['path', { d: 'M9 6h11M9 12h11M9 18h11' }],
			['circle', { cx: '4.5', cy: '6', r: '1' }],
			['circle', { cx: '4.5', cy: '12', r: '1' }],
			['circle', { cx: '4.5', cy: '18', r: '1' }]
		],
		bulb: [
			['path', { d: 'M9 18h6M10 21h4' }],
			['path', { d: 'M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1 2.1h5c0-.9.4-1.6 1-2.1A6 6 0 0 0 12 3z' }]
		]
	};

	function createElement(tagName, className, text) {
		var element = document.createElement(tagName);
		if (className) {
			element.className = className;
		}
		if (text !== undefined && text !== null) {
			element.textContent = String(text);
		}
		return element;
	}

	function createShape(shape) {
		var node = document.createElementNS(SVG_NAMESPACE, shape[0]);
		Object.keys(shape[1]).forEach(function (attribute) {
			node.setAttribute(attribute, shape[1][attribute]);
		});
		return node;
	}

	function createIcon(name) {
		var svg = document.createElementNS(SVG_NAMESPACE, 'svg');
		svg.setAttribute('viewBox', '0 0 24 24');
		svg.setAttribute('aria-hidden', 'true');
		svg.setAttribute('focusable', 'false');
		(ICON_SHAPES[name] || []).forEach(function (shape) {
			svg.appendChild(createShape(shape));
		});
		return svg;
	}

	function createList(items, className, ordered) {
		var list = createElement(ordered ? 'ol' : 'ul', className);
		items.forEach(function (item) {
			list.appendChild(createElement('li', '', item));
		});
		return list;
	}

	function createButton(className, iconName, label) {
		var button = createElement('button', className);
		button.type = 'button';
		if (iconName) {
			button.appendChild(createIcon(iconName));
		}
		button.appendChild(createElement('span', '', label));
		return button;
	}

	function clearChildren(element) {
		element.textContent = '';
	}

	ns.Dom = {
		createElement: createElement,
		createIcon: createIcon,
		createList: createList,
		createButton: createButton,
		clearChildren: clearChildren
	};
})(window.ItcpFinder = window.ItcpFinder || {});
