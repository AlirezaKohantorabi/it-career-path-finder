# `assets/js/admin.js`

**Purpose:** small admin-only behaviour on the plugin's pages (loaded by [`AdminAssets`](../../src/Admin/AdminAssets.md)). It does not use the `ItcpFinder` namespace and the site never loads it.

- Forms with `data-itcp-confirm="question"` ask that question before submitting (delete, restore, reset).
- An input with `data-itcp-filter="<table id>"` filters that table's `[data-itcp-row]` rows as you type.

## Functions

| Function | What it does |
|---|---|
| `normalized(text)` | Lowercases and turns Arabic `ي`/`ك` into Persian `ی`/`ک`, so searching works whichever keyboard typed the text. |
| `confirmBeforeSubmit(form)` | Cancels the submit when the visitor answers "Cancel". |
| `filterRows(input)` | Hides rows that do not contain the search text. |
| `bindFilter(input)` | Runs `filterRows` on every keystroke. |
| `start()` | Wires every confirm form and filter input on the page. |

## Relations

- Markup: [`roles-list.php`](../../templates/admin/roles-list.md), [`transfer.php`](../../templates/admin/transfer.md).
