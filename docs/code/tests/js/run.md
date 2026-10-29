# `tests/js/run.html`

**Purpose:** the browser page that runs the JavaScript tests (D22). Serve the repository root and open it:

```bash
php -S localhost:8090
```

Then open `http://localhost:8090/tests/js/run.html`. The page title shows `PASS — n/n` or `FAIL — …`, and each check is listed in green or red.

It loads `assets/css/app.css`, the 17 modules in the same order as [`Assets::SCRIPT_MODULES`](../../src/Frontend/Assets.md) (all except `boot.js`, so nothing mounts by itself), and [`tests.js`](tests.md).

**Fixture:** `#fixture` holds a copy of the tool markup (`data-itcp-app-fixture`) with every `data-itcp` part from [`templates/shortcode-app.php`](../../templates/shortcode-app.md). When a part is added or renamed in the template, update it here as well.

Has no functions.
