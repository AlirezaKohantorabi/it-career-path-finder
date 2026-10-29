# `src/Support/View.php`

**Purpose:** renders a PHP template from `templates/`, giving it one variable, `$view` (an array). Keeps HTML out of classes.

## Functions

| Function | What it does |
|---|---|
| `render( $template, $view )` | Runs the template and returns its output as a string (used by the shortcode, which must return text). |
| `display( $template, $view )` | Runs the template and prints it directly (used by admin pages). |
| `include_template( $template, $view )` | Checks the name, builds `templates/<name>.php` and includes it if readable; inside it, `$view` is available. |
| `is_safe_name( $template )` | Accepts only names made of `a-z`, `0-9`, `-` separated by `/` (no `..`, no extensions), so a template path can never come from outside. |

## Relations

- Used by: [`Shortcode`](../Frontend/Shortcode.md), [`RolesPage`](../Admin/RolesPage.md), [`RoleEditorPage`](../Admin/RoleEditorPage.md), [`TransferPage`](../Admin/TransferPage.md), [`AboutPage`](../Admin/AboutPage.md), and the template [`role-editor.php`](../../templates/admin/role-editor.md) (for its partials).
- Renders: everything in [`templates/`](../../templates/shortcode-app.md).
