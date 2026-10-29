# `it-career-path-finder.php`

**Purpose:** the plugin's main file. WordPress reads its header to list the plugin; when the plugin is active, WordPress includes it on every request.

## What it does, top to bottom

1. **Plugin header** (the only comment block allowed in the code base, D13): name, version, requirements, authors, license, text domain. `Version` must match `ITCP_VERSION`, `readme.txt` and `CHANGELOG.md` (`tests/VersionTest.php`).
2. `defined( 'ABSPATH' ) || exit;` — does nothing when the file is opened directly.
3. Defines constants used everywhere:

| Constant | Value | Used by |
|---|---|---|
| `ITCP_VERSION` | `1.0.0` | Cache-busting `?ver=` on CSS/JS (`Assets`, `AdminAssets`), the About page |
| `ITCP_PLUGIN_FILE` | this file | `Plugin::load_translations()` |
| `ITCP_PLUGIN_DIR` | plugin folder path with trailing slash | `Plugin`, `View` (templates), data folder |
| `ITCP_PLUGIN_URL` | plugin folder URL | `Assets`, `AdminAssets` |

4. Loads [`src/Autoloader.php`](src/Autoloader.md), registers it for `src/`, and calls [`Plugin::boot()`](src/Plugin.md).

## Relations

- Uses: `Autoloader`, `Plugin`.
- Used by: WordPress.
- Has no functions of its own.
