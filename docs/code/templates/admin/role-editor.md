# `templates/admin/role-editor.php`

**Purpose:** the add/edit career form.

**Receives** (`$view`): `role` (a full sanitized career), `errors` (keys from the last failed save), `is_new`, `missing`, `schema` ([`RoleSchema`](../../src/Data/RoleSchema.md)), `list_url`.

## Sections

1. **اطلاعات اصلی** — id (editable only for new careers; shown as code when editing, with a hidden `original_id`), Persian and English title, category dropdown, and **این شغل به زبان ساده** (`plain_description`, required) with guidance on writing it for non-IT readers.
2. **سطح‌ها و امتیازها** — one [`rating-select`](rating-select.md) per single rating and per option of every fit group, with the help text from `vocabulary.json`.
3. **علاقه‌ها، محیط کار و برچسب‌ها** — three [`checkbox-group`](checkbox-group.md)s.
4. **مهارت‌ها و راه ورود** — skills (one per line), entry route, market caveat.

Field names follow the career shape (`role[ratings_1_to_5][work_mode_fit][remote]`, `role[interest_tags][]`, …) so `$_POST['role']` can go straight into `RoleSanitizer::sanitize()`.

If `missing` is true (the requested id does not exist), only an error and a back link are shown. Errors are listed with human labels (`$itcp_error_labels`). HTML5 `required`/`pattern` give quick feedback, but the server checks everything again.

## Relations

- Rendered by: [`RoleEditorPage`](../../src/Admin/RoleEditorPage.md). Posts to: [`RoleActions::save()`](../../src/Admin/RoleActions.md). Uses partials through [`View::display()`](../../src/Support/View.md).
- Has no functions.
