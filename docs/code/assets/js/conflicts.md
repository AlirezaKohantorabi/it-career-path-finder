# `assets/js/conflicts.js` → `ItcpFinder.Conflicts`

**Purpose:** finds contradictions between the visitor's own answers and explains them at the top of the results (yellow box). Independent of careers.

| Check | Message key |
|---|---|
| Freelance or own business **and** mandatory insurance | `conflict_freelance_insurance` |
| Individual clients **and** interest in systems, security or data | `conflict_individual_enterprise` |
| Starting from zero (beginner or career switcher), under 3 months, **and** above-market or very high income | `conflict_fast_high_income` |
| No on-call **and** interest in systems | `conflict_no_on_call_systems` |
| Beginner/learner/switcher **and** no portfolio | `conflict_no_portfolio` |
| International remote **and** basic or reading-only English | `conflict_international_english` |

## Functions

| Function | What it does |
|---|---|
| `chose(answers, questionId, value)` | Whether an answer is (or contains) a value. |
| `choseAny(answers, questionId, values)` | Whether any of the values was chosen. |
| `freelanceWithMandatoryInsurance(answers)` | Check 1. |
| `individualClientWithEnterpriseInterests(answers)` | Check 2. |
| `freshStartWithHighIncome(answers)` | Check 3. |
| `noOnCallWithSystemsInterest(answers)` | Check 4. |
| `beginnerWithoutPortfolio(answers)` | Check 5. |
| `internationalRemoteWithLimitedEnglish(answers)` | Check 6. |
| `listConflicts(answers, messages)` | **Public.** Messages of every check that applies, in table order. |

To add a check: write a small predicate, add `{ message, applies }` to `CHECKS`, and add the message to `interface-text.json → messages`.

## Relations

- Used by: [`results-view`](results-view.md). Messages: [`interface-text.json`](../../data/interface-text.md).
