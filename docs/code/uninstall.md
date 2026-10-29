# `uninstall.php`

**Purpose:** WordPress runs this file when an admin **deletes** the plugin (not when it is deactivated). It removes everything the plugin stored in the database.

## Functions

| Function | What it does |
|---|---|
| `itcp_delete_site_data()` | Deletes the `itcp_role_changes` option (admin edits) of the current site. |
| `itcp_delete_network_data()` | On multisite, switches to every site (`get_sites( number => 0 )`), runs `itcp_delete_site_data()`, and switches back. |

The file then calls `itcp_delete_network_data()` on multisite or `itcp_delete_site_data()` otherwise.

## Notes

- Guarded by `defined( 'WP_UNINSTALL_PLUGIN' ) || exit;` instead of `ABSPATH`, as WordPress requires for uninstall files.
- Draft transients (`itcp_draft_<user>`) are not deleted; they expire after 10 minutes on their own.
- Functions are global and prefixed `itcp_` because this file runs without the plugin's autoloader.

## Relations

- Mirrors the option name in [`src/Data/RoleChanges.php`](src/Data/RoleChanges.md) (`RoleChanges::OPTION_NAME`). If that name changes, change it here too.
