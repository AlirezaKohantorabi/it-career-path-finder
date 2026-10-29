# `assets/js/assessment.js` → `ItcpFinder.Assessment`

**Purpose:** while one career is being scored, the factor functions in [`scoring.js`](scoring.md) record *why* through an `Assessment`: reasons it fits, risks, skill gaps, compromises the visitor would need to make, and penalty points. At the end it produces the result object used by ranking and the cards.

Messages are looked up by key in `interface-text.json → messages` and filled with `{placeholders}`.

## Functions

| Function | What it does |
|---|---|
| `Assessment(context)` | Constructor; `context` is `Finder.scoringContext()` (`index`, `text`, `scoring`, `strictInsuranceFilter`). |
| `message(key, values)` | Filled message text for a key. |
| `addReason(key, values)` | A reason this career fits. |
| `addRisk(key, values)` | A risk or warning. |
| `addGap(key, values)` | A skill/market gap; shown after the risks. |
| `addCompromise(key, values)` | Something the visitor would have to accept. |
| `addPenalty(key)` | Adds the points of `scoring.json → penalties[key]`. |
| `reasonCount()` | How many reasons so far (fallback reasons fill up to 3). |
| `finalRisks()` | Risks then gaps, de-duplicated; the default risk if empty. |
| `toResult(role, score, excluded)` | `{ role, score, reasons (max 3), risks (max 2), compromises, penalty, excluded }`. |

## Relations

- Uses: [`format`](format.md). Created by: [`scoring.js`](scoring.md) (`scoreRole`). Results consumed by: [`ranking`](ranking.md), [`role-card`](role-card.md).
- Every key it is called with must exist in [`interface-text.json`](../../data/interface-text.md) and [`scoring.json`](../../data/scoring.md); `DatasetIntegrityTest` checks this.
