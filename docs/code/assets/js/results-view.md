# `assets/js/results-view.js` → `ItcpFinder.ResultsView`

**Purpose:** renders the whole results screen into `data-itcp="results"`, in this order:

1. **Summary:** title, "compared all N careers with your M answers", general note, chips of key answers (`summary_questions`), and one jump button per recommended career (clicking scrolls to its card, where the plain explanation comes first).
2. **Conflicts** box (only if there are any), plus the "we promoted careers" note.
3. **Tweaks:** one-click buttons to try a different condition (more learning time, all client types, occasional on-call, strict insurance filter) and "edit my answers".
4. Card sections: recommended, compromise, not recommended.
5. Full ranking of every career (collapsible, not printed).
6. Action bar.

## Functions

| Function | What it does |
|---|---|
| `buildSummaryChips(finder)` | Chips with the labels of the summary answers. |
| `buildJumpButton(finder, result, position)` | A button that calls `finder.revealRoleCard()`. |
| `buildSummary(finder, ranking)` | Section 1. |
| `conflictMessages(finder, ranking)` | Conflict messages + the promoted note. |
| `buildConflicts(finder, messages)` | Section 2. |
| `buildTweakButton(finder, label, iconName, change, pressed)` | One tweak button; calls `finder.applyTweak(change)`. |
| `availableTweaks(finder)` | Which tweaks make sense for the current answers. |
| `buildTweaks(finder)` | Section 3. |
| `buildSectionHeading(modifier, title, intro)` | Heading of a card section. |
| `appendCardGroup(finder, container, results, kind, numbered)` | Cards of one group. |
| `appendCardSections(finder, container, ranking)` | Section 4. |
| `buildRankRow(finder, result, position, ranking)` | One row of the full ranking, coloured by group. |
| `buildFullRanking(finder, ranking)` | Section 5. |
| `focusResultsTitle(finder)` | Focuses the summary title and scrolls the tool into view. |
| `render(finder, moveFocus)` | **Public.** Ranks and renders everything. |

## Relations

- Uses: [`format`](format.md), [`dom`](dom.md), [`dataset`](dataset.md), [`ranking`](ranking.md), [`conflicts`](conflicts.md), [`role-card`](role-card.md), [`result-actions`](result-actions.md), [`scroll`](scroll.md). Used by: [`finder`](finder.md).
