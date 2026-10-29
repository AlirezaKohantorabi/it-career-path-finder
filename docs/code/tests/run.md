# `tests/run.php`

**Purpose:** a tiny test runner with no dependencies (D22). Run from the repository root:

```bash
php tests/run.php
```

It loads [`bootstrap.php`](bootstrap.md), finds every `tests/*Test.php`, runs each public method whose name starts with `test_` on a fresh object (after `set_up()`), prints `.` or `F`, then a summary and the failure details. Exit code 0 = all passed, 1 = something failed (CI uses this).

## Functions

| Function | What it does |
|---|---|
| `itcp_test_files()` | Sorted list of `*Test.php` files. |
| `itcp_test_class_for( $file )` | `ItCareerPathFinder\Tests\<FileName>`. |
| `itcp_test_methods( $class_name )` | Method names starting with `test_`. |
| `itcp_run_test( $class_name, $method )` | Runs one test; returns `null` on success or the failure/error message (with file and line for PHP errors). |
| `itcp_run_all_tests()` | Runs everything, prints the report, returns the exit code. |

## Relations

- Uses: [`bootstrap.php`](bootstrap.md), [`Support/TestCase.php`](Support/TestCase.md) (`AssertionFailed`). Runs every test class.
