# Changelog

Every version lists **what** changed and **why**. Longer reasoning lives in [docs/DECISIONS.md](docs/DECISIONS.md) (entries `D1`, `D2`, …) and mistakes or bugs we learned from live in [docs/LESSONS.md](docs/LESSONS.md) (entries `L1`, `L2`, …). Versions follow [Semantic Versioning](https://semver.org/): `MAJOR.MINOR.PATCH`.

The version number must be the same in four places: the plugin header and `ITCP_VERSION` in `it-career-path-finder.php`, `Stable tag` in `readme.txt`, and a heading in this file. `tests/VersionTest.php` fails if they disagree.

## [1.0.0] — 2026-10-30

First release as a WordPress plugin. Rebuilt from the single-file HTML tool (0.1.0) because that file did not load reliably inside WordPress (L1, D1).

### Added

- Shortcode `[it_career_path_finder]` with optional `title` and `subtitle` attributes; several instances can live on one page (D2, D16).
- Admin menu **مسیر شغلی IT** with pages to list, add, edit, delete and restore careers, import/export them as JSON, and reset to the bundled list (D6, D7).
- A plain, non-technical explanation for all 87 careers (`plain_description`), shown first on every career card (D9).
- Data split into five JSON files in `data/`: questions, vocabulary, scoring, interface text and roles (D5, D12).
- Industry aliases so choosing an industry also matches related workplaces, e.g. healthcare → hospital (D21, L4).
- Security: capability and nonce checks on every admin action, field-by-field sanitizing, escaped output, safe JSON embedding, no `innerHTML` (D19, [docs/SECURITY.md](docs/SECURITY.md)).
- PHP test runner (no dependencies) and a browser test page for the JavaScript; GitHub Actions run them and build the zip (D22).
- Full documentation: one page per code file in `docs/code/`, Persian guides in `docs/fa/`.

### Changed (compared with 0.1.0)

- The confusing agile question was removed. The "work arrangement" question became **شیوه کار** with clear options: official employment with a fixed salary, contracting for companies, project work for individuals or companies (freelance), consulting, own business, internship (D8). Scores move by at most 1 point; measured on two very different answer sets (L5).
- All visitor-facing text was rewritten in a friendly, informal tone (D11).
- Sections ("بخش ۱ از ۵") come from the data instead of hard-coded question numbers (D18).
- The tool uses the theme's font everywhere (D17).
- Code is split into small modules: 25 PHP classes and 18 JavaScript files, each function doing one job (D13, D14, D15).

### Removed

- The `summary` field of each career: all 87 were the same template sentence (L3, D10).
- The `agile_team_fit` rating, which only fed the removed question (D8).
- The SEO article that surrounded the tool in the HTML file; it belongs in the WordPress page, not the plugin (D27).

### Fixed

- Saved answers named like built-in object properties (for example `constructor`) were accepted by the old restore code; the new code only accepts real option values (L10).

## [0.1.0] — before 2026-10-29 (not in this repository)

The original tool: one 261 KB HTML file on a single line (CSS, 170 KB of JSON data and minified JavaScript, plus an SEO article), pasted into a Gutenberg "Custom HTML" block. It worked as a standalone page but did not load properly inside WordPress, which led to this plugin (L1).
