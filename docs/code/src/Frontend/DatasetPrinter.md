# `src/Frontend/DatasetPrinter.php`

**Purpose:** prints the dataset **once per page**, however many shortcodes the page has, as

```html
<script type="application/json" id="itcp-dataset">{…}</script>
```

It is printed in `wp_footer` (priority 5, before the scripts). If a shortcode is rendered after the footer was already printed, the block is returned to the shortcode instead (D4).

## Functions

| Function | What it does |
|---|---|
| `__construct( $dataset )` | Stores the [`FrontendDataset`](FrontendDataset.md). |
| `register()` | Hooks `print_if_requested` on `wp_footer`. |
| `request()` | Called by each shortcode render. Before the footer: remembers to print and returns `''`. After the footer: returns the block itself. |
| `print_if_requested()` | In the footer: prints the block if any shortcode asked for it. |
| `take_markup()` | Returns the block the first time and `''` afterwards, so it is never printed twice. |
| `markup()` | The `<script type="application/json">` element with an escaped id. |
| `encoded_dataset()` | JSON with `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE`. The HEX flags make `<`, `>`, `&`, `'`, `"` impossible inside the block, so data can never close the script tag (D19). Persian stays readable UTF-8 (half the size of `\u` escapes). |

`ELEMENT_ID` (`itcp-dataset`) must match `DATASET_ELEMENT_ID` in [`dataset.js`](../../assets/js/dataset.md).

## Relations

- Uses: [`FrontendDataset`](FrontendDataset.md).
- Used by: [`Shortcode`](Shortcode.md), registered by [`Plugin`](../Plugin.md).
