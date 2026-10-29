# Decisions

Every design decision, the alternatives we looked at, why we chose what we chose, and what it costs us. Entries are in the order they were made; dates are decision dates. When a decision is replaced, the old entry stays and gets a "Replaced by" line.

**Who decided:** **O** = project owners (Alireza, Khosro); **E** = engineering recommendation the owners accepted.

---

### D1 — Ship the tool as a WordPress plugin (2026-10-29, O)

- **Alternatives:** keep pasting the HTML into a "Custom HTML" block; host it as a separate static page in an iframe.
- **Why:** the 261 KB single-line HTML did not load properly in WordPress (L1). A plugin loads CSS and JS as normal files, keeps data editable, and can be reused on many pages.
- **Cost:** needs PHP code, a release process and WordPress knowledge to maintain.

### D2 — Embed with a shortcode (2026-10-29, O)

- **Alternatives:** a Gutenberg block; a widget.
- **Why:** the owners asked for a shortcode. It works in the classic editor, the block editor (Shortcode block), page builders and widgets.
- **Cost:** no visual preview in the block editor. A real block can be added later on top of the same `Shortcode::render()`.

### D3 — Matching runs in the visitor's browser (2026-10-29, E)

- **Alternatives:** score on the server through an AJAX/REST endpoint.
- **Why:** answers never leave the browser (privacy), there is no public endpoint to attack or overload, and the original logic was already JavaScript, so it could be ported and checked line by line (L5).
- **Cost:** the whole dataset (~150 KB, ~35 KB gzipped) is sent to every page that shows the tool.

### D4 — Deliver the dataset as one JSON block in the footer (2026-10-29, E)

- **Alternatives:** REST endpoint fetched by JavaScript; `wp_localize_script` global variable; JSON inside every shortcode output.
- **Why:** many Iranian sites disable the REST API with security plugins, so a fetch could fail. A `<script type="application/json">` block is never executed, is ignored by most optimizer plugins, and is printed once even when the shortcode appears several times. If the footer was already printed (rare late rendering), the block is added right after the shortcode instead.
- **Cost:** the data is not cached separately by the browser.

### D5 — Split the data into five JSON files (2026-10-29, O + E)

- **Alternatives:** one big JSON file (like the original); one file per career.
- **Why:** the owners asked for a separate JSON so careers and levels can be changed later. Splitting by purpose (questions, vocabulary, scoring, interface text, roles) means each person edits only the file they need, and Git diffs stay small. One file per career was rejected: 87 files to read on every page view.
- **Cost:** five files to keep consistent; `tests/DatasetIntegrityTest.php` checks the links between them.

### D6 — Admin edits are stored as an overlay, not by rewriting JSON files (2026-10-29, E)

- **Alternatives:** write admin edits into `data/roles.json`; copy all roles into the database on activation.
- **Why:** plugin updates replace every file in the plugin folder, so edits written there would be lost; writing PHP-reachable files from a web request is also a security risk. Copying everything into the database would hide new careers shipped in later versions. The overlay (`itcp_role_changes` option: `saved` roles + `deleted` ids) keeps admin edits **and** lets new bundled careers appear after an update.
- **Cost:** two sources of truth. Export/import and "reset to defaults" bridge them; the admin list shows each role's origin (default / edited / added).

### D7 — An edit identical to the bundled version is not stored (2026-10-29, E)

- **Why:** otherwise re-importing an export would mark every role as "edited" and freeze it against future data updates.
- **Cost:** comparison runs on every save (cheap: 87 roles).

### D8 — Remove the agile question; rewrite "work arrangement" as "شیوه کار" (2026-10-29, O)

- **Context:** Khosro found the agile question ("Agile is not a contract type…") hard to understand and asked for a "way of working" question with options like contracting for companies, official employment with a fixed salary, and project work for individuals or legal entities.
- **Alternatives:** keep a team-style question without the word "agile"; add a new "شیوه کار" question next to the existing work-arrangement question.
- **Why:** the existing work-arrangement question already asked exactly that, so we rewrote *it* with Khosro's options instead of adding a duplicate, and deleted the agile question. 20 questions became 19. The `routine_and_team_style_match` weight became `routine_match`; the `agile_team_fit` rating was removed from all roles.
- **Cost:** scores shift by at most 1 point (measured, L5); occasionally a tie at the edge of the top 6 resolves differently.

### D9 — Add a plain-language description to every career (2026-10-29, O)

- **Why:** Khosro asked that after a match, clicking a job first explains in very simple, non-IT words what the job is and does. New required field `plain_description` (written for all 87 roles), shown as the first box on every card ("این شغل به زبون ساده چیه؟"). The admin editor requires it.
- **Cost:** every new career needs this text.

### D10 — Remove the `summary` field (2026-10-29, E)

- **Why:** all 87 summaries were the same template sentence with only the title changed (L3). The useful part ("depends on city, experience…") is now one general note in the results summary.

### D11 — Friendly, informal tone everywhere (2026-10-29, O)

- **Why:** requested by Khosro. Questions, options, hints, results and messages use conversational Persian ("چی", "می‌خوای", "بهت").
- **Cost:** the admin screens stay in standard written Persian, which suits a settings page.

### D12 — All visitor-facing text lives in `data/interface-text.json` (2026-10-29, E)

- **Alternatives:** hard-code Persian strings in JavaScript (original); pass gettext-translated strings from PHP.
- **Why:** wording and tone can be changed without touching code, and the JavaScript stays language-neutral. Admin strings use WordPress `__()` so they can still be translated with normal tools.
- **Cost:** visitor text is not translatable with Loco Translate; a translation means a different JSON file (the `itcp_data_directory` filter supports that).

### D13 — No comments in code; documentation lives next to it in `docs/code/` (2026-10-29, O)

- **Why:** Khosro asked for no comments and names that say what each function does (Clean Code). Alireza asked for a document per code file explaining every function and how files relate. `tests/DocsCoverageTest.php` fails when a code file has no doc page or a function is not mentioned in it.
- **Exception:** the WordPress plugin header in `it-career-path-finder.php` is a required comment block.

### D14 — Namespaced PHP classes, PSR-4 autoloading, WordPress coding style (2026-10-29, E)

- **Why:** one class per file in `src/`, grouped by area (`Admin`, `Data`, `Frontend`, `Support`), loaded by a tiny autoloader with no Composer dependency. Method and variable names follow WordPress style (snake_case) so they read naturally next to WordPress functions.
- **Requires:** PHP 7.4 (typed properties).

### D15 — Vanilla JavaScript in 18 small files, no build step (2026-10-29, E)

- **Alternatives:** ES modules; a bundler (webpack/Vite).
- **Why:** no Node.js toolchain needed to edit or release; classic scripts with chained WordPress dependencies load in a guaranteed order and survive optimizer plugins better than ES modules. ES5 syntax keeps older Android browsers working.
- **Cost:** 18 small requests (fine on HTTP/2; optimizers may combine them).

### D16 — Scope CSS with `.itcp-app.itcp-scope` instead of `#itcp-app` (2026-10-29, E)

- **Why:** an ID only allows one tool per page. Two classes on the same element still out-rank typical theme selectors like `.entry-content h2`, which the original ID protected against.

### D17 — Use the theme's font (2026-10-29, O)

- **Why:** requested. Every element uses `font-family: inherit`; the plugin ships no font.

### D18 — Question order is the array order; sections come from data (2026-10-29, E)

- **Why:** the original used `step` numbers and hard-coded "questions 1–3 = section 1". Now a question has a `section` id, and inserting a question is one edit in one file.

### D19 — Security model (2026-10-29, E)

- Capability (`manage_options`, filterable) **and** nonce on every admin action; every input sanitized field by field against the vocabulary; every output escaped; JSON embedded with `JSON_HEX_*` flags; frontend builds the DOM with `textContent` only; uploads are read from the temporary file and never stored; uninstall removes stored data. Details: [SECURITY.md](SECURITY.md).

### D20 — One saved state per tool instance (2026-10-29, E)

- **Why:** answers are kept in `sessionStorage` under `itcp_state_v3_<n>`, where `n` is the instance number on the page, so two tools on one page do not overwrite each other. The same instance number on different pages shares answers, which is useful when the tool appears on several pages.

### D21 — Industry aliases cover related workplaces (2026-10-29, E)

- **Why:** choosing "healthcare" did not match careers whose workplace is "hospital" (L4). `industry_aliases` now maps every industry option to its related workplaces.
- **Cost:** results change slightly when a visitor picks industries.

### D22 — Zero-dependency tests (2026-10-29, E)

- **Alternatives:** PHPUnit via Composer; Jest via Node.
- **Why:** neither Composer nor Node is installed on the owners' machines. `php tests/run.php` needs only PHP; the JavaScript tests run in any browser (`tests/js/run.html`). GitHub Actions runs the PHP tests on every push.

### D23 — License GPL-2.0-or-later (2026-10-29, E)

- **Why:** WordPress plugins that use WordPress APIs must be GPL-compatible; this is the WordPress standard.

### D24 — Repository root is the plugin folder; a script builds the zip (2026-10-29, E)

- **Why:** cloning the repo into `wp-content/plugins/` gives a working plugin for development. `tools/build-zip.php` packs only runtime files under an `it-career-path-finder/` folder with forward-slash paths (Windows backslash zips break WordPress installs).

### D25 — Commits are authored by the repository owner (2026-10-29, O)

- **Why:** requested. Commits use `AlirezaKohantorabi <alireza.kohantorabi91082@gmail.com>`, the same identity as the owner's other repositories.

### D26 — Khosro is a collaborator with write access (2026-10-29, O)

- **Why:** requested so both developers can push. Personal GitHub repositories give collaborators write access; repository settings stay with the owner.

### D27 — The SEO article is not part of the plugin (2026-10-29, E)

- **Why:** the original HTML wrapped the tool in a long article. Articles are page content; editing them in WordPress is easier and keeps the plugin reusable on any page.
