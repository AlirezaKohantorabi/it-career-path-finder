# `tests/Support/TestCase.php`

**Purpose:** base class for every test class, with a few readable assertions. A failed assertion throws `AssertionFailed`, which [`run.php`](../run.md) reports as a failure (any other exception is reported as an error).

## Functions

| Function | What it does |
|---|---|
| `set_up()` | Runs before each test; clears in-memory options. Override and call `parent::set_up()` to add more. |
| `assert_same( $expected, $actual, $message )` | Strict `===` comparison; shows both values on failure. |
| `assert_true( $condition, $message )` | Condition must be true. |
| `assert_false( $condition, $message )` | Condition must be false. |
| `assert_contains( $needle, $haystack, $message )` | Strict `in_array`. |
| `assert_empty( $list, $message )` | List must be empty; on failure prints each item on its own line (used for "problems found" lists). |
| `fail( $message )` | Throws `AssertionFailed`. |

## Relations

- Extended by every `tests/*Test.php`. Loaded by [`bootstrap.php`](../bootstrap.md).
