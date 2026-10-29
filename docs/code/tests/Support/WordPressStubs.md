# `tests/Support/WordPressStubs.php`

**Purpose:** minimal stand-ins for the WordPress functions the tested classes call. Options live in memory (`$GLOBALS['itcp_test_options']`) and are cleared before every test.

## Functions

| Function | What it does |
|---|---|
| `itcp_test_reset_options()` | Empties the in-memory options (called by `TestCase::set_up()`). |
| `get_option( $name, $default_value )` | Reads an in-memory option. |
| `update_option( $name, $value, $autoload )` | Writes an in-memory option. |
| `delete_option( $name )` | Removes an in-memory option. |
| `apply_filters( $hook_name, $value )` | Returns the value unchanged (no filters in tests). |
| `sanitize_key( $key )` | Lowercase, keep `a-z0-9_-` (same rule as WordPress). |
| `sanitize_text_field( $text )` | Strip tags, collapse whitespace, trim (simplified WordPress behaviour). |
| `sanitize_textarea_field( $text )` | Strip tags, trim, keep line breaks (simplified). |
| `trailingslashit( $path )` | Ensures one trailing slash. |

## Notes

The sanitize stubs are simplified; they are close enough to test our rules (what is kept and dropped), not WordPress itself. When a tested class starts calling a new WordPress function, add a stub here.

## Relations

- Loaded by: [`bootstrap.php`](bootstrap.md). Used by: [`RoleChanges`](../../src/Data/RoleChanges.md), [`RoleSanitizer`](../../src/Data/RoleSanitizer.md) and others under test.
