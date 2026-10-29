# `templates/shortcode-app.php`

**Purpose:** the HTML shell of one tool instance. It has no questions or results yet; [`finder.js`](../assets/js/finder.md) fills it. Every printed value is escaped.

**Receives** (`$view`): `instance` (1, 2, … on the page) and `text` (the `app` group of interface text, with the shortcode's `title`/`subtitle`).

## Structure and the `data-itcp` parts

The browser code finds elements by `data-itcp="…"` (not by class or id), so styling and behavior stay separate.

```
div.itcp-app.itcp-scope #itcp-app-<n>  [data-itcp-app] [data-itcp-instance=n]  dir=rtl lang=fa-IR
 ├─ div.itcp-shell                     data-itcp="shell"
 │   ├─ div.itcp-head
 │   │   ├─ span.itcp-chip-h           data-itcp="phase"      "بخش ۱ از ۵: …"
 │   │   ├─ span … span                data-itcp="time"       "۱۹ سؤال · حدود ۳ دقیقه"
 │   │   ├─ button.itcp-restart        data-itcp="restart"
 │   │   ├─ h2.itcp-title / p.itcp-sub
 │   │   ├─ span                       data-itcp="step"       "سؤال ۱ از ۱۹"
 │   │   ├─ span                       data-itcp="left"       time left
 │   │   └─ div.itcp-track (progressbar) data-itcp="bar" > div.itcp-fill data-itcp="fill"
 │   ├─ nav.itcp-dots                  data-itcp="dots"       question buttons
 │   ├─ form.itcp-form                 data-itcp="form"
 │   │   ├─ div.itcp-body              data-itcp="question"   loading text, hidden data-error text, noscript text
 │   │   └─ div.itcp-foot: button data-itcp="previous", button data-itcp="next" > span data-itcp="next-label"
 │   └─ div.itcp-res                   data-itcp="results"
 └─ div.itcp-toast                     data-itcp="toast"
```

`Finder.hasRequiredParts()` checks that all of these exist before starting. If you rename or add a part, update `PART_NAMES` in [`finder.js`](../assets/js/finder.md) and the fixture in [`tests/js/run.html`](../tests/js/run.md).

## Relations

- Rendered by: [`Shortcode::render()`](../src/Frontend/Shortcode.md) through [`View`](../src/Support/View.md).
- Filled by: [`finder.js`](../assets/js/finder.md) and the view modules; started by [`boot.js`](../assets/js/boot.md).
- Styled by: [`app.css`](../assets/css/app.md).
- Has no functions.
