# `tests/VersionTest.php`

**Purpose:** the version number must match in four places (see [CHANGELOG.md](../../../CHANGELOG.md)); this test fails as soon as one is forgotten.

## Functions

| Function | What it does |
|---|---|
| `read( $file )` | Helper: file contents from the repository root. |
| `header_version()` | Helper: `Version:` from the plugin header. |
| `test_constant_matches_plugin_header()` | `ITCP_VERSION` = header version. |
| `test_readme_stable_tag_matches_plugin_header()` | `Stable tag:` in `readme.txt` = header version. |
| `test_changelog_mentions_current_version()` | `CHANGELOG.md` contains `[<version>]`. |
