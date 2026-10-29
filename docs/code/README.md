# Code documentation

One page per code file, at the same path as the file (`src/Data/RoleSanitizer.php` → [`src/Data/RoleSanitizer.md`](src/Data/RoleSanitizer.md)). Each page says what the file is for, which files it uses and which use it, and what every function does. Read it together with [../PROJECT.md](../PROJECT.md), which shows how a request flows through these files.

`tests/DocsCoverageTest.php` fails when a code file has no page here or when a function name is missing from its page, so this folder cannot silently fall behind the code.

## Suggested reading order

1. [data/roles.md](data/roles.md) and [data/questionnaire.md](data/questionnaire.md) — what the tool knows.
2. [src/Plugin.md](src/Plugin.md) — how everything is wired to WordPress.
3. [src/Data/RoleRepository.md](src/Data/RoleRepository.md) — bundled careers + admin edits = what visitors see.
4. [src/Frontend/Shortcode.md](src/Frontend/Shortcode.md) and [src/Frontend/DatasetPrinter.md](src/Frontend/DatasetPrinter.md) — what lands on the page.
5. [assets/js/finder.md](assets/js/finder.md) — the browser controller.
6. [assets/js/scoring.md](assets/js/scoring.md) and [assets/js/ranking.md](assets/js/ranking.md) — how careers are matched.
7. [src/Admin/RoleActions.md](src/Admin/RoleActions.md) and [src/Data/RoleSanitizer.md](src/Data/RoleSanitizer.md) — how admin edits are checked and saved.

## File index

### Plugin root

| File | Purpose |
|---|---|
| [it-career-path-finder.php](it-career-path-finder.md) | Plugin header, constants, autoloader, boot |
| [uninstall.php](uninstall.md) | Deletes stored data when the plugin is deleted |

### PHP — `src/`

| File | Purpose |
|---|---|
| [Autoloader.php](src/Autoloader.md) | Loads `ItCareerPathFinder\…` classes from `src/` |
| [Plugin.php](src/Plugin.md) | Creates every module and hooks it into WordPress |
| [Support/View.php](src/Support/View.md) | Renders templates from `templates/` |
| [Data/JsonFile.php](src/Data/JsonFile.md) | Reads and decodes JSON safely |
| [Data/BundledData.php](src/Data/BundledData.md) | Reads the five files in `data/` |
| [Data/RoleSchema.php](src/Data/RoleSchema.md) | The allowed values and rating fields of a career, from `vocabulary.json` |
| [Data/RoleSanitizer.php](src/Data/RoleSanitizer.md) | Cleans any career input into the exact career shape |
| [Data/RoleChanges.php](src/Data/RoleChanges.md) | Stores admin edits (one WordPress option) |
| [Data/RoleRepository.php](src/Data/RoleRepository.md) | Combines bundled careers with admin edits |
| [Data/RoleImporter.php](src/Data/RoleImporter.md) | Imports careers from JSON text |
| [Frontend/FrontendDataset.php](src/Frontend/FrontendDataset.md) | Builds the data array sent to the browser |
| [Frontend/DatasetPrinter.php](src/Frontend/DatasetPrinter.md) | Prints that array once per page as a JSON block |
| [Frontend/Assets.php](src/Frontend/Assets.md) | Registers and enqueues the CSS and the 18 scripts |
| [Frontend/Shortcode.php](src/Frontend/Shortcode.md) | The `[it_career_path_finder]` shortcode |
| [Admin/RequestGuard.php](src/Admin/RequestGuard.md) | Capability and nonce checks |
| [Admin/Notices.php](src/Admin/Notices.md) | Redirect-with-message and the admin notices |
| [Admin/Drafts.php](src/Admin/Drafts.md) | Keeps an invalid form submission for one more page load |
| [Admin/AdminMenu.php](src/Admin/AdminMenu.md) | The "مسیر شغلی IT" menu and its pages |
| [Admin/AdminAssets.php](src/Admin/AdminAssets.md) | Loads admin CSS/JS on the plugin's pages only |
| [Admin/RolesPage.php](src/Admin/RolesPage.md) | The careers list page |
| [Admin/RoleEditorPage.php](src/Admin/RoleEditorPage.md) | The add/edit career page |
| [Admin/RoleActions.php](src/Admin/RoleActions.md) | Save, delete and restore actions |
| [Admin/TransferPage.php](src/Admin/TransferPage.md) | The import/export page |
| [Admin/TransferActions.php](src/Admin/TransferActions.md) | Export, import and reset actions |
| [Admin/AboutPage.php](src/Admin/AboutPage.md) | The help and credits page |

### Templates — `templates/`

| File | Purpose |
|---|---|
| [shortcode-app.php](templates/shortcode-app.md) | The empty tool shell the browser code fills |
| [admin/roles-list.php](templates/admin/roles-list.md) | Careers table with actions |
| [admin/role-editor.php](templates/admin/role-editor.md) | Career form |
| [admin/rating-select.php](templates/admin/rating-select.md) | One 1–5 dropdown |
| [admin/checkbox-group.php](templates/admin/checkbox-group.md) | A grid of checkboxes |
| [admin/transfer.php](templates/admin/transfer.md) | Export / import / reset forms |
| [admin/about.php](templates/admin/about.md) | Shortcode help and credits |

### Browser — `assets/js/` (load order)

| File | Namespace object | Purpose |
|---|---|---|
| [format.js](assets/js/format.md) | `ItcpFinder.Format` | Small pure helpers: digits, templates, ratings |
| [dom.js](assets/js/dom.md) | `ItcpFinder.Dom` | Safe element and icon creation |
| [scroll.js](assets/js/scroll.md) | `ItcpFinder.Scroll` | Scrolling below sticky theme headers |
| [dataset.js](assets/js/dataset.md) | `ItcpFinder.Dataset` | Reads the JSON block and builds lookups |
| [state.js](assets/js/state.md) | `ItcpFinder.State` | Answers and position of one tool |
| [storage.js](assets/js/storage.md) | `ItcpFinder.Storage` | Saves/restores state in `sessionStorage` |
| [assessment.js](assets/js/assessment.md) | `ItcpFinder.Assessment` | Collects reasons, risks and penalties for one career |
| [scoring.js](assets/js/scoring.md) | `ItcpFinder.Scoring` | Scores one career against the answers |
| [ranking.js](assets/js/ranking.md) | `ItcpFinder.Ranking` | Scores all careers and splits them into groups |
| [conflicts.js](assets/js/conflicts.md) | `ItcpFinder.Conflicts` | Finds contradictions between answers |
| [plan.js](assets/js/plan.md) | `ItcpFinder.Plan` | Builds the 30-day plan of a career |
| [progress-view.js](assets/js/progress-view.md) | `ItcpFinder.ProgressView` | Header chips, progress bar, question dots |
| [question-view.js](assets/js/question-view.md) | `ItcpFinder.QuestionView` | Renders one question |
| [role-card.js](assets/js/role-card.md) | `ItcpFinder.RoleCard` | Renders one career card |
| [result-actions.js](assets/js/result-actions.md) | `ItcpFinder.ResultActions` | Copy, share, print, restart |
| [results-view.js](assets/js/results-view.md) | `ItcpFinder.ResultsView` | Renders the whole results page |
| [finder.js](assets/js/finder.md) | `ItcpFinder.Finder` | Controller of one tool instance |
| [boot.js](assets/js/boot.md) | `ItcpFinder.mountAll` | Starts every tool on the page |
| [admin.js](assets/js/admin.md) | — | Admin-only: confirm dialogs and table search |

### Styles and data

| File | Purpose |
|---|---|
| [assets/css/app.css](assets/css/app.md) | The tool's look |
| [assets/css/admin.css](assets/css/admin.md) | Admin pages' look |
| [data/questionnaire.json](data/questionnaire.md) | Sections and questions |
| [data/vocabulary.json](data/vocabulary.md) | Allowed values and labels |
| [data/scoring.json](data/scoring.md) | Weights, thresholds, penalties |
| [data/interface-text.json](data/interface-text.md) | Every visitor-facing word |
| [data/roles.json](data/roles.md) | The bundled careers |

### Tests and tools

| File | Purpose |
|---|---|
| [tests/run.php](tests/run.md) | Runs every `*Test.php` |
| [tests/bootstrap.php](tests/bootstrap.md) | Test environment setup |
| [tests/Support/WordPressStubs.php](tests/Support/WordPressStubs.md) | Minimal WordPress functions for tests |
| [tests/Support/TestCase.php](tests/Support/TestCase.md) | Base class with assertions |
| [tests/RoleSanitizerTest.php](tests/RoleSanitizerTest.md) | Sanitizer tests |
| [tests/RoleRepositoryTest.php](tests/RoleRepositoryTest.md) | Overlay logic tests |
| [tests/RoleImporterTest.php](tests/RoleImporterTest.md) | Import tests |
| [tests/DatasetIntegrityTest.php](tests/DatasetIntegrityTest.md) | Data consistency tests |
| [tests/DocsCoverageTest.php](tests/DocsCoverageTest.md) | Keeps this folder complete |
| [tests/VersionTest.php](tests/VersionTest.md) | Keeps the version in sync |
| [tests/js/run.html](tests/js/run.md) | Browser test page |
| [tests/js/tests.js](tests/js/tests.md) | Browser tests |
| [tools/build-zip.php](tools/build-zip.md) | Builds the install zip |

## Files without their own page

- `index.php` in every folder: empty on purpose (`<?php` only) so a web server never lists the folder's files.
- `languages/`: place for `.po`/`.mo` (or `.l10n.php`) translations of the admin strings.

## Conventions used everywhere

- PHP files start with `defined( 'ABSPATH' ) || exit;`. Classes are `final` and live in `ItCareerPathFinder\<Area>`.
- JavaScript files are `(function (ns) { … })(window.ItcpFinder = window.ItcpFinder || {});` and add exactly one object to `ns`. They load in the order of `Assets::SCRIPT_MODULES`; a file may only use objects from files above it at load time.
- Visitor-facing text always comes from `finder.text` / `context.text` (interface-text.json); no Persian strings in JavaScript.
- DOM is built with `ItcpFinder.Dom` and `textContent`; there is no `innerHTML`.
