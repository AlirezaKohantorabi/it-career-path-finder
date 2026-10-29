# `assets/js/scroll.js` → `ItcpFinder.Scroll`

**Purpose:** scrolls so an element appears just **below** the theme's sticky/fixed header (many themes have one), instead of hiding under it. Ported from the original tool.

How the header height is found: look at the elements at the top-centre of the screen (`elementsFromPoint`), walk up their parents, and take the lowest bottom edge of any `fixed`/`sticky` element touching the top; cap it at 180 px and add a 12 px gap.

## Functions

| Function | What it does |
|---|---|
| `prefersReducedMotion()` | Whether the visitor asked the OS for less motion; then scrolling jumps instead of animating. |
| `isPinned(element)` | `position` is `fixed` or `sticky`. |
| `isPageBoundary(node, root)` | Stop walking up at `body`, `html` or the tool itself (the tool's own sticky footer must not count). |
| `pinnedBottomAbove(element, root)` | Lowest bottom edge of pinned ancestors touching the top of the screen. |
| `measurePinnedHeaderHeight(root)` | Height of the theme's pinned header (0 when there is none or the API is missing). |
| `headerOffset(root)` | Header height (max 180) + 12 px. |
| `isComfortablyVisible(rect, offset)` | The element's top is already below the header and in the top 40% of the screen. |
| `currentScrollTop()` | Current vertical scroll position. |
| `scrollWindowTo(top)` | Smooth scroll to a position, falling back to an instant jump in old browsers. |
| `scrollToElement(element, root, onlyWhenHidden)` | Scrolls the element to just below the header; with `onlyWhenHidden`, does nothing if it is already comfortably visible. |
| `centerHorizontally(container, child)` | Scrolls a horizontal strip (the question dots on mobile) so the child is centred. |

## Relations

- Uses: [`format`](format.md) (`safely`). Used by: [`question-view`](question-view.md), [`progress-view`](progress-view.md), [`results-view`](results-view.md), [`finder`](finder.md).
