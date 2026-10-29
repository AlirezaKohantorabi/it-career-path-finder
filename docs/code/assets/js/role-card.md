# `assets/js/role-card.js` → `ItcpFinder.RoleCard`

**Purpose:** builds one career card. Three kinds: `primary` (blue), `compromise` (amber), `low` (grey).

Card layout, top to bottom:

1. Head: number + Persian title, category chip, English title, score ring.
2. **"این شغل به زبون ساده چیه؟"** — the career's `plain_description`, always first (D9).
3. *Low cards stop here* with "why this isn't a priority now" (blockers + skills to learn).
4. Compromise box (compromise cards, or primary cards that carry a penalty).
5. Reasons (green) and risks (amber), side by side on wide screens.
6. A collapsible "skills, workplaces and 30-day plan" section (open on the first recommended card): skills + entry route; workplaces + four 1–5 meters; the plan and experiment.
7. Market caveat line.

## Functions

| Function | What it does |
|---|---|
| `cardElementId(finder, role)` | `itcp-<instance>-role-<id>`; used to scroll to a card. |
| `buildBox(modifier, iconName, title)` | A titled box used by most sections. |
| `buildTitleBlock(finder, role, number)` | Title, category and English title. |
| `buildScoreRing(finder, score, kind)` | The circular score (CSS conic gradient driven by `--v`). |
| `buildHead(finder, result, kind, number)` | Title block + ring. |
| `buildPlainDescription(finder, role)` | Section 2. |
| `buildLowMatchExplanation(finder, result)` | Section 3. |
| `buildCompromiseBox(finder, result)` | Section 4. |
| `needsCompromiseBox(result, kind)` | When section 4 is shown. |
| `buildReasonsAndRisks(finder, result)` | Section 5. |
| `buildChips(values)` | A row of small chips. |
| `buildSkillsBox(finder, role)` | Skills and entry route. |
| `buildMeter(finder, label, value)` | One 5-step meter with an accessible label. |
| `buildMeters(finder, role)` | Entry difficulty, income ceiling, stability, insurance meters. |
| `buildWorkplacesBox(finder, role)` | Workplaces + meters + note. |
| `buildPlanBox(finder, role)` | Plan from [`plan.js`](plan.md). |
| `buildDetails(finder, role, openDetails)` | Section 6. |
| `caveatText(finder, role)` | Section 7 text. |
| `buildCard(finder, result, kind, number, openDetails)` | **Public.** The whole card. |

## Relations

- Uses: [`format`](format.md), [`dom`](dom.md), [`plan`](plan.md). Used by: [`results-view`](results-view.md), and `cardElementId` by [`finder`](finder.md) (`revealRoleCard`).
- Styled by `.itcp-role`, `.itcp-box.*` in [`app.css`](../css/app.md).
