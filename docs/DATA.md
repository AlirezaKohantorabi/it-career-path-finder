# Data files

All content lives in `data/`. The plugin reads these files on every page that shows the tool; changes take effect immediately (clear your page cache). Persian how-to: [fa/DATA.fa.md](fa/DATA.fa.md). Per-file details: [code/data/](code/data/).

After any edit, run `php tests/run.php` — `DatasetIntegrityTest` catches broken JSON, unknown ids, missing texts and ratings outside 1–5.

| File | Holds | Read by |
|---|---|---|
| `questionnaire.json` | Sections, questions with options and hints, which answers appear as chips in the result summary | Browser (`dataset.js`, views, scoring) |
| `vocabulary.json` | Categories, interest tags, workplace types, industry aliases, role labels, rating legend, the rating fields shown in the admin editor | Browser and admin (`RoleSchema`, `RoleSanitizer`) |
| `scoring.json` | Weights of each factor, score bounds, grouping thresholds, penalties | Browser (`scoring.js`, `ranking.js`) |
| `interface-text.json` | Every word the visitor sees outside questions and careers: buttons, headings, reasons, risks, conflicts, the 30-day plan | Browser and the shortcode template |
| `roles.json` | The 87 bundled careers | `RoleRepository` (combined with admin edits) |

## roles.json

```json
{
  "schema_version": "2.0.0",
  "roles": [
    {
      "id": "frontend_developer",
      "title_fa": "توسعه‌دهنده فرانت‌اند",
      "title_en": "Front-end Developer",
      "category_id": "software_development",
      "plain_description": "Plain, non-technical explanation shown first on the card.",
      "ratings_1_to_5": {
        "entry_difficulty": 3,
        "relative_market_opportunity": 4,
        "income_ceiling": 4,
        "job_stability": 3,
        "insurance_likelihood_in_employee_roles": 3,
        "stress_level": 3,
        "responsibility_level": 3,
        "routine_level": 2,
        "work_mode_fit": { "remote": 5, "hybrid": 5, "onsite": 3 },
        "client_type_fit": { "individual": 4, "small_business": 4, "medium_large_organization": 4, "public_sector": 2 },
        "work_arrangement_fit": { "employee": 4, "freelance": 4, "project_contract": 4, "consultant": 3, "self_employed": 3 }
      },
      "interest_tags": ["coding", "visual", "product", "problem_solving"],
      "typical_workplaces": ["software_company", "digital_agency", "ecommerce", "startup", "in_house_it"],
      "essential_skills": ["HTML/CSS", "JavaScript/TypeScript", "React یا Vue", "Git", "مصرف API"],
      "recommended_entry_route": "…",
      "market_caveat": "…",
      "labels": ["market_opportunity_relatively_good", "remote_compatible", "freelance_possible"]
    }
  ]
}
```

| Field | Required | Rules |
|---|---|---|
| `id` | yes | `a-z`, `0-9`, `_`, `-`; unique; never change it (admin edits are stored by id) |
| `title_fa` | yes | Shown as the card title |
| `title_en` | no | Shown small, left-to-right |
| `category_id` | yes | One of `vocabulary.json` → `categories[].id` |
| `plain_description` | yes | Very simple, non-IT explanation of what the job is and does |
| `ratings_1_to_5` | yes | Every key listed in `vocabulary.json` → `role_fields`; integers 1–5. `entry_difficulty`: 5 = hardest to enter. `routine_level`: 5 = most routine |
| `interest_tags` | – | Values from `interest_tags[].value`; the main input of the interest match (25% weight) |
| `typical_workplaces` | – | Values from `workplace_types[].value`; used for the industry match and shown as chips |
| `essential_skills` | – | The first two feed the 30-day plan |
| `labels` | – | Values from `role_labels[].value`. `high_stress_or_urgency_possible` affects visitors who refuse on-call work |

## questionnaire.json

- `sections`: `[{ "id", "title" }]` in display order. The header shows "بخش n از total: title".
- `questions`: in display order. Each has `id`, `section` (a section id), `title`, `type` (`single_select` or `multi_select`), `required`, `hint`, `options: [{ "value", "label" }]`.
- `summary_questions`: question ids whose answers appear as chips at the top of the results.

The scoring code reads answers by question id (`primary_goal`, `client_type`, `work_arrangement`, `income_target`, `security_priority`, `insurance_requirement`, `work_mode`, `stress_tolerance`, `responsibility_preference`, `routine_preference`, `interests`, `current_level`, `time_to_entry`, `english_level`, `location_scope`, `industry_preferences`, `customer_contact`, `on_call_tolerance`, `must_have_portfolio`). Labels and hints can change freely; changing an id or an option value needs a matching change in `assets/js/scoring.js` or `conflicts.js`. A new question is shown and stored, but affects scores only after code uses it.

A multi-select option with value `no_preference` clears the other choices when picked.

## scoring.json

- `weights`: percentage weight of each factor. Keys must match the factors in `scoring.js` (`interest_match`, `work_arrangement_match`, `client_type_match`, `work_mode_match`, `entry_accessibility_match`, `income_match`, `stability_match`, `insurance_match`, `stress_match`, `responsibility_match`, `routine_match`). They sum to 100 today; they do not have to.
- `meaning_interest_bonus`: extra weight for interest when the goal is "I want to love my job".
- `score_bounds`: final scores are clamped to `min`–`max`.
- `grouping`: `primary_min_score`, `primary_max_count`, `primary_min_count` (the list is topped up to this many), `compromise_min_score`, `compromise_max_count`, `low_shown_count`.
- `penalties`: points subtracted for hard conflicts (remote-only vs on-site job, beginner in a hurry vs hard job, mandatory insurance vs unlikely insurance, no on-call vs urgent job, individual clients vs organizational job). Any penalty moves a job out of the "recommended" group.

## interface-text.json

Groups: `app` (header, buttons), `question`, `results`, `card`, `separators`, `plan`, `messages` (reasons, risks, compromises, conflicts). `{name}` placeholders are filled by the code; keep them when rewording. Every key used by the JavaScript is checked by `DatasetIntegrityTest::test_interface_text_keys_used_in_scripts_exist`.

## vocabulary.json

- `categories`, `interest_tags`, `workplace_types`, `role_labels`: the allowed values for role fields, with Persian labels.
- `industry_aliases`: industry option → list of workplace values it also matches.
- `rating_legend`: labels for 1–5 in the admin dropdowns.
- `role_fields.ratings` and `role_fields.fit_groups`: the rating fields, their labels and help texts in the admin editor; the sanitizer only keeps ratings listed here.

## Sources and principles of the original dataset

The ratings are qualitative estimates for a first version, not statistics from job ads; they should be reviewed every 3–6 months. Context sources used for the original dataset: a published summary of the Jobvision 1404 salary report (eghtesaddigital.com), Quera's job-ad channel, and the Jobiva job search engine. Principles kept from the original: suggest several testable paths instead of one "right" job; keep client type separate from contract type; show limits as trade-offs, never as "you can't"; give a 30-day experiment instead of promising a job.
