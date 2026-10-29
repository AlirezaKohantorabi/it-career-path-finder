# `tests/js/tests.js`

**Purpose:** browser tests for the JavaScript modules. Loads the five data files with `fetch`, builds the same dataset shape as [`FrontendDataset`](../../src/Frontend/FrontendDataset.md), runs the checks, and reports them on [`run.html`](run.md). Results are also available as `window.ItcpTestResults` for automation.

## Functions

| Function | What it does |
|---|---|
| `check(name, condition, detail)` | Records one pass/fail. |
| `loadJson(name)` | Fetches `data/<name>.json` without cache. |
| `buildDataset(files)` | Assembles the browser dataset from the five files. |
| `contextFor(dataset, strictInsurance)` | A scoring context like `Finder.scoringContext()`. |
| `findRole(dataset, roleId)` | One career by id. |
| `isCleanText(text)` | Non-empty text without `undefined` or `{`. |
| `completeAnswers(dataset)` | Answers every question with its first option. |
| `testFormat()` | Persian digits, templates, rating normalization. |
| `testState()` | Multi-select rules around `no_preference`. |
| `testStorage(dataset)` | Restoring drops invalid values (`constructor`, `__proto__`), cannot skip ahead, does not reopen results early. |
| `testScoring(dataset)` | Remote-only penalty on an on-site job, none on a remote job; scores within bounds; 1–3 reasons and 1–2 risks; all texts filled. |
| `testRanking(dataset)` | At least 3 recommended; groups do not overlap; sorted; strict insurance filter works. |
| `testConflicts(dataset)` | Freelance + mandatory insurance found once; no answers → no conflicts. |
| `testPlan(dataset)` | Four filled weeks and an experiment. |
| `testRenderedResults(dataset)` | Starts a real `Finder` on the fixture, renders results and a question: no `undefined`, no `{placeholders}`, plain explanation first, all options drawn. |
| `report()` | Writes the results list, summary and page title. |
| `runAll(dataset)` | Runs every test, turning a crash into a failed check. |

## Relations

- Tests: [`format`](../../assets/js/format.md), [`state`](../../assets/js/state.md), [`storage`](../../assets/js/storage.md), [`scoring`](../../assets/js/scoring.md), [`ranking`](../../assets/js/ranking.md), [`conflicts`](../../assets/js/conflicts.md), [`plan`](../../assets/js/plan.md), [`finder`](../../assets/js/finder.md) and the views.
