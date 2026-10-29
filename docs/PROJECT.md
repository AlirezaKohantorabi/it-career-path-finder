# Project overview

How the plugin is put together, how the pieces talk to each other, and where to look when something breaks. For the reasoning behind each choice see [DECISIONS.md](DECISIONS.md); for every function see [code/README.md](code/README.md).

## Folder map

```
it-career-path-finder.php   Plugin header, constants, autoloader, boot
uninstall.php               Deletes stored data when the plugin is deleted
readme.txt                  WordPress-format readme (Stable tag = version)
data/                       All content as JSON (questions, vocabulary, scoring, interface text, roles)
src/                        PHP classes (namespace ItCareerPathFinder)
  Plugin.php                Wires every module to WordPress hooks
  Autoloader.php            Loads classes from src/ by namespace
  Data/                     Reading, cleaning and storing careers
  Frontend/                 Shortcode, assets, dataset for the browser
  Admin/                    Admin menu, pages, form actions, guards
  Support/View.php          Renders templates
templates/                  HTML views (shortcode markup, admin pages)
assets/js/                  Browser code: 18 small modules + admin.js
assets/css/                 app.css (the tool), admin.css (admin pages)
languages/                  Place for translation files of admin strings
tests/                      PHP tests (php tests/run.php) and JS tests (tests/js/run.html)
tools/build-zip.php         Builds dist/it-career-path-finder-<version>.zip
docs/                       This documentation; docs/fa has the Persian guides
.github/workflows/          CI (tests on every push) and Release (zip on tags)
```

## What happens when a visitor opens a page with the tool

```
WordPress renders the page
 ├─ wp_enqueue_scripts
 │   ├─ Assets::register_assets()      registers app.css + 18 scripts (each depends on the previous one)
 │   └─ Shortcode::enqueue_for_current_post()   enqueues them early if the post contains the shortcode (CSS lands in <head>)
 ├─ the_content → [it_career_path_finder]
 │   └─ Shortcode::render()
 │       ├─ Assets::enqueue()          (again, harmless; covers widgets/builders)
 │       ├─ View::render('shortcode-app')   the empty shell: header, dots, form, results, toast
 │       └─ DatasetPrinter::request()  remembers that the footer must print the data
 └─ wp_footer
     ├─ DatasetPrinter::print_if_requested()
     │   └─ FrontendDataset::build()   questionnaire + vocabulary + scoring + interface text + RoleRepository::all()
     │       → <script type="application/json" id="itcp-dataset">…</script>  (printed once per page)
     └─ the 18 scripts, ending with boot.js
         └─ mountAll(): reads #itcp-dataset once, creates one Finder per [data-itcp-app] element
```

In the browser, `Finder` (finder.js) is the controller for one tool instance:

```
Finder ── state (state.js) ── saved per instance in sessionStorage (storage.js)
  ├─ ProgressView   header chips, progress bar, question dots
  ├─ QuestionView   one question, its options and the error box
  └─ ResultsView    summary, conflicts, "try another condition" buttons, cards, full ranking
        ├─ Ranking  → Scoring → Assessment     (who fits, why, what to give up)
        ├─ Conflicts                             (contradictions between answers)
        ├─ RoleCard → Plan                      (one card; plain explanation first; 30-day plan)
        └─ ResultActions                        (copy, share, print, restart)
```

Nothing is sent back to the server. All visitor-facing words come from `dataset.text` (interface-text.json).

## What happens in the admin

```
admin_menu → AdminMenu::add_pages()
   مسیر شغلی IT            → RolesPage        (list, delete/restore buttons)
   ├─ افزودن شغل           → RoleEditorPage   (add or edit one career)
   ├─ درون‌ریزی و برون‌بری  → TransferPage     (export, import, reset)
   └─ راهنما و سازندگان     → AboutPage

Forms post to wp-admin/admin-post.php with an action name:
   itcp_save_role / itcp_delete_role / itcp_restore_role   → RoleActions
   itcp_export_roles / itcp_import_roles / itcp_reset_roles → TransferActions

Every action: RequestGuard::verify_action() (capability + nonce)
            → RoleSanitizer (clean every field) → RoleRepository → RoleChanges (one option)
            → Notices::redirect() back to a page with a message key
```

## Where careers come from

```
data/roles.json (bundled, read-only)          option itcp_role_changes (admin edits)
          │                                       { saved: {id: role}, deleted: [ids] }
          └──────────────┬────────────────────────────────┘
                 RoleRepository::all()
   bundled roles in file order, minus deleted ids, each replaced by its saved version if any,
   then custom roles (saved roles whose id is not in the bundle), in the order they were added
```

- Editing a bundled career and saving it **identical** to the bundled version removes the saved copy (D7).
- A plugin update that adds careers to `roles.json` shows them automatically; admin edits survive (D6).
- Export writes `RoleRepository::all()` in the same format as `roles.json`, so an export can replace the bundled file in Git.

## Extension points (filters)

| Filter | Used in | What you can change |
|---|---|---|
| `itcp_data_directory` | `Plugin::data_directory()` | Folder the five JSON files are read from (e.g. a translated copy in a theme) |
| `itcp_frontend_dataset` | `FrontendDataset::build()` | The whole array sent to the browser, after it is built |
| `itcp_manage_capability` | `RequestGuard::capability()` | Capability needed for the admin pages and actions (default `manage_options`) |

## Stored data

| Where | Name | Content | Removed by |
|---|---|---|---|
| `wp_options` | `itcp_role_changes` (not autoloaded) | `saved` roles and `deleted` ids | "Reset" button, `uninstall.php` |
| transients | `itcp_draft_<user id>` | an invalid form submission, for 10 minutes | read once, or expires |
| visitor's browser | `sessionStorage` `itcp_state_v3_<n>` | answers and position of tool instance `n` | "شروع دوباره", closing the tab |

## Versioning and releases

See [CONTRIBUTING.md](../CONTRIBUTING.md#release-checklist). In short: change the version in the plugin header, `ITCP_VERSION` and `readme.txt`, add a CHANGELOG entry, run the tests, commit, tag `vX.Y.Z`, push the tag; GitHub Actions builds the zip and attaches it to the release.
