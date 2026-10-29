# `src/Data/RoleChanges.php`

**Purpose:** stores admin edits in a single WordPress option, `itcp_role_changes` (not autoloaded), shaped as:

```php
array(
	'saved'   => array( 'role_id' => array( …career… ) ),
	'deleted' => array( 'bundled_role_id', … ),
)
```

It knows nothing about bundled careers; [`RoleRepository`](RoleRepository.md) decides what each change means (D6).

## Functions

| Function | What it does |
|---|---|
| `saved_roles()` | Map of id → saved career (edited bundled careers and custom careers). |
| `deleted_ids()` | Ids of bundled careers the admin deleted. |
| `save_role( $role )` | Stores the career under its id and removes the id from `deleted`. |
| `forget_role( $role_id )` | Removes a saved career. |
| `mark_deleted( $role_id )` | Removes any saved version and adds the id to `deleted` (once). |
| `unmark_deleted( $role_id )` | Removes the id from `deleted`. |
| `replace_all( $saved_roles, $deleted_ids )` | Overwrites both lists (used by "replace" import). |
| `clear()` | Deletes the option (used by "reset to defaults"). |
| `read()` | Loads the option and repairs its shape, so a damaged option never breaks the site. |
| `write( $changes )` | Saves the option with autoload off. |
| `without( $role_ids, $role_id )` | The list without one id. |

## Relations

- Used by: [`RoleRepository`](RoleRepository.md) only. [`uninstall.php`](../../uninstall.md) deletes the same option by name.
