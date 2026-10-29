# `tests/RoleRepositoryTest.php`

**Purpose:** proves the overlay logic of [`RoleRepository`](../src/Data/RoleRepository.md) (D6, D7) with the real `roles.json` and in-memory options.

## Functions

| Function | What it checks |
|---|---|
| `repository()` | Helper: repository over the real data and a fresh `RoleChanges`. |
| `bundled_count()` | Helper: number of careers in `roles.json`. |
| `custom_role()` | Helper: a minimal valid custom career. |
| `ids( $roles )` | Helper: list of ids. |
| `test_returns_every_bundled_role_without_changes()` | No changes → all bundled careers, origin `default`. |
| `test_edited_role_replaces_bundled_version_in_place()` | An edit keeps the career's position and shows `edited`. |
| `test_saving_bundled_content_again_returns_to_default()` | Saving the original content removes the stored copy. |
| `test_custom_role_is_appended_and_marked_custom()` | New careers come last with origin `custom`. |
| `test_deleting_bundled_role_hides_it_until_restored()` | Delete → hidden and listed as deleted; restore → back. |
| `test_deleting_custom_role_removes_it_completely()` | No "deleted" trace for custom careers. |
| `test_replace_with_keeps_only_given_roles()` | Replace keeps exactly the given list, unchanged bundled ones stay `default`. |
| `test_reset_forgets_every_change()` | Reset → bundled list again. |
