# `src/Admin/RequestGuard.php`

**Purpose:** one place for "is this user allowed, and did this request really come from our form?". Every admin page and action goes through it (D19).

## Functions

| Function | What it does |
|---|---|
| `capability()` | The capability needed: `manage_options` by default, changeable with the `itcp_manage_capability` filter (e.g. to let editors manage careers). |
| `current_user_can_manage()` | Whether the current user has that capability. Pages call this before rendering. |
| `verify_action( $nonce_action )` | For form actions: stops with a 403 page if the user lacks the capability, then `check_admin_referer( $nonce_action )`, which stops with "link expired" if the nonce is missing or wrong. |

## Relations

- Used by: [`AdminMenu`](AdminMenu.md) (menu capability), every page ([`RolesPage`](RolesPage.md), [`RoleEditorPage`](RoleEditorPage.md), [`TransferPage`](TransferPage.md), [`AboutPage`](AboutPage.md)) and every action ([`RoleActions`](RoleActions.md), [`TransferActions`](TransferActions.md)).
