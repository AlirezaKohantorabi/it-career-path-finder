# `tests/DatasetIntegrityTest.php`

**Purpose:** catches broken data before it reaches a site: invalid JSON, wrong references between the five data files, and mismatches between the data and what the JavaScript expects. Run it after every data edit.

## Functions

| Function | What it does |
|---|---|
| `data( $file )` | Helper: decodes a data file. |
| `roles()` | Helper: the careers. |
| `script( $name )` | Helper: source of one JS file. |
| `all_scripts()` | Helper: source of every JS file, by name. |
| `matches( $pattern, $subject )` | Helper: unique first capture group of a regex. |
| `quoted_words( $code )` | Helper: words in single quotes. |
| `rating_problems( $path, $value )` | Helper: problem text when a rating is not an integer 1–5. |
| `text_references( $code )` | Helper: `[group, key]` pairs of interface text the code reads (`text.card.x`, and aliases such as `var texts = finder.text.card;` → `texts.x`). |
| `text_aliases( $code )` | Helper: variable → text group, from `var x = finder.text.group;` plus `planText` → `plan`. |
| `is_text_group_reference( $reference )` | Helper: keeps references to real text groups, ignores `length`/`push`. |
| `test_every_data_file_is_valid_json()` | All five files decode. |
| `test_role_ids_are_unique_and_safe()` | No duplicates; only `a-z0-9_-`, max 64. |
| `test_every_role_has_required_text()` | `id`, `title_fa`, `category_id`, `plain_description` are not empty. |
| `test_every_role_rating_is_complete_and_between_one_and_five()` | Every rating in `role_fields` exists and is an integer 1–5. |
| `test_role_references_exist_in_vocabulary()` | Category, interest tags, workplaces and labels are known values. |
| `test_every_question_has_a_known_section_and_unique_options()` | Section exists, options non-empty and unique, type valid, ids unique. |
| `test_summary_questions_exist()` | Summary chips point to real questions. |
| `test_industry_aliases_use_known_options_and_workplaces()` | Alias keys are industry options; aliases are workplaces (L4). |
| `test_weights_cover_every_scoring_factor()` | `scoring.json` weights = `factors.x =` names in `scoring.js`. |
| `test_penalty_keys_used_in_scoring_exist()` | Every `addPenalty('x')` key exists. |
| `test_message_keys_used_in_scripts_exist()` | Every `addReason/Risk/Gap/Compromise('x')`, `message: 'x'` and `this.message('x')` key exists. |
| `test_interface_text_keys_used_in_scripts_exist()` | Every interface-text key read by the JavaScript exists. |

## Relations

- Reads: everything in [`data/`](../data/roles.md) and [`assets/js/`](../assets/js/scoring.md).
