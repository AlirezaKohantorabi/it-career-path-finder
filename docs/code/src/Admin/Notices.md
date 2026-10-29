# `src/Admin/Notices.php`

**Purpose:** after an action, redirect back to a page with a message key in the URL (`itcp_notice=saved`), and on that page show the matching message. Only keys from a fixed list are shown, so the URL can never inject text (D19).

## Functions

| Function | What it does |
|---|---|
| `redirect( $page, $notice, $arguments )` | Redirects to `admin.php?page=<page>&itcp_notice=<notice>` plus extra arguments, with `wp_safe_redirect()`, then exits. |
| `render()` | Prints the notice for the current `itcp_notice` key, if it is a known key. |
| `current_notice_key()` | The `itcp_notice` query value through `sanitize_key()`. |
| `query_number( $key )` | A number from the URL through `absint()` (for import counts). |
| `with_counts( $message )` | Replaces `{imported}` and `{skipped}` with localized numbers. |
| `messages()` | The fixed list: `saved`, `deleted`, `restored`, `invalid`, `missing`, `imported`, `import_failed`, `reset`, each with a type (`success`/`error`) and a translatable text. |

## Relations

- Used by: [`RoleActions`](RoleActions.md) and [`TransferActions`](TransferActions.md) (`redirect`); templates [`roles-list.php`](../../templates/admin/roles-list.md), [`role-editor.php`](../../templates/admin/role-editor.md), [`transfer.php`](../../templates/admin/transfer.md) (`render`).
