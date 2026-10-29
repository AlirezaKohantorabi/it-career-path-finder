# `tests/bootstrap.php`

**Purpose:** prepares a fake WordPress-like environment so plugin classes can run outside WordPress.

- Defines `ABSPATH` (so the `defined( 'ABSPATH' ) || exit;` guards pass), `ITCP_TESTS_ROOT` (repository root), `ITCP_PLUGIN_DIR`, `ITCP_PLUGIN_URL` and a test `ITCP_VERSION`.
- Loads [`Support/WordPressStubs.php`](Support/WordPressStubs.md) and [`Support/TestCase.php`](Support/TestCase.md).
- Registers the plugin's [`Autoloader`](../src/Autoloader.md) for `src/`.

Has no functions. Used by [`run.php`](run.md).
