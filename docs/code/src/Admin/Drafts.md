# `src/Admin/Drafts.php`

**Purpose:** when a career form is submitted with missing fields, the action redirects back to the editor. A draft keeps what the admin typed (already sanitized) and the list of errors for that one page load, so nothing has to be typed again.

Stored as the transient `itcp_draft_<user id>` for at most 10 minutes; read once.

## Functions

| Function | What it does |
|---|---|
| `keep( $role, $errors )` | Saves the sanitized career and the error keys for the current user. |
| `take()` | Returns the draft (`[ 'role' => …, 'errors' => … ]`) and deletes it, or `null`. |
| `key()` | Transient name for the current user. |

## Relations

- Used by: [`RoleActions::save()`](RoleActions.md) (`keep`), [`RoleEditorPage::render()`](RoleEditorPage.md) (`take`).
