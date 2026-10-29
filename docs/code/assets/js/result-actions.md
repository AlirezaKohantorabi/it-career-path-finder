# `assets/js/result-actions.js` → `ItcpFinder.ResultActions`

**Purpose:** the action bar under the results: copy a text summary, share (on devices with `navigator.share`), print / save as PDF, restart.

**Printing:** to print only the tool, every sibling of the tool and of each of its ancestors gets the class `itcp-print-hide` (hidden by `@media print`), collapsed sections are opened, then `window.print()` runs. Everything is restored after printing (`afterprint`, or the next click/focus for browsers that don't fire it).

## Functions

| Function | What it does |
|---|---|
| `pageAddress()` | Current page URL without `#…`. |
| `summaryLines(finder, ranking)` | Lines of the text summary (recommended, then compromise careers with scores). |
| `summaryText(finder, ranking)` | The summary plus the page URL. |
| `copyWithTextarea(text)` | Old-browser copy via a hidden textarea (class `itcp-copy-buffer`) and `execCommand('copy')`. |
| `copyText(finder, text)` | Clipboard API on secure pages, textarea fallback otherwise; shows a toast either way. |
| `shareSummary(finder, ranking)` | Opens the system share sheet. |
| `hideSiblingsForPrint(root)` | Hides everything except the tool for printing; returns what was hidden. |
| `openClosedDetails(container)` | Opens collapsed card sections for printing; returns which. |
| `restoreAfterPrint(hidden, opened)` | Undoes both. |
| `printResults(finder)` | The full print flow. |
| `addAction(bar, className, iconName, label, onClick)` | Adds one button to the bar. |
| `buildActionBar(finder, ranking)` | **Public.** The bar. |

`summaryText` is also public.

## Relations

- Uses: [`format`](format.md), [`dom`](dom.md). Used by: [`results-view`](results-view.md); calls `finder.showToast()` and `finder.restart()`.
