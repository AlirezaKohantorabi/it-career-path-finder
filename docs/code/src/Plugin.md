# `src/Plugin.php`

**Purpose:** the composition root. It creates every module with its dependencies and hooks them into WordPress. Nothing else in the plugin calls `new` on services.

## Functions

| Function | What it does |
|---|---|
| `boot()` | Static entry point called by the main file; schedules `register()` on `plugins_loaded`. |
| `register()` | Hooks `load_translations` on `init`, creates `BundledData` and `RoleRepository`, registers the frontend, and the admin when `is_admin()` (also true for `admin-post.php`). |
| `load_translations()` | Loads admin-string translations from `languages/` (`it-career-path-finder` text domain). |
| `data_directory()` | Folder of the JSON files: `data/`, changeable with the `itcp_data_directory` filter. |
| `register_frontend( $bundled, $roles )` | Creates `Assets`, `DatasetPrinter` (with `FrontendDataset`) and `Shortcode`, and registers their hooks. |
| `register_admin( $bundled, $roles )` | Creates `RoleSchema`, `RoleSanitizer`, the four admin pages, `AdminMenu`, `AdminAssets`, `RoleActions`, `TransferActions` (with `RoleImporter`) and registers their hooks. |

`TEXT_DOMAIN` holds the text domain string.

## Object graph

```
BundledData ─┬─ RoleRepository ── RoleChanges
             │        │
             │        ├─ FrontendDataset ── DatasetPrinter
             │        ├─ RolesPage, RoleEditorPage, RoleActions, RoleImporter, TransferActions
             ├─ Shortcode (also Assets, DatasetPrinter)
             └─ RoleSchema ── RoleSanitizer (used by RoleEditorPage, RoleActions, RoleImporter)
```

## Relations

- Used by: [`it-career-path-finder.php`](../it-career-path-finder.md).
- Uses: every class in `src/Data`, `src/Frontend` and `src/Admin`.
