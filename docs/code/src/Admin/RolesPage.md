# `src/Admin/RolesPage.php`

**Purpose:** the careers list (`admin.php?page=itcp-roles`). Prepares one plain row per career and renders [`templates/admin/roles-list.php`](../../templates/admin/roles-list.md).

## Functions

| Function | What it does |
|---|---|
| `__construct( $roles, $schema )` | Stores the repository and the schema. |
| `url()` | Static: the page URL (used for "back to list" links). |
| `render()` | Returns early for users without the capability; otherwise renders the list with rows, deleted rows, the add URL and the shortcode tag. |
| `row_for( $role )` | `{ id, title_fa, title_en, category (Persian title), origin, edit_url }` for one career. Public because it is used as an `array_map` callback. |
| `deleted_row_for( $role )` | `{ id, title_fa }` for a deleted bundled career. Public for the same reason. |

`SLUG` = `itcp-roles` (also the parent menu slug).

## Relations

- Uses: [`RoleRepository`](../Data/RoleRepository.md), [`RoleSchema`](../Data/RoleSchema.md), [`RoleEditorPage::url()`](RoleEditorPage.md), [`Shortcode::TAG`](../Frontend/Shortcode.md), [`View`](../Support/View.md), [`RequestGuard`](RequestGuard.md).
- Used by: [`AdminMenu`](AdminMenu.md), [`RoleEditorPage`](RoleEditorPage.md) and actions (redirect target).
