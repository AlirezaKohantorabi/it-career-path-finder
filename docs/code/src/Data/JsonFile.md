# `src/Data/JsonFile.php`

**Purpose:** the only place that turns JSON text into PHP arrays. Always returns an array (empty on any problem) so callers never deal with `null` or exceptions.

## Functions

| Function | What it does |
|---|---|
| `decode_file( $path )` | Reads a file if it is readable and decodes it; `[]` if missing or unreadable. |
| `decode_text( $text )` | Decodes JSON into associative arrays with a nesting limit of 32; `[]` when the result is not an array. |
| `without_byte_order_mark( $text )` | Removes a UTF-8 BOM, which some Windows editors (e.g. Notepad) add and which breaks `json_decode`. |

## Relations

- Used by: [`BundledData`](BundledData.md) (data files) and [`RoleImporter`](RoleImporter.md) (uploaded or pasted JSON).
