# `templates/admin/transfer.php`

**Purpose:** three forms on one page, each with its own nonce:

1. **برون‌بری** — POST `itcp_export_roles` → downloads the JSON.
2. **درون‌ریزی** — `multipart/form-data` POST `itcp_import_roles` with a file field `itcp_file`, a textarea `itcp_json`, and a mode radio `itcp_mode` (`merge` default, or `replace`).
3. **بازگشت به حالت پیش‌فرض** — POST `itcp_reset_roles`, with a `data-itcp-confirm` question.

**Receives:** nothing (`$view` is empty).

## Relations

- Rendered by: [`TransferPage`](../../src/Admin/TransferPage.md). Posts to: [`TransferActions`](../../src/Admin/TransferActions.md). Uses `RoleImporter::MODE_*` constants. Shows [`Notices`](../../src/Admin/Notices.md).
- Has no functions.
