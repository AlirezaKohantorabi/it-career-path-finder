# `assets/js/dom.js` → `ItcpFinder.Dom`

**Purpose:** the only way the tool creates elements. Text is always set with `textContent` and icons are built node by node, so data can never become HTML (no `innerHTML` anywhere, D19).

## Functions

| Function | What it does |
|---|---|
| `createElement(tagName, className, text)` | New element with an optional class and optional text. |
| `createShape(shape)` | One SVG child (`path`, `circle`, `rect`) from `[tag, attributes]`. |
| `createIcon(name)` | A 24×24 SVG icon from `ICON_SHAPES` (`check`, `info`, `up`, `warn`, `chevron`, `copy`, `share`, `print`, `redo`, `edit`, `tune`, `flag`, `list`, `bulb`), hidden from screen readers. Unknown names give an empty SVG. |
| `createList(items, className, ordered)` | `<ul>` or `<ol>` with one `<li>` per text. |
| `createButton(className, iconName, label)` | A `type="button"` button with an optional icon and a label span. |
| `clearChildren(element)` | Removes all children. |

To add an icon: add its shapes to `ICON_SHAPES` (copy them from any 24×24 stroke icon set) and use its name.

## Relations

- Uses: nothing. Used by: all view modules ([`question-view`](question-view.md), [`progress-view`](progress-view.md), [`role-card`](role-card.md), [`results-view`](results-view.md), [`result-actions`](result-actions.md)) and [`finder`](finder.md).
