# Testing

Three layers: PHP tests (no dependencies), JavaScript tests in a browser page, and a manual check in a real WordPress. GitHub Actions runs the PHP tests and builds the zip on every push.

## 1. PHP tests

```bash
php tests/run.php
```

Prints a dot per passing test and details for failures; exits with code 1 if anything fails. What is covered:

| File | Checks |
|---|---|
| `tests/RoleSanitizerTest.php` | HTML stripping, unknown values dropped, ratings clamped, skills split, length limits, required fields, bundled roles pass unchanged |
| `tests/RoleRepositoryTest.php` | Overlay logic: edit in place, back to default, custom roles, delete/restore, replace, reset |
| `tests/RoleImporterTest.php` | Merge/replace import, plain lists, skipped invalid and duplicate roles, nothing changes on bad input |
| `tests/DatasetIntegrityTest.php` | All JSON valid; ids unique and safe; every role complete with ratings 1–5 and known references; questions and sections consistent; weights match the factors in `scoring.js`; every penalty, message and interface-text key used by the JavaScript exists |
| `tests/DocsCoverageTest.php` | Every code file has a page in `docs/code/`; every function is named in it |
| `tests/VersionTest.php` | Version is the same in the header, `ITCP_VERSION`, `readme.txt` and `CHANGELOG.md` |

**Windows note (L11):** PHP from winget has no `php.ini`. Create one with:

```ini
extension_dir = "C:\path\to\php\ext"
extension=mbstring
extension=zip
extension=openssl
extension=pdo_sqlite
extension=sqlite3
```

and run `php -c C:\path\to\php.ini tests/run.php`.

## 2. JavaScript tests

Serve the repository and open the test page:

```bash
php -S localhost:8090
```

Then open `http://localhost:8090/tests/js/run.html`. The page title becomes `PASS — n/n` or `FAIL — …`; failing checks are listed in red. It covers formatting, multi-select rules, storage validation (including `constructor`/`__proto__` values), scoring penalties and bounds, ranking groups, the strict insurance filter, conflicts, the 30-day plan, and a full render of the results (no `undefined`, no unfilled `{placeholders}`, plain explanation first).

The page contains a copy of the shortcode markup (`#fixture`). If you change `templates/shortcode-app.php` in a way that adds or renames a `data-itcp` part, update the fixture too.

## 3. Manual check in WordPress

A throwaway local WordPress is enough (no MySQL needed):

1. Download WordPress and the **SQLite Database Integration** plugin from wordpress.org. Copy `wp-content/plugins/sqlite-database-integration/db.copy` to `wp-content/db.php`.
2. Link or copy this repository to `wp-content/plugins/it-career-path-finder`.
3. Run `php -c php.ini -S localhost:8080 -t path/to/wordpress`, finish the installer, activate the plugin.
4. Create a page with `[it_career_path_finder]` and another with two shortcodes.

Checklist:

- [ ] Page loads, no console errors, `app.css` is in `<head>`, one `#itcp-dataset` block per page.
- [ ] "بعدی" without an answer shows the error; clicking a single-choice option moves on by itself.
- [ ] Results: recommended / compromise / not-recommended sections; every card starts with "این شغل به زبون ساده چیه؟".
- [ ] Jump buttons scroll to the card; copy, print, restart work; tweak buttons update results.
- [ ] Refreshing the page keeps answers; two tools on one page keep separate answers.
- [ ] At 375 px width nothing scrolls sideways.
- [ ] Admin: add (with missing fields → errors and kept values), edit (badge "ویرایش‌شده"), delete (appears under deleted), restore, export, import merge/replace, reset.
- [ ] `wp-content/debug.log` has no warnings from the plugin (`WP_DEBUG_LOG` on).

## 4. Parity check (after changing scoring or ranking)

Changes to `assets/js/scoring.js` or `ranking.js` can silently move results (L5). Compare with the previous release:

1. Run the previous release on one page and the new code on another (two local sites, or switch tags).
2. Open the tool, answer the questions with a fixed profile, and read the full ranking in the console:
   ```js
   [...document.querySelectorAll('.itcp-rk')].map(r => r.querySelector('b').textContent + ' ' + r.querySelector('.itcp-rk-s').textContent)
   ```
3. Compare both lists. Write the largest difference and any intended change into the CHANGELOG entry.

The 1.0.0 port was checked this way against the original HTML tool: largest difference 1 point over 87 careers, for two very different profiles.
