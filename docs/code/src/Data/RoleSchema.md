# `src/Data/RoleSchema.php`

**Purpose:** answers "what is a valid career?" from `vocabulary.json`: which categories, interest tags, workplaces and labels exist, and which rating fields a career has. Because it is data-driven, adding a category or workplace to the JSON makes it available in the admin form and accepted by the sanitizer without code changes.

## Functions

| Function | What it does |
|---|---|
| `__construct( $vocabulary )` | Stores the decoded `vocabulary.json`. |
| `categories()` | List of `{ id, title, description }`. |
| `category_ids()` | Just the ids. |
| `category_title( $category_id )` | Persian title of a category, or the id itself if unknown. |
| `interest_tags()` | List of `{ value, label }` interest tags. |
| `interest_tag_values()` | Just the values. |
| `workplace_types()` | List of `{ value, label }` workplaces. |
| `workplace_values()` | Just the values. |
| `role_labels()` | List of `{ value, label }` career labels. |
| `role_label_values()` | Just the values. |
| `rating_legend()` | Labels for 1–5 (`{"1": "کم", …}`) for the admin dropdowns. |
| `rating_fields()` | Single ratings: `{ key, label, help }` each. |
| `rating_keys()` | Just the keys of the single ratings. |
| `fit_groups()` | Grouped ratings (`work_mode_fit`, `client_type_fit`, `work_arrangement_fit`) with their options. |
| `fit_group_keys( $group )` | The option keys of one group, e.g. `remote`, `hybrid`, `onsite`. |
| `list_of( $key )` | A top-level list of the vocabulary, or `[]` if missing or not a list. |
| `values_of( $key, $field )` | One field of every item of a top-level list. |
| `string_column( $rows, $field )` | `array_column` that keeps only string values. |

## Relations

- Used by: [`RoleSanitizer`](RoleSanitizer.md) (allowed values), [`RolesPage`](../Admin/RolesPage.md) (category titles), [`RoleEditorPage`](../Admin/RoleEditorPage.md) and the template [`role-editor.php`](../../templates/admin/role-editor.md) (form fields).
- Reads: [`data/vocabulary.json`](../../data/vocabulary.md).
