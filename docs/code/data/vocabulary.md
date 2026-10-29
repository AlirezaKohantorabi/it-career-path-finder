# `data/vocabulary.json`

**Purpose:** the shared words of the dataset: which categories, interest tags, workplaces and labels exist (with Persian labels), how industries map to workplaces, and which rating fields a career has in the admin editor. Format: [DATA.md](../../DATA.md#vocabularyjson).

| Key | Count (1.0.0) | Used for |
|---|---|---|
| `rating_legend` | 5 | Labels of 1–5 in admin dropdowns |
| `categories` | 12 | Career categories (chip on each card; dropdown in admin) |
| `interest_tags` | 17 | Allowed `interest_tags` of careers; labels in "matches your interests in …" |
| `workplace_types` | 46 | Allowed `typical_workplaces`; chips on cards |
| `industry_aliases` | 11 | Industry option → related workplaces (D21, L4) |
| `role_labels` | 8 | Allowed `labels` of careers |
| `role_fields.ratings` | 8 | Single ratings shown in the editor and kept by the sanitizer |
| `role_fields.fit_groups` | 3 | `work_mode_fit`, `client_type_fit`, `work_arrangement_fit` and their options |

## Relations

- Read by: [`RoleSchema`](../src/Data/RoleSchema.md) (admin + sanitizer) and [`FrontendDataset`](../src/Frontend/FrontendDataset.md) (categories, tags, workplaces, aliases for the browser).
- Removing a value that careers still use makes `DatasetIntegrityTest` fail and makes the sanitizer drop it on the next admin save.
