# `assets/js/question-view.js` → `ItcpFinder.QuestionView`

**Purpose:** renders the current question into `data-itcp="question"`: tags (single/multi, required/optional, time left), numbered title, hint, options as large clickable cards, a counter for multi-select, and the error box. Element ids include the instance number so two tools on a page never share ids.

Accessibility: options are real radio/checkbox inputs inside `<label>`s (visually hidden inputs, keyboard works); the group has `role=radiogroup|group`, `aria-labelledby` the title and `aria-describedby` the hint; the error box has `role=alert`.

## Functions

| Function | What it does |
|---|---|
| `elementIds(finder, question)` | Unique ids for title, hint, error and the input name. |
| `buildTags(finder, question)` | The three small tags above the title. |
| `buildTitle(finder, question, ids)` | Focusable `h3` with the question number. |
| `buildHint(finder, question, ids)` | The "راهنما: …" box. |
| `buildHeader(finder, question, ids)` | Tags + title + hint. |
| `buildOptionGroup(question, ids)` | The options container (two columns for 6+ options on wide screens). |
| `buildOptionControl(question, option, position, ids)` | One option: label, hidden input, check indicator, text. |
| `isSelected(question, answer, value)` | Whether an option is currently chosen. |
| `counterText(finder, question, answer)` | "n مورد انتخاب شده" or a hint to choose. |
| `syncSelection(finder, question, controls, counter)` | Makes inputs and `is-sel` classes match the state. |
| `listenToOption(finder, question, control, onChange)` | Wires pointer timing (for auto-advance), re-clicks, and changes to `finder`. |
| `buildErrorBox(ids)` | The hidden alert box. |
| `focusTitle(finder, title)` | Moves keyboard focus to the title and scrolls the dots into view. |
| `render(finder, moveFocus)` | **Public.** Builds and shows the current question. |
| `showError(finder, message)` | **Public.** Shows (and re-animates) the error and scrolls to it if hidden. |
| `clearError(finder)` | **Public.** Hides the error. |

## Relations

- Uses: [`format`](format.md), [`dom`](dom.md), [`state`](state.md), [`progress-view`](progress-view.md), [`scroll`](scroll.md). Used by: [`finder`](finder.md), which receives `selectOption`, `reselectOption` and `recordPointer` calls from here.
