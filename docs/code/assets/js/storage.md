# `assets/js/storage.js` → `ItcpFinder.Storage`

**Purpose:** keeps a tool's state in `sessionStorage` (this browser tab only, never sent anywhere) so a refresh does not lose the answers. Restored data is **never trusted**: every answer is checked against the current questions, so stale or tampered data cannot break the tool or inject values (L10).

Key: `itcp_state_v3_<instance number>` (D20). `v3` changed from the original `v2` because the questions changed; old saved answers are simply ignored.

## Functions

| Function | What it does |
|---|---|
| `keyFor(instanceNumber)` | The storage key of one tool instance. |
| `snapshotOf(state)` | The plain object that is saved. |
| `save(key, state)` | Saves; silently does nothing when storage is blocked (private mode, full quota). |
| `clear(key)` | Removes the saved state. |
| `readSnapshot(key)` | Parsed saved object if it has an `answers` object, else `null`. |
| `allowedValues(question)` | Null-prototype map of the question's option values. |
| `validAnswer(question, value)` | The value if it is a real option (a list of real options for multi-select, de-duplicated), otherwise `undefined`. |
| `restoredAnswers(questions, storedAnswers)` | Only valid answers for questions that still exist. |
| `wholeNumber(value)` | Integer or 0. |
| `restore(key, questions)` | Rebuilds a state: valid answers, positions clamped so the visitor cannot jump past the first unanswered question, results reopened only when every required question is answered. `null` when nothing is saved. |

## Relations

- Uses: [`format`](format.md), [`state`](state.md). Used by: [`finder`](finder.md).
