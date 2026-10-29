# `src/Admin/AboutPage.php`

**Purpose:** the help and credits page (`admin.php?page=itcp-about`): how to use the shortcode, its attributes, the authors, the version and a link to the source code.

## Functions

| Function | What it does |
|---|---|
| `render()` | Renders [`templates/admin/about.php`](../../templates/admin/about.md) with the shortcode tag, version, repository URL and developers. |
| `developers()` | The two authors with their website and GitHub links (names are translatable). |

Constants: `SLUG` = `itcp-about`, `REPOSITORY_URL`.

## Relations

- Uses: [`RequestGuard`](RequestGuard.md), [`Shortcode::TAG`](../Frontend/Shortcode.md), [`View`](../Support/View.md), `ITCP_VERSION`.
