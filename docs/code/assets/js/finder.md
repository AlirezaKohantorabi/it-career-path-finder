# `assets/js/finder.js` → `ItcpFinder.Finder`

**Purpose:** the controller of **one** tool instance. It owns the state, reacts to buttons and answers, saves progress, and tells the views what to draw. Views call back into it; they never change state themselves.

## Lifecycle

```
new Finder(root, dataset, instanceNumber)
  parts  ← every [data-itcp="…"] inside root (PART_NAMES)
  state  ← Storage.restore(…) or a fresh state
start()
  bindControls()  previous / next / restart / form submit
  renderTimeChip
  showResults(false) if the restored state was on results, else showQuestion(false)
```

**Auto-advance:** when a single-choice option is chosen by pointer (mouse/touch) within 1.2 s of pressing it, the next question opens after 0.38 s. Keyboard choices never auto-advance, so keyboard users can review their choice.

## Functions

| Function | What it does |
|---|---|
| `camelCase(name)` | `next-label` → `nextLabel` for the `parts` object. |
| `collectParts(root)` | Finds every named part inside the root. |
| `hasRequiredParts(root)` | All parts exist (also exposed as `Finder.hasRequiredParts`). |
| `Finder(root, dataset, instanceNumber)` | Constructor (see above). |
| `start()` | Binds controls and shows the first screen; marks the root `data-itcp-ready`. |
| `bindControls()` | Click handlers for previous/next/restart; blocks form submission. |
| `currentQuestion()` | The question on screen. |
| `isLastQuestion()` | Whether it is the last one. |
| `scoringContext()` | `{ index, text, scoring, strictInsuranceFilter }` for scoring. |
| `persist()` | Saves the state. |
| `updateFooter()` | Disables "previous" on the first question; "next" becomes "دیدن نتیجه" on the last. |
| `showQuestion(moveFocus)` | Hides results, draws progress, dots and the question. |
| `showResults(moveFocus)` | Hides the form, draws completed progress, dots and the results. |
| `moveTo(position)` | Goes to a question and unlocks it. |
| `finishQuestionnaire()` | On the last "next": jumps to the first unanswered required question with an error, or shows results. |
| `goNext()` | Validates the current question, then moves on or finishes. |
| `goPrevious()` | One question back. |
| `goToQuestion(position)` | From a dot; only unlocked questions. |
| `selectOption(question, value, checked)` | Stores the answer (toggling for multi-select), clears errors, updates dots, saves; may schedule auto-advance. |
| `reselectOption(question, value)` | Clicking the already-chosen single option also advances. |
| `recordPointer()` | Remembers when a pointer pressed an option. |
| `advanceIfPointerSelected()` | Schedules auto-advance only after a recent pointer press. |
| `scheduleAutoAdvance()` | Moves on after the delay if the visitor is still on the same question. |
| `cancelAutoAdvance()` | Stops a pending auto-advance. |
| `restart()` | After confirmation (if anything was answered), clears state and storage and shows question 1. |
| `editAnswers()` | From results back to question 1, keeping answers. |
| `applyTweak(change)` | Changes one answer or toggles the strict insurance filter, saves, re-renders results, shows a toast. |
| `revealRoleCard(role)` | Scrolls to a card and highlights it briefly. |
| `showToast(message)` | Shows a short message at the bottom for 2.6 s. |

## Relations

- Uses: [`dataset`](dataset.md), [`state`](state.md), [`storage`](storage.md), [`progress-view`](progress-view.md), [`question-view`](question-view.md), [`results-view`](results-view.md), [`role-card`](role-card.md), [`scroll`](scroll.md), [`dom`](dom.md).
- Created by: [`boot.js`](boot.md). Markup: [`templates/shortcode-app.php`](../../templates/shortcode-app.md).
