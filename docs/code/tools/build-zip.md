# `tools/build-zip.php`

**Purpose:** builds the install-ready `dist/it-career-path-finder-<version>.zip` (D24). Run from the repository root:

```bash
php tools/build-zip.php
```

The zip contains one folder, `it-career-path-finder/`, with only what WordPress needs: the main file, `uninstall.php`, `index.php`, `readme.txt`, `README.md`, `CHANGELOG.md`, `LICENSE`, and the `assets`, `data`, `languages`, `src`, `templates` folders. Tests, docs, tools and Git files are left out. Paths inside the zip always use `/`, because zips made on Windows with `\` fail to install in WordPress.

## Functions

| Function | What it does |
|---|---|
| `itcp_build_root()` | Repository root (parent of `tools/`). |
| `itcp_build_version()` | `Version:` from the plugin header, used in the file name. |
| `itcp_build_files_in( $relative )` | A file, or every file inside a folder, as relative `/` paths. |
| `itcp_build_files()` | All files of `ITCP_BUILD_ENTRIES`, sorted; fails if an entry is missing. |
| `itcp_build_zip( $target, $files )` | Writes the zip with every file under `it-career-path-finder/`. |
| `itcp_build_main()` | Checks the `zip` extension, creates `dist/`, builds, prints the result; returns the exit code. |

## Relations

- Run by: developers and both GitHub workflows (`.github/workflows/ci.yml`, `release.yml`).
- Needs the PHP `zip` extension (L11).
