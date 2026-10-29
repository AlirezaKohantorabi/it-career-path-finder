# `assets/js/format.js` → `ItcpFinder.Format`

**Purpose:** small, pure helper functions with no DOM and no state. Loaded first; every other module uses it.

## Functions

| Function | What it does |
|---|---|
| `toPersianDigits(value)` | Replaces `0-9` with `۰-۹` in any value turned into text. |
| `fillTemplate(template, values)` | Replaces `{name}` placeholders with `values.name`; unknown placeholders are left as they are (so a typo is visible, and the JS test catches it). |
| `uniqueValues(list)` | The list without repeated items, keeping the first occurrence. |
| `clamp(value, min, max)` | Limits a number to a range. |
| `ratingOf(map, key, fallback)` | `map[key]` when it is a number, otherwise `fallback`. Mirrors the original code's different defaults (2 or 3 depending on the place). |
| `normalizeRating(rating)` | Maps a 1–5 rating to 0–1 (1 → 0, 3 → 0.5, 5 → 1); a missing rating counts as 3. |
| `listOrFallback(value, fallback)` | The value when it is a non-empty array, otherwise the fallback. |
| `listOrEmpty(value)` | The value when it is an array, otherwise `[]`. |
| `safely(action, fallback)` | Runs `action()` and returns its result, or `fallback` if it throws. Used for browser APIs that may be blocked (storage, `matchMedia`, `focus` options) so the tool keeps working. |

## Relations

- Used by: almost every module, always as `ns.Format` (often aliased `F`).
