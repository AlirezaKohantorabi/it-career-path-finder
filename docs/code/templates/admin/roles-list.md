# `templates/admin/roles-list.php`

**Purpose:** the careers table in wp-admin.

**Receives** (`$view`): `rows` (from `RolesPage::row_for()`), `deleted_rows`, `add_url`, `shortcode`.

## What it shows

- Heading with an "افزودن شغل" button, the current notice (`Notices::render()`), and the shortcode to copy.
- A search box (`data-itcp-filter="itcp-roles-table"`) and the number of careers; [`admin.js`](../../assets/js/admin.md) filters rows (`data-itcp-row`) as you type.
- One row per career: Persian title (links to edit), English title, category, id, an origin badge (پیش‌فرض / ویرایش‌شده / اضافه‌شده) and actions:
  - **ویرایش** → editor link;
  - **بازگردانی پیش‌فرض** (only for edited bundled careers) → POST `itcp_restore_role`;
  - **حذف** → POST `itcp_delete_role`.
  Both forms carry the per-career nonce from `RoleActions::nonce_action_for()` and a `data-itcp-confirm` question.
- If bundled careers were deleted, a second table lists them with a **برگرداندن** button.

Nonce fields are printed with the referer field off (L8). Every value is escaped.

## Relations

- Rendered by: [`RolesPage`](../../src/Admin/RolesPage.md). Posts to: [`RoleActions`](../../src/Admin/RoleActions.md). Uses constants of [`RoleRepository`](../../src/Data/RoleRepository.md). Styled by [`admin.css`](../../assets/css/admin.md).
- Has no functions.
