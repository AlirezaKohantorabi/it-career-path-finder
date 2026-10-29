# `templates/admin/about.php`

**Purpose:** the help page: how to place the shortcode (block editor, classic editor), how to change title/subtitle per page, a note on fonts and privacy, the authors with links, the plugin version and the source-code link.

**Receives** (`$view`): `shortcode`, `version`, `repository`, `developers` (list of `{ name, website, github }`).

External links open in a new tab with `rel="noopener noreferrer"`.

## Relations

- Rendered by: [`AboutPage`](../../src/Admin/AboutPage.md).
- Has no functions.
