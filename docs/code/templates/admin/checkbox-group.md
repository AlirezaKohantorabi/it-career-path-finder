# `templates/admin/checkbox-group.php`

**Purpose:** a responsive grid of checkboxes inside a `<fieldset>` for one multi-value career field.

**Receives** (`$view`): `name` (e.g. `role[interest_tags]`; `[]` is appended), `legend` (screen-reader title), `options` (`{ value, label }` list from the vocabulary), `selected` (list of checked values).

## Relations

- Rendered by: [`role-editor.php`](role-editor.md) for interest tags, workplaces and labels. Styled by `.itcp-checkbox-group` in [`admin.css`](../../assets/css/admin.md).
- Has no functions.
