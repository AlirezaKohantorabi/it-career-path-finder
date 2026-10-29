# `tests/DocsCoverageTest.php`

**Purpose:** keeps `docs/code/` complete (D13). Fails when a code file has no page, or when a function in a code file is not mentioned (as `` `name `` in backticks) on its page.

Code files checked: root `*.php`, `src/**/*.php`, `templates/**/*.php`, `assets/js/*.js`, `assets/css/*.css`, `data/*.json`, `tests/*.php`, `tests/Support/*.php`, `tests/js/*`, `tools/*.php` (every `index.php` is skipped). Page path: `docs/code/<same path with .md instead of the extension>`.

## Functions

| Function | What it does |
|---|---|
| `code_files()` | Relative paths of all code files. |
| `doc_path( $code_file )` | The expected page path. |
| `function_names( $code_file )` | Function names of a PHP or JS file (none for CSS/JSON/HTML). |
| `php_function_names( $source )` | Named functions and methods via PHP's tokenizer (closures skipped). |
| `next_identifier( $tokens, $position )` | The name after a `function` keyword, or `null` for closures. |
| `javascript_function_names( $source )` | `function name(` declarations and `X.prototype.name = function`. |
| `test_every_code_file_has_a_document()` | Every code file has a page. |
| `test_every_function_is_documented()` | Every function name appears on its page. |
