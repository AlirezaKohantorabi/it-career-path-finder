# `src/Autoloader.php`

**Purpose:** loads a class file the first time a class from the `ItCareerPathFinder\` namespace is used, so no file needs `require` statements and Composer is not needed (D14).

Mapping: `ItCareerPathFinder\Data\RoleSanitizer` → `src/Data/RoleSanitizer.php`.

## Functions

| Function | What it does |
|---|---|
| `register( $base_directory )` | Remembers the `src/` path and registers `load_class` with `spl_autoload_register`. |
| `load_class( $class_name )` | Called by PHP for unknown classes; ignores classes of other plugins, otherwise requires the matching file if it is readable. |
| `belongs_to_plugin( $class_name )` | True when the class name starts with `ItCareerPathFinder\`. |
| `file_for_class( $class_name )` | Turns the class name into a file path under `src/`. |

## Relations

- Used by: [`it-career-path-finder.php`](../it-career-path-finder.md) and [`tests/bootstrap.php`](../tests/bootstrap.md).
- Loads every other file in `src/`.

## Notes

The test namespace `ItCareerPathFinder\Tests\…` also matches the prefix, but its files are required directly by `tests/run.php`, so the autoloader simply finds nothing for them.
