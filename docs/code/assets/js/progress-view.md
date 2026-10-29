# `assets/js/progress-view.js` → `ItcpFinder.ProgressView`

**Purpose:** everything in the tool's header and the row of question buttons ("dots"): section chip, total time, "question n of N", time left, progress bar, and the dots (answered / current / locked).

Time estimate (from the original): single choice 8 s, multi choice 13 s, multi choice with more than 8 options 16 s.

## Functions

| Function | What it does |
|---|---|
| `secondsFor(question)` | Estimated seconds for one question. |
| `secondsFrom(questions, startIndex)` | Seconds for the remaining questions. |
| `wholeMinutes(seconds)` | Rounded minutes, at least 1. |
| `timeLeftText(finder, startIndex)` | "حدود n دقیقه مونده" or "کمتر از ۱ دقیقه مونده" under 45 s. |
| `renderTimeChip(finder)` | "۱۹ سؤال · حدود ۳ دقیقه". |
| `updateProgressBar(finder, current, total, valueText)` | ARIA values of the progress bar. |
| `renderQuestionProgress(finder)` | Section chip, step text, time left and bar width for the current question. |
| `renderCompletedProgress(finder)` | Header texts and a full bar on the results screen. |
| `buildDot(finder, question, position)` | One question button, marked done/locked, which calls `finder.goToQuestion()`. |
| `markCurrentDot(finder, dot)` | Highlights the current dot and scrolls it to the centre (mobile). |
| `renderDots(finder)` | Rebuilds all dots. |
| `refreshDoneDots(finder)` | Updates only the "answered" marks after an answer changes. |

## Relations

- Uses: [`format`](format.md), [`dom`](dom.md), [`dataset`](dataset.md), [`state`](state.md), [`scroll`](scroll.md). Used by: [`finder`](finder.md) and [`question-view`](question-view.md) (`timeLeftText`).
