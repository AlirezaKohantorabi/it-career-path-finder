# `src/Frontend/Assets.php`

**Purpose:** registers the tool's stylesheet and its 18 scripts, and enqueues them when a page needs the tool. Each script depends on the one before it, so enqueuing the last one (`boot`) loads all of them in the right order, in the footer (D15).

## Functions

| Function | What it does |
|---|---|
| `register()` | Hooks `register_assets` on `wp_enqueue_scripts` (priority 5). |
| `register_assets()` | Registers `itcp-app` (`assets/css/app.css`) and the scripts, all versioned with `ITCP_VERSION`. |
| `enqueue()` | Registers first if needed (e.g. a shortcode rendered outside the normal page flow), then enqueues the style and the entry script. Safe to call many times. |
| `register_scripts()` | Registers `itcp-<module>` for each name in `SCRIPT_MODULES`, each depending on the previous handle. |
| `entry_script_handle()` | Handle of the last module (`itcp-boot`). |

## The load order (`SCRIPT_MODULES`)

`format, dom, scroll, dataset, state, storage, assessment, scoring, ranking, conflicts, plan, progress-view, question-view, role-card, result-actions, results-view, finder, boot`

A module may use objects from modules **above** it when the file loads (for example `results-view.js` keeps `ns.RoleCard` in a variable). When adding a JS file: add it here in the right place, add it to [`tests/js/run.html`](../../tests/js/run.md), and write its page in `docs/code/assets/js/`.

## Relations

- Used by: [`Shortcode`](Shortcode.md) (`enqueue`), registered by [`Plugin`](../Plugin.md).
- Loads: [`assets/css/app.css`](../../assets/css/app.md) and every file in [`assets/js/`](../../assets/js/finder.md) except `admin.js`.
