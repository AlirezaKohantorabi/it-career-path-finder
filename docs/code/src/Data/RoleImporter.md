# `src/Data/RoleImporter.php`

**Purpose:** imports careers from JSON text (an uploaded file or pasted text). Accepts the export format `{ "roles": [ … ] }` and a plain list `[ … ]`. Every career is sanitized; incomplete or duplicate ones are skipped. If nothing valid remains, **nothing changes** — so a wrong file can never wipe the list in "replace" mode.

## Functions

| Function | What it does |
|---|---|
| `__construct( $sanitizer, $roles )` | Stores the sanitizer and the repository. |
| `import( $json, $mode )` | Parses, cleans and applies the careers with `merge` or `replace`; returns `[ 'imported' => n, 'skipped' => m ]`, or `null` when nothing valid was found. |
| `role_candidates( $json )` | Decodes the text and returns the list of career objects (at most 500), or `[]` if it is not a list. |
| `valid_roles( $candidates )` | Sanitizes each candidate and keeps complete ones, first occurrence of each id wins. |
| `is_complete( $role )` | No required field is missing. |

Constants: `MODE_MERGE`, `MODE_REPLACE` (also used by the transfer template and `TransferActions`), `MAX_ROLES`.

## Relations

- Uses: [`JsonFile`](JsonFile.md), [`RoleSanitizer`](RoleSanitizer.md), [`RoleRepository`](RoleRepository.md).
- Used by: [`TransferActions`](../Admin/TransferActions.md).
