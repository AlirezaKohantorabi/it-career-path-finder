# `src/Admin/AdminMenu.php`

**Purpose:** adds the **مسیر شغلی IT** menu (icon `dashicons-welcome-learn-more`, position 58) with four sub-pages, all requiring `RequestGuard::capability()`.

| Menu item | Slug | Page class |
|---|---|---|
| همه شغل‌ها (also the top item) | `itcp-roles` | [`RolesPage`](RolesPage.md) |
| افزودن شغل | `itcp-role-edit` | [`RoleEditorPage`](RoleEditorPage.md) |
| درون‌ریزی و برون‌بری | `itcp-transfer` | [`TransferPage`](TransferPage.md) |
| راهنما و سازندگان | `itcp-about` | [`AboutPage`](AboutPage.md) |

## Functions

| Function | What it does |
|---|---|
| `__construct( $roles_page, $editor_page, $transfer_page, $about_page )` | Stores the four pages. |
| `register()` | Hooks `add_pages` on `admin_menu`. |
| `add_pages()` | Adds the top menu and the four sub-pages. |
| `add_subpage( $slug, $title, $callback )` | Adds one sub-page under the top menu with the shared capability. |

## Relations

- Uses: the four page classes and [`RequestGuard`](RequestGuard.md). Created by [`Plugin`](../Plugin.md).
