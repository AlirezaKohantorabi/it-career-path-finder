# Security

What can go wrong, and what the plugin does about it. Report security problems privately to the authors instead of opening a public issue.

## Attack surface

| Surface | Who can reach it | Protection |
|---|---|---|
| Shortcode output | Anyone | No input from visitors reaches PHP. The only inputs are the shortcode attributes `title` and `subtitle`, written by editors; they are cleaned with `sanitize_text_field()` and printed with `esc_html()`. |
| Dataset JSON in the page | Anyone (read-only) | Data is public by design. It is encoded with `JSON_HEX_TAG`, `JSON_HEX_AMP`, `JSON_HEX_APOS` and `JSON_HEX_QUOT`, so no `</script>` or quote can end the block early. The block has `type="application/json"` and is never executed. |
| Browser code | Anyone | All text from data is inserted with `textContent`; there is no `innerHTML` anywhere. Icons are built with `createElementNS` from constant shapes. Saved answers from `sessionStorage` are re-validated against the current options before use (L10). |
| Admin pages | Users with `manage_options` | Each page checks `RequestGuard::current_user_can_manage()` before rendering. |
| Admin actions (`admin-post.php`) | Logged-in users | Each action calls `RequestGuard::verify_action()`: capability check **and** `check_admin_referer()` nonce check. Delete/restore nonces include the role id, so a nonce for one role cannot delete another. No `admin_post_nopriv_*` handlers exist, so logged-out requests are rejected by WordPress. |
| JSON import | Admins | Upload must be a real upload (`is_uploaded_file`), end in `.json`, and be at most 2 MB; pasted text is limited to 2 MB. The file is read from PHP's temporary location and never saved. Decoding is limited to depth 32 and 500 roles. Every role goes through `RoleSanitizer`; incomplete roles are skipped; if nothing is valid, nothing changes. |
| JSON export | Admins | Sent with `Content-Disposition: attachment`, `Content-Type: application/json` and `X-Content-Type-Options: nosniff`, so browsers download it instead of rendering it. |

## Sanitizing rules (RoleSanitizer)

- `id`: `sanitize_key()` (lowercase letters, digits, `_`, `-`), max 64 characters, cannot be changed after creation, must not collide with an existing or deleted bundled role.
- One-line text: `sanitize_text_field()`, max 200 characters. Paragraphs: `sanitize_textarea_field()`, max 2000. Skills: one per line, max 20 lines of 120 characters.
- Category, interest tags, workplaces, labels: only values listed in `data/vocabulary.json`; everything else is dropped.
- Ratings: numbers rounded and clamped to 1–5; missing or non-numeric values become 3.
- Unknown fields are dropped: the output always has exactly the documented role shape.

## Output escaping

Admin templates escape every value at the point of output: `esc_html()`, `esc_attr()`, `esc_url()`, `esc_textarea()`, `selected()`, `checked()`. Notices only print messages from a fixed list chosen by a key from the URL; counts in the URL are passed through `absint()`.

## Other measures

- Every PHP file starts with `defined( 'ABSPATH' ) || exit;` so it does nothing when opened directly; `uninstall.php` checks `WP_UNINSTALL_PLUGIN` instead.
- Every folder has an empty `index.php` so servers without directory-listing protection show nothing.
- Templates are loaded by name through `View`, which accepts only `[a-z0-9-]` path segments, so no user input can choose a file.
- The plugin writes no files at runtime.
- Uninstalling deletes the `itcp_role_changes` option on every site of a multisite network.

## Verified on 2026-10-29 (WordPress 7.1.4, PHP 8.3)

- POST to `itcp_delete_role` without a nonce → 403; with a fake nonce (`itcp_reset_roles`) → 403; export without a nonce → 403; logged-out request → 400. Nothing changed.
- A role saved with id `Test Job<script>` became `testjobscript`; a description containing `<img src=x onerror=alert(1)>` was stored and shown without the tag.
- A shortcode attribute containing `<b>` was printed as plain text.
