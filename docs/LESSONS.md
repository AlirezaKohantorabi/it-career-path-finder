# Lessons

Mistakes we made, bugs we found, and what we learned, so we do not repeat them. Newest at the bottom. Each entry says what happened, why, and what we do now.

---

### L1 — A 261 KB single-line HTML block does not belong in the post editor (2026-10-29)

- **What happened:** the original tool was one HTML file (CSS + 170 KB JSON + minified JS + an article) on a single line, pasted into a Gutenberg "Custom HTML" block. It did not load properly in the browser.
- **Why:** the editor, `wpautop`, security filters and optimizer plugins all process post content; a huge inline script inside content is exactly what they mangle, defer or strip. The original even needed `data-no-optimize`, `nowprocket` and `<!--noptimize-->` workarounds.
- **Now:** CSS and JS are separate enqueued files, data is one JSON block printed by PHP (D4), and the shortcode output has no whitespace between tags so `wpautop` cannot insert `<p>`/`<br>` into it.

### L2 — Splitting the original HTML with a regex on `<script` broke on a string (2026-10-29)

- **What happened:** while migrating, the first attempt split the file on every `<script…>` tag. The JavaScript contained the text `<script id="itcp-json-data">` inside an error message, so the split cut the code in the middle.
- **Now:** when extracting parts from minified files, cut on known unique markers (the exact opening tag and the *last* `</script>`), then verify by parsing (JSON) or formatting (JS).

### L3 — All 87 career summaries were the same sentence (2026-10-29)

- **What happened:** every `summary` was "مسیر شغلی X در بازار فناوری اطلاعات ایران؛ میزان و شکل فرصت‌ها …", only the title changed. It looked like content but said nothing.
- **Now:** removed (D10). Real content per career is the new `plain_description`. Generated-looking text should be checked for duplicates before shipping.

### L4 — Industry aliases had dead keys and missing synonyms (2026-10-29)

- **What happened:** `industry_aliases` was keyed by `public_sector`, `individual`, `small_business` and `medium_large_organization` — client types, not industry options — so those entries were never used. Meanwhile "healthcare" did not match "hospital", "telecom" did not match "isp", and so on.
- **Now:** every key is an industry option and every alias is a known workplace; `DatasetIntegrityTest::test_industry_aliases_use_known_options_and_workplaces` enforces it (D21).

### L5 — Refactors of scoring code are checked with a parity run (2026-10-29)

- **What happened:** the scoring logic was ported from minified code into ~50 small named functions. A wrong `||` default or a swapped branch would silently change results.
- **Check we ran:** the original HTML and the new plugin were served side by side and given the same answers (with the removed agile question set to "no preference"). Across all 87 careers the largest score difference was **1 point** for two very different profiles; top-5 lists and the "compromise" group were identical. The only expected difference is the removed agile factor.
- **Now:** any change to `scoring.js` or `ranking.js` should be checked the same way against the previous release (see [TESTING.md](TESTING.md), "Parity check").

### L6 — `hidden` loses to a CSS `display` rule (2026-10-29)

- **What happened:** the reset styles set `display` on many elements; an element with the `hidden` attribute can still show when a stylesheet sets `display`.
- **Now:** `.itcp-app.itcp-scope [hidden] { display: none !important; }`.

### L7 — Numeric role ids become integer array keys in PHP (2026-10-29)

- **What happened:** PHP turns the array key `"123"` into the integer `123`. A role with a numeric id stored in `saved[$id]` comes back as an `int` key and fails `===` comparisons with strings.
- **Now:** ids from array keys are cast with `(string)` (`RoleRepository::custom_roles()`).

### L8 — Many forms on one admin page: use `wp_nonce_field( …, '_wpnonce', false )` (2026-10-29)

- **What happened:** each row in the careers table has its own delete/restore form. The default `wp_nonce_field()` also prints a referer field, doubling hidden inputs on an 87-row page.
- **Now:** the referer field is turned off; `check_admin_referer()` still verifies the nonce, which is what protects the action.

### L9 — The browser pane gives blank screenshots while it is hidden (2026-10-29)

- **What happened:** during testing, screenshots of the built-in browser came back blank when its panel was not visible.
- **Now:** verify pages with DOM checks (`get_page_text`, JavaScript assertions) and use screenshots only when layout matters and the pane is visible.

### L10 — Plain-object lookups accept `constructor` (2026-10-29)

- **What happened:** the original restore code validated stored answers with `allowed = {}` and `allowed[value]`. For the value `"constructor"`, `allowed["constructor"]` is `Object`'s constructor function, which is truthy, so a tampered or stale value passed validation.
- **Now:** lookups built from data use `Object.create(null)` and exact `=== true` checks, or `hasOwnProperty`. A JavaScript test stores `constructor` and `__proto__` and checks they are dropped.

### L11 — Windows PHP from winget has no `php.ini` (2026-10-29)

- **What happened:** PHP installed with winget runs without `php.ini`, so extensions like `mbstring`, `zip` and `pdo_sqlite` are off. Tests that use `mb_substr` and the zip builder need them.
- **Now:** use a small `php.ini` and run `php -c path\to\php.ini …` (see [TESTING.md](TESTING.md)). GitHub's Ubuntu runners already have these extensions.
