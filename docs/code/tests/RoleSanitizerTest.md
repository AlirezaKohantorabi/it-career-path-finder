# `tests/RoleSanitizerTest.php`

**Purpose:** proves that [`RoleSanitizer`](../src/Data/RoleSanitizer.md) cleans hostile or sloppy input and keeps good data intact.

## Functions

| Function | What it checks |
|---|---|
| `sanitizer()` | Helper: a sanitizer built from the real `vocabulary.json`. |
| `test_strips_html_and_unsafe_characters()` | `My Role<script>` → `myrolescript`; tags removed from titles and descriptions; line breaks kept. |
| `test_drops_unknown_category_tags_workplaces_and_labels()` | Values not in the vocabulary disappear; duplicates removed. |
| `test_clamps_ratings_and_fills_missing_with_neutral()` | 9 → 5, −4 → 1, text → 3, 4.6 → 5, missing → 3, in single ratings and fit groups. |
| `test_splits_skill_text_into_clean_lines()` | Text with CRLF, blank lines and tags → clean list. |
| `test_limits_text_length()` | Titles stop at 200 characters (multibyte-safe). |
| `test_reports_missing_required_fields()` | `id`, `category_id`, `plain_description` reported when empty. |
| `test_bundled_roles_survive_sanitizing_unchanged()` | Every career in `roles.json` is equal after sanitizing (guards against adding a JSON field the sanitizer would drop). |
