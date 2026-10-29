# `src/Frontend/FrontendDataset.php`

**Purpose:** builds the one array the browser needs: questions, vocabulary pieces, scoring settings, interface text and the effective careers. Only what the browser uses is included (admin-only parts of the vocabulary such as `role_fields` are left out).

## Functions

| Function | What it does |
|---|---|
| `__construct( $bundled, $roles )` | Stores the data sources. |
| `build()` | Returns `sections`, `questions`, `summary_questions`, `categories`, `interest_tags`, `workplace_types`, `industry_aliases` (cast to an object so it is always `{}` in JSON), `scoring`, `text` and `roles` (`RoleRepository::all()`), passed through the `itcp_frontend_dataset` filter. |

## Relations

- Uses: [`BundledData`](../Data/BundledData.md), [`RoleRepository`](../Data/RoleRepository.md).
- Used by: [`DatasetPrinter`](DatasetPrinter.md).
- Read in the browser by: [`dataset.js`](../../assets/js/dataset.md) (`readDataset`), and the shape is mirrored by `buildDataset()` in [`tests/js/tests.js`](../../tests/js/tests.md).
