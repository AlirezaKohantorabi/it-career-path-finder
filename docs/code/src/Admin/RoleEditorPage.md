# `src/Admin/RoleEditorPage.php`

**Purpose:** the add/edit career page (`admin.php?page=itcp-role-edit`, with `&role=<id>` to edit). Picks what to show in the form and renders [`templates/admin/role-editor.php`](../../templates/admin/role-editor.md).

What the form shows, in order of priority:

1. A **draft** from [`Drafts`](Drafts.md) (the last invalid submission, with its errors);
2. the stored career when `role=<id>` is given;
3. an empty career (`RoleSanitizer::sanitize( [] )`: blank texts, all ratings 3).

## Functions

| Function | What it does |
|---|---|
| `__construct( $roles, $schema, $sanitizer )` | Stores collaborators. |
| `url( $role_id )` | Static: the page URL, with `role` when editing. |
| `render()` | Returns early without the capability; renders the form with `role`, `errors`, `is_new`, `missing` (an id was asked for but not found), `schema` and `list_url`. |
| `requested_role_id()` | The `role` query value through `sanitize_key()`. |

`SLUG` = `itcp-role-edit`.

## Relations

- Uses: [`RoleRepository`](../Data/RoleRepository.md), [`RoleSchema`](../Data/RoleSchema.md), [`RoleSanitizer`](../Data/RoleSanitizer.md), [`Drafts`](Drafts.md), [`RolesPage::url()`](RolesPage.md), [`View`](../Support/View.md), [`RequestGuard`](RequestGuard.md).
- The form posts to [`RoleActions::save()`](RoleActions.md).
