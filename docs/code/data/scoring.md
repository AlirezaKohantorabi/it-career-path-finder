# `data/scoring.json`

**Purpose:** every tunable number of the matching: factor weights, the score range, the thresholds that split careers into result groups, and penalty points. How they are used: [`scoring.js`](../assets/js/scoring.md) and [`ranking.js`](../assets/js/ranking.md). Format: [DATA.md](../../DATA.md#scoringjson).

| Key | 1.0.0 value |
|---|---|
| `weights` | interest 25, work arrangement 12, client type 12, work mode 12, entry 10, income 8, stability 7, insurance 6, stress 3, responsibility 2, routine 3 |
| `meaning_interest_bonus` | 10 |
| `score_bounds` | 8 – 98 |
| `grouping` | recommended ≥ 58 (max 6, min 3), compromise ≥ 45 (max 4), 3 lowest shown |
| `penalties` | individual client no/poor fit 16/9, remote-only mismatch 22, urgent beginner very hard/hard 24/15, short-time beginner very hard 12, insurance unlikely 10, no on-call critical/other 18/10 |

`routine_match` replaced the original `routine_and_team_style_match` (D8).

## Relations

- Read by: [`BundledData::scoring()`](../src/Data/BundledData.md) → browser. Keys are checked against the code by [`DatasetIntegrityTest`](../tests/DatasetIntegrityTest.md).
