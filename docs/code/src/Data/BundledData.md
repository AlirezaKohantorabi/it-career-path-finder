# `src/Data/BundledData.php`

**Purpose:** reads the five JSON files shipped in `data/` (or the folder given by the `itcp_data_directory` filter). Each file is read at most once per request.

## Functions

| Function | What it does |
|---|---|
| `__construct( $directory )` | Stores the data folder path (with trailing slash). |
| `questionnaire()` | Contents of `questionnaire.json` (sections, questions, summary questions). |
| `vocabulary()` | Contents of `vocabulary.json` (allowed values, labels, admin rating fields). |
| `scoring()` | Contents of `scoring.json` (weights, bounds, grouping, penalties). |
| `interface_text()` | Contents of `interface-text.json` (all visitor-facing text). |
| `roles()` | The `roles` list from `roles.json`, or `[]`. These are the **bundled** careers, before admin edits. |
| `read( $file_name )` | Decodes a file through `JsonFile::decode_file()` and caches the result in memory. |

Constants `QUESTIONNAIRE_FILE`, `VOCABULARY_FILE`, `SCORING_FILE`, `INTERFACE_FILE`, `ROLES_FILE` hold the file names.

## Relations

- Uses: [`JsonFile`](JsonFile.md).
- Used by: [`RoleRepository`](RoleRepository.md) (bundled roles), [`FrontendDataset`](../Frontend/FrontendDataset.md), [`Shortcode`](../Frontend/Shortcode.md) (app text), [`Plugin`](../Plugin.md) (vocabulary for `RoleSchema`).
- Reads: everything in [`data/`](../../data/roles.md).
