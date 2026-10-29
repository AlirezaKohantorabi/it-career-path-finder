# `src/Admin/TransferPage.php`

**Purpose:** the import/export page (`admin.php?page=itcp-transfer`). It only checks the capability and renders [`templates/admin/transfer.php`](../../templates/admin/transfer.md); the forms post to [`TransferActions`](TransferActions.md).

## Functions

| Function | What it does |
|---|---|
| `render()` | Renders the page for users with the capability. |

`SLUG` = `itcp-transfer`.

## Relations

- Uses: [`RequestGuard`](RequestGuard.md), [`View`](../Support/View.md). Used by [`AdminMenu`](AdminMenu.md); redirect target of [`TransferActions`](TransferActions.md).
