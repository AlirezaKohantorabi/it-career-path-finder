# `templates/admin/rating-select.php`

**Purpose:** one `<select>` with the options 1–5, each labelled with the rating legend ("۳ — متوسط/وابسته به شرایط").

**Receives** (`$view`): `id`, `name`, `value` (selected rating), `legend` (`RoleSchema::rating_legend()`).

The range comes from `RoleSanitizer::RATING_MIN` / `RATING_MAX`, so the form and the sanitizer can never disagree.

## Relations

- Rendered by: [`role-editor.php`](role-editor.md) (many times). Uses constants of [`RoleSanitizer`](../../src/Data/RoleSanitizer.md).
- Has no functions.
