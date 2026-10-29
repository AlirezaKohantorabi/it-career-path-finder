# `src/Data/RoleRepository.php`

**Purpose:** the single answer to "which careers exist right now?". It overlays admin edits ([`RoleChanges`](RoleChanges.md)) on the bundled careers ([`BundledData::roles()`](BundledData.md)), see D6:

1. Bundled careers in file order, skipping deleted ids, each replaced by its saved version if one exists.
2. Then custom careers (saved careers whose id is not bundled), in the order they were added.

## Functions

| Function | What it does |
|---|---|
| `__construct( $bundled, $changes )` | Stores both sources. |
| `all()` | The effective list described above. Sent to the browser and shown in the admin list. |
| `find( $role_id )` | One effective career, or `null`. |
| `exists( $role_id )` | Whether an effective career has this id. |
| `is_bundled( $role_id )` | Whether `roles.json` contains this id (even if deleted). |
| `origin_of( $role_id )` | `default`, `edited` (bundled with a saved version) or `custom` (not bundled). |
| `deleted_defaults()` | Bundled careers the admin deleted, for the "restore" list. |
| `save( $role )` | Saves a career; if it is identical to its bundled version, removes the saved copy instead (D7). |
| `delete( $role_id )` | Bundled → mark deleted; custom → forget it completely. |
| `restore_default( $role_id )` | Forgets the saved version and un-deletes, so the bundled version shows again. |
| `merge( $roles )` | Saves each career (import "merge" mode). |
| `replace_with( $roles )` | Keeps only the given careers: stores changed/custom ones and marks every other bundled id deleted (import "replace" mode). |
| `reset()` | Clears all changes. |
| `effective_bundled_roles()` | Step 1 of `all()`. |
| `custom_roles()` | Step 2 of `all()`; casts array keys to strings (L7). |
| `bundled_role( $role_id )` | The bundled version of a career, or `null`. |
| `matches_bundled_version( $role )` | Whether a career equals its bundled version (loose `==`, key order ignored). |
| `bundled_ids_missing_from( $roles )` | Bundled ids that do not appear in a list (to mark deleted on replace). |

Constants `ORIGIN_DEFAULT`, `ORIGIN_EDITED`, `ORIGIN_CUSTOM` are used by the list template for badges.

## Relations

- Uses: [`BundledData`](BundledData.md), [`RoleChanges`](RoleChanges.md).
- Used by: [`FrontendDataset`](../Frontend/FrontendDataset.md), [`RolesPage`](../Admin/RolesPage.md), [`RoleEditorPage`](../Admin/RoleEditorPage.md), [`RoleActions`](../Admin/RoleActions.md), [`RoleImporter`](RoleImporter.md), [`TransferActions`](../Admin/TransferActions.md).
