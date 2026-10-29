# `assets/js/state.js` → `ItcpFinder.State`

**Purpose:** the shape of one tool's state and the rules for answers.

```js
{
  currentIndex: 0,              // question on screen
  maxReached: 0,                // furthest question unlocked (dots after it are disabled)
  answers: { questionId: 'value' | ['values'] },
  strictInsuranceFilter: false, // "only jobs likely to have insurance" tweak
  showingResults: false
}
```

## Functions

| Function | What it does |
|---|---|
| `createState()` | A fresh state. |
| `isMultiSelect(question)` | `type === 'multi_select'`. |
| `hasAnswer(question, answers)` | A non-empty array or a non-empty value exists. |
| `isComplete(question, answers)` | Optional questions are always complete; required ones need an answer. |
| `firstIncompleteIndex(questions, answers)` | Position of the first incomplete question, or `-1`. |
| `toggledSelection(current, value, checked)` | New multi-select list: picking `no_preference` clears the rest; picking anything else removes `no_preference`; unchecking removes the value. |
| `setAnswer(state, questionId, value)` | Stores an answer; an empty list deletes it. |
| `hasAnyAnswers(state)` | Whether any answer exists (restart asks for confirmation only then). |
| `resetState(state)` | Resets the same object to a fresh state (other modules keep their reference). |

## Relations

- Uses: [`format`](format.md). Used by: [`storage`](storage.md), [`finder`](finder.md), [`question-view`](question-view.md), [`progress-view`](progress-view.md).
