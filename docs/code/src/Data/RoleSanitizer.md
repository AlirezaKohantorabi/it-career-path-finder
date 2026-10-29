# `src/Data/RoleSanitizer.php`

**Purpose:** the security gate for career data. Takes **any** input (admin form, imported JSON, empty array) and returns a career with exactly the documented fields, clean values and ratings 1–5. Nothing is stored without passing through `sanitize()`.

## Functions

| Function | What it does |
|---|---|
| `__construct( $schema )` | Stores the [`RoleSchema`](RoleSchema.md) that lists allowed values. |
| `sanitize( $input )` | Builds a clean career from `$input`; unknown fields are dropped, missing ones get empty/neutral values. `sanitize( [] )` is an empty career for the "add" form. |
| `missing_required_fields( $role )` | Names of required fields (`id`, `title_fa`, `category_id`, `plain_description`) that are empty. |
| `clean_id( $value )` | `sanitize_key()` (lowercase `a-z0-9_-`) limited to 64 characters. |
| `clean_line( $value )` | `sanitize_text_field()` limited to 200 characters. |
| `clean_paragraph( $value )` | `sanitize_textarea_field()` (keeps line breaks) limited to 2000 characters. |
| `clean_choice( $value, $allowed )` | The value if it is in `$allowed`, otherwise `''`. |
| `clean_choices( $values, $allowed )` | Allowed values only, without duplicates, in input order. |
| `clean_lines( $values )` | Accepts a list or text with one item per line; cleans each, drops empty ones, keeps at most 20. |
| `clean_ratings( $input )` | Every single rating and every fit group listed in the schema, each cleaned. |
| `clean_fit_group( $input, $keys )` | One rating per key of a fit group. |
| `clean_rating( $value )` | Number rounded and clamped to 1–5; non-numbers become 3. |
| `as_string( $value )` | Trimmed string for scalars, `''` for arrays/objects/null. |
| `as_list( $value )` | Array values re-indexed, or `[]`. |
| `as_map( $value )` | The array, or `[]`. |
| `limit( $text, $max_length )` | Multibyte-safe cut (Persian characters count as one). |

Public constants `RATING_MIN`, `RATING_MAX`, `RATING_NEUTRAL` are also used by the rating dropdown template.

## Relations

- Uses: [`RoleSchema`](RoleSchema.md); WordPress `sanitize_*` functions.
- Used by: [`RoleActions`](../Admin/RoleActions.md) (admin form), [`RoleImporter`](RoleImporter.md) (JSON import), [`RoleEditorPage`](../Admin/RoleEditorPage.md) (empty career), [`rating-select.php`](../../templates/admin/rating-select.md) (constants).

## Notes

`tests/RoleSanitizerTest.php` checks that every bundled career passes through `sanitize()` unchanged — if you add a field to `roles.json`, add it here too or it will be dropped on the next admin save.
