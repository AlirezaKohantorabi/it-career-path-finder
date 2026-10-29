# `src/Admin/AdminAssets.php`

**Purpose:** loads [`admin.css`](../../assets/css/admin.md) and [`admin.js`](../../assets/js/admin.md) only on the plugin's own admin pages, so other admin screens are untouched.

## Functions

| Function | What it does |
|---|---|
| `register()` | Hooks `enqueue` on `admin_enqueue_scripts`. |
| `enqueue()` | Enqueues the admin style and script (handle `itcp-admin`) when on a plugin page. |
| `is_plugin_page()` | Whether the `page` query value (through `sanitize_key`) is one of the plugin's slugs. |
| `plugin_pages()` | The four slugs from the page classes. |

## Relations

- Uses: the `SLUG` constants of [`RolesPage`](RolesPage.md), [`RoleEditorPage`](RoleEditorPage.md), [`TransferPage`](TransferPage.md), [`AboutPage`](AboutPage.md). Created by [`Plugin`](../Plugin.md).
