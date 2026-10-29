# `assets/js/dataset.js` → `ItcpFinder.Dataset`

**Purpose:** reads the JSON block printed by [`DatasetPrinter`](../../src/Frontend/DatasetPrinter.md) and builds fast lookups (the "index") that the other modules use.

## Functions

| Function | What it does |
|---|---|
| `readDataset()` | Parses `#itcp-dataset`; `null` if it is missing or not valid JSON. |
| `isUsable(dataset)` | Has questions, a roles list, text and scoring. If not, [`boot.js`](boot.md) shows the "data did not load" message. |
| `createLookup()` | `Object.create(null)`: a map with no inherited keys, so values like `constructor` cannot match by accident (L10). |
| `indexByKey(list, key)` | Map from `item[key]` to item. |
| `labelsByValue(list)` | Map from `value` to `label`. |
| `optionValueSet(question)` | Map of a question's option values to `true`. |
| `positionsById(list)` | Map from `id` to position. |
| `buildIndex(dataset)` | The index: `questions`, `sections`, `sectionPositions`, `questionById`, `categoryById`, `workplaceLabels`, `interestLabels`, `knownInterests` (options of the `interests` question), `industryAliases`, `summaryQuestions`. |
| `optionLabel(index, questionId, value)` | Persian label of an answer value, or the value itself. |
| `optionLabels(index, questionId, values)` | Same for a list. |
| `sectionOf(index, question)` | `{ number, total, title }` of the question's section, for "بخش ۱ از ۵: …". |

## Relations

- Uses: [`format`](format.md). Used by: [`boot`](boot.md) (read), [`finder`](finder.md) (index), [`scoring`](scoring.md), [`progress-view`](progress-view.md), [`results-view`](results-view.md).
- Data shape: built by [`FrontendDataset`](../../src/Frontend/FrontendDataset.md).
