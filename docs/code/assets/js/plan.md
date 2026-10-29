# `assets/js/plan.js` → `ItcpFinder.Plan`

**Purpose:** builds the "30-day plan and low-cost experiment" for a career from the templates in `interface-text.json → plan`.

Values put into the templates: `{skill}` (first essential skill), `{second_skill}` (second), `{route}` (recommended entry route without its final period), `{title}` (Persian title). Defaults from `plan.default_*` are used when a career has no skills or route.

## Functions

| Function | What it does |
|---|---|
| `withoutFinalPeriod(text)` | Removes a trailing `.` so the route fits inside a sentence. |
| `planValues(role, planText)` | The placeholder values for a career. |
| `buildPlan(role, planText)` | **Public.** `{ steps: [week 1–4], experiment }`. |

## Relations

- Uses: [`format`](format.md). Used by: [`role-card`](role-card.md).
