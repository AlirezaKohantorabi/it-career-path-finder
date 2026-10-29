# `assets/css/app.css`

**Purpose:** the tool's whole look, ported from the original (D16) and extended for the new parts.

## How it is organised

1. **Variables and root** on `.itcp-app.itcp-scope`: colours (`--p` navy, `--ac` sky, `--ok` teal, `--wr` amber, `--er` red…), radii, shadow. `font-family: inherit` so the theme's font is used (D17).
2. **Reset** of headings, lists, buttons, labels and SVGs inside the tool, so theme styles don't leak in.
3. Header (`.itcp-head`, chips, progress bar), dots (`.itcp-dots`, `.itcp-dot.is-done/.is-cur`), question body (`.itcp-q-*`, `.itcp-hint`, `.itcp-opts`, `.itcp-opt.is-sel`, `.itcp-ind`), error (`.itcp-err.is-on` with a shake), sticky footer (`.itcp-foot`) and buttons (`.itcp-btn-p/-s/-g`).
4. Results: summary (`.itcp-sum`, `.itcp-chip`, `.itcp-jump`), conflicts (`.itcp-box-warn`), tweaks, section headings (`.itcp-sec`), cards (`.itcp-role.is-p/.is-c/.is-l`), score ring (`.itcp-ring`, conic gradient from `--v`), boxes (`.itcp-box.is-ok/.is-wr/.is-pl/.is-cm`), lists, 30-day steps (Persian counters), details, chips, meters, caveat, full ranking (`.itcp-rk`), action bar, toast.
5. Responsive rules: `min-width: 640px` (two columns, bigger paddings), `max-width: 480/380/360px` (tighter phones).
6. `prefers-reduced-motion`: no transitions or animations.
7. **Print**: hides navigation, buttons and everything outside the tool (`.itcp-print-hide`), keeps colours.
8. Additions in 1.0.0: `[hidden]` guard (L6), the plain-explanation box (`.itcp-box.is-plain`, `.itcp-plain-tx` with `white-space: pre-line` so admin line breaks show), summary note, meter layout (`.itcp-mt-r`), note text (`.itcp-note`), highlight of a card jumped to (`.itcp-role.is-focus`), error state (`.is-error` hides dots and footer), and the off-screen copy buffer (`.itcp-copy-buffer`, global because it is appended to `body`).

## Why `.itcp-app.itcp-scope`

Two classes give specificity (0,2,0)+, enough to beat common theme selectors like `.entry-content h2`, while allowing several tools per page (an id would allow only one).

## Relations

- Loaded by: [`Assets`](../../src/Frontend/Assets.md) (handle `itcp-app`). Classes are created by the view modules in [`assets/js/`](../js/finder.md) and the template [`shortcode-app.php`](../../templates/shortcode-app.md).
- No functions.
