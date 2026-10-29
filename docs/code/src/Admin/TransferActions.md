# `src/Admin/TransferActions.php`

**Purpose:** handles export, import and reset, posted to `admin-post.php`. Every handler verifies capability + nonce first.

| Action (`action=`) | Handler | Result |
|---|---|---|
| `itcp_export_roles` | `export()` | Downloads `it-career-roles-YYYY-MM-DD.json` with `RoleRepository::all()` in the `roles.json` format |
| `itcp_import_roles` | `import()` | Merges or replaces careers from an uploaded `.json` file or pasted text |
| `itcp_reset_roles` | `reset()` | Deletes all admin changes |

## Functions

| Function | What it does |
|---|---|
| `__construct( $roles, $importer )` | Stores collaborators. |
| `register()` | Hooks the three `admin_post_<action>` handlers. |
| `export()` | Verifies, sends download headers, prints pretty JSON (readable Persian, unescaped slashes), exits. |
| `import()` | Verifies, reads the JSON (file first, then pasted text), imports in the chosen mode, redirects with `imported` (+ counts) or `import_failed`. |
| `reset()` | Verifies, clears changes, redirects to the list with `reset`. |
| `send_download_headers()` | `nocache_headers()`, `Content-Type: application/json`, `Content-Disposition: attachment`, `X-Content-Type-Options: nosniff`. |
| `submitted_json()` | Uploaded file contents if any, otherwise the pasted text. |
| `uploaded_file_contents()` | Reads the uploaded temp file when it is acceptable; never moves or keeps it. |
| `is_acceptable_upload( $file )` | No upload error, a real upload (`is_uploaded_file`), at most 2 MB, name ending in `.json`. |
| `pasted_json()` | The `itcp_json` textarea (unslashed, string only, at most 2 MB). |
| `submitted_mode()` | `replace` only when explicitly chosen, otherwise `merge`. |

## Relations

- Uses: [`RequestGuard`](RequestGuard.md), [`RoleRepository`](../Data/RoleRepository.md), [`RoleImporter`](../Data/RoleImporter.md), [`Notices`](Notices.md), [`TransferPage::SLUG`](TransferPage.md), [`RolesPage::SLUG`](RolesPage.md).
- Forms that post here: [`transfer.php`](../../templates/admin/transfer.md).
