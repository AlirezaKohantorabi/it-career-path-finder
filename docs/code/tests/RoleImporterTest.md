# `tests/RoleImporterTest.php`

**Purpose:** proves that [`RoleImporter`](../src/Data/RoleImporter.md) accepts the export format and plain lists, skips bad entries, and never changes anything when the input is useless.

## Functions

| Function | What it checks |
|---|---|
| `set_up()` | Builds a repository and importer over the real data. |
| `new_role( $role_id )` | Helper: a minimal valid career. |
| `test_merges_roles_from_export_document()` | `{ "roles": [...] }` is merged; bundled careers stay. |
| `test_accepts_a_plain_list_of_roles()` | `[ ... ]` works too. |
| `test_skips_invalid_and_duplicate_roles()` | 1 imported, 3 skipped (incomplete, not an object, duplicate id). |
| `test_replace_mode_keeps_only_imported_roles()` | Replace leaves only the imported careers. |
| `test_changes_nothing_when_no_role_is_valid()` | Invalid careers, non-JSON text and a single object all return `null` and leave the list as it was. |
