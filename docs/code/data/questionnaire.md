# `data/questionnaire.json`

**Purpose:** the questions visitors answer, in display order, grouped into five sections. Full format: [DATA.md](../../DATA.md#questionnairejson); Persian how-to: [DATA.fa.md](../../fa/DATA.fa.md).

## Current content (1.0.0)

| # | id | Section | Type | Notes |
|---|---|---|---|---|
| 1 | `primary_goal` | goals | single | Fast entry, income, stability, freedom, remote, meaning, growth |
| 2 | `client_type` | goals | single | Individuals, small business, large organizations, public sector, no preference |
| 3 | `work_arrangement` | goals | multi | **شیوه کار** (D8): employment with fixed salary, contracting for companies, project/freelance, consulting, own business, internship |
| 4 | `income_target` | money | single | |
| 5 | `security_priority` | money | single | |
| 6 | `insurance_requirement` | money | single | |
| 7 | `work_mode` | style | multi | remote, hybrid, onsite, field work, flexible |
| 8 | `stress_tolerance` | style | single | |
| 9 | `responsibility_preference` | style | single | |
| 10 | `routine_preference` | style | single | |
| 11 | `interests` | skills | multi | The 16 interest tags (most important factor) |
| 12 | `current_level` | skills | single | |
| 13 | `time_to_entry` | skills | single | |
| 14 | `english_level` | skills | single | |
| 15 | `location_scope` | place | single | |
| 16 | `industry_preferences` | place | multi, optional | Uses `industry_aliases` from the vocabulary |
| 17 | `customer_contact` | place | single | |
| 18 | `on_call_tolerance` | place | single | |
| 19 | `must_have_portfolio` | place | single | |

The original question 11 (`team_method`, agile) was removed (D8).

## Relations

- Read by: [`BundledData::questionnaire()`](../src/Data/BundledData.md) → [`FrontendDataset`](../src/Frontend/FrontendDataset.md) → browser ([`dataset.js`](../assets/js/dataset.md), [`question-view.js`](../assets/js/question-view.md), [`progress-view.js`](../assets/js/progress-view.md)).
- Question ids and option values are used by [`scoring.js`](../assets/js/scoring.md), [`conflicts.js`](../assets/js/conflicts.md) and [`results-view.js`](../assets/js/results-view.md) (tweaks); change them only together with that code.
- Checked by [`DatasetIntegrityTest`](../tests/DatasetIntegrityTest.md).
