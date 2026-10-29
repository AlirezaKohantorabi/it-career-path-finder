# Contributing

Rules both of us follow so the code stays easy to change. Persian version of the workflow: [docs/fa/DEVELOPMENT.fa.md](docs/fa/DEVELOPMENT.fa.md).

## Code rules (Clean Code)

1. **One function, one job.** If you need "and" to describe it, split it.
2. **Names say what the function does** (`missing_required_fields()`, `scheduleAutoAdvance()`). No abbreviations except well-known ones.
3. **No comments in code.** Explain *what* with names and *why* in `docs/`. The only exception is the WordPress plugin header.
4. **No magic numbers.** Use a named constant, or put tunable values in `data/scoring.json`.
5. **Small files.** One PHP class per file; one JavaScript module per file.
6. **PHP style:** WordPress coding standards (tabs, spaces inside parentheses, Yoda conditions, snake_case). Every PHP file starts with `defined( 'ABSPATH' ) || exit;`.
7. **JavaScript style:** ES5 (`var`, `function`), `'use strict'`, each file adds one object to `window.ItcpFinder`.
8. **Security:** sanitize every input, escape every output, check capability and nonce on every admin action, never use `innerHTML`. Read [docs/SECURITY.md](docs/SECURITY.md) before touching admin actions.
9. **Visitor-facing words** go in `data/interface-text.json`; admin words go in `__()` with the `it-career-path-finder` text domain.

## Documentation rules

Every change that adds, renames or removes a function updates:

- `docs/code/<same path as the file>.md` — the function list and the "Relations" section.
- `docs/DECISIONS.md` — when you chose between options (add `D<next>`).
- `docs/LESSONS.md` — when you found a bug or made a mistake worth remembering (add `L<next>`).
- `CHANGELOG.md` — always, under the next version.

`php tests/run.php` fails if a code file has no doc page or a function is missing from it.

## Release checklist

1. Pick the version: `PATCH` for fixes, `MINOR` for new features, `MAJOR` for changes that break saved data or shortcodes.
2. Change it in **four places**: `Version:` and `ITCP_VERSION` in `it-career-path-finder.php`, `Stable tag:` in `readme.txt`, and a new `## [x.y.z] — date` heading in `CHANGELOG.md` (also add a line under `== Changelog ==` in `readme.txt`).
3. `php tests/run.php` and the JavaScript test page are green; run the manual checklist in [docs/TESTING.md](docs/TESTING.md) for user-visible changes.
4. Commit, then tag and push:
   ```bash
   git tag v1.0.1
   ```
   ```bash
   git push origin main --tags
   ```
5. The **Release** workflow builds `it-career-path-finder-x.y.z.zip` and attaches it to the GitHub release. Install that zip on the site.

## Commits

- One topic per commit; the first line says what changed and ends with the version in parentheses when it is a release, e.g. `Plain-language box on every career card (1.1.0)`.
- Commits in this repository are authored as `AlirezaKohantorabi <alireza.kohantorabi91082@gmail.com>` or by Khosro under his own GitHub account.
