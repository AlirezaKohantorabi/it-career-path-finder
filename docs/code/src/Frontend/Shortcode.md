# `src/Frontend/Shortcode.php`

**Purpose:** the `[it_career_path_finder]` shortcode. Outputs the empty tool shell (the browser code fills it), makes sure CSS/JS are loaded, and asks for the dataset to be printed.

```
[it_career_path_finder]
[it_career_path_finder title="…" subtitle="…"]
```

## Functions

| Function | What it does |
|---|---|
| `__construct( $assets, $printer, $bundled )` | Stores its collaborators. |
| `register()` | Adds the shortcode and hooks `enqueue_for_current_post` on `wp_enqueue_scripts` (priority 20). |
| `render( $attributes )` | Enqueues assets, numbers the instance (1, 2, … per page), renders [`templates/shortcode-app.php`](../../templates/shortcode-app.md), removes whitespace between tags, and appends whatever `DatasetPrinter::request()` returns. |
| `enqueue_for_current_post()` | On a single post/page whose content contains the shortcode, enqueues assets early so the CSS is printed in `<head>` (no flash of unstyled content). |
| `current_post_uses_shortcode()` | `is_singular()` and `has_shortcode()` on the current post. |
| `app_text( $attributes )` | The `app` group of interface text, with `title`/`subtitle` replaced by the shortcode attributes when given, cleaned with `sanitize_text_field()` (HTML is removed). |
| `without_whitespace_between_tags( $markup )` | Removes newlines/spaces between `>` and `<` so `wpautop` or page builders cannot inject `<p>`/`<br>` into the tool (L1). |

`TAG` is the shortcode name; the admin pages show it from here.

## Relations

- Uses: [`Assets`](Assets.md), [`DatasetPrinter`](DatasetPrinter.md), [`BundledData`](../Data/BundledData.md), [`View`](../Support/View.md).
- Used by: WordPress (shortcode callback); registered by [`Plugin`](../Plugin.md); `TAG` used by [`RolesPage`](../Admin/RolesPage.md) and [`AboutPage`](../Admin/AboutPage.md).
- Its output is mounted in the browser by [`boot.js`](../../assets/js/boot.md).
