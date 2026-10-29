# `src/Admin/RoleActions.php`

**Purpose:** handles the career forms posted to `admin-post.php`: save, delete, restore. Each handler verifies capability + nonce first, changes data through [`RoleRepository`](../Data/RoleRepository.md), then redirects with a notice.

| Action (`action=`) | Nonce action | Handler |
|---|---|---|
| `itcp_save_role` | `itcp_save_role` | `save()` |
| `itcp_delete_role` | `itcp_delete_role_<role id>` | `delete()` |
| `itcp_restore_role` | `itcp_restore_role_<role id>` | `restore()` |

Delete/restore nonces include the role id, so a nonce taken from one row cannot act on another career.

## Functions

| Function | What it does |
|---|---|
| `__construct( $roles, $sanitizer )` | Stores collaborators. |
| `nonce_action_for( $action, $role_id )` | Static: builds the per-career nonce action; also used by the list template. |
| `register()` | Hooks the three `admin_post_<action>` handlers (no `nopriv` versions: logged-out users are rejected). |
| `save()` | Verifies, sanitizes the posted `role[…]` fields, validates; on errors keeps a draft and redirects to the editor with `invalid`; otherwise saves and redirects to the list with `saved`. |
| `delete()` | Verifies with the per-career nonce; `missing` if the career does not exist; otherwise deletes and redirects with `deleted`. |
| `restore()` | Verifies with the per-career nonce; only for bundled careers; restores the bundled version and redirects with `restored`. |
| `posted_role( $original_id )` | The posted `role` array (unslashed); when editing, forces `id` to the original id so an id can never be changed. |
| `posted_key( $field )` | A posted string field through `sanitize_key()`. |
| `validation_errors( $role, $original_id )` | Missing required fields, plus `duplicate_id` (new career whose id is taken) and `missing_role` (edited career no longer exists). |
| `is_taken( $role_id )` | The id belongs to an existing career or to a (possibly deleted) bundled career. |
| `editor_arguments( $original_id )` | Extra redirect arguments to return to the same career's editor. |

## Relations

- Uses: [`RequestGuard`](RequestGuard.md), [`RoleSanitizer`](../Data/RoleSanitizer.md), [`RoleRepository`](../Data/RoleRepository.md), [`Drafts`](Drafts.md), [`Notices`](Notices.md).
- Forms that post here: [`role-editor.php`](../../templates/admin/role-editor.md), [`roles-list.php`](../../templates/admin/roles-list.md).
