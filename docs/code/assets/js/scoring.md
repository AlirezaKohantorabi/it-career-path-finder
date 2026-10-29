# `assets/js/scoring.js` → `ItcpFinder.Scoring`

**Purpose:** scores **one career** against the visitor's answers, from 8 to 98, and records the reasons, risks, compromises and penalties through an [`Assessment`](assessment.md). Ported faithfully from the original tool (L5); only the agile factor was removed (D8).

## The formula

```
score = round( clamp( Σ factor × weight  [+ meaning bonus]  − penalties ,  8 , 98 ) )
```

- Each **factor** is 0–1. Weights come from `scoring.json → weights` (sum 100).
- **Meaning bonus:** if the goal is "loving my job", `meaning_interest_bonus × (interest − 0.5)`.
- **Penalties** (`scoring.json → penalties`) are hard conflicts. Any penalty keeps the career out of the "recommended" group ([`ranking.js`](ranking.md)).

| Factor (weight) | Main inputs | How |
|---|---|---|
| `interest_match` (25) | `interests`, `customer_contact`, role `interest_tags` | No shared tag → 0.18. Otherwise 0.45 + 0.55 × (shared / known tags). ±0.12 / +0.08 for people contact vs "communication"/"business" tags. |
| `work_arrangement_match` (12) | `work_arrangement`, role `work_arrangement_fit` | Best fit among chosen arrangements (internship = average of employee fit and inverted entry difficulty). +0.1 for the "freedom" goal on freelance-friendly roles. |
| `client_type_match` (12) | `client_type`, `industry_preferences`, role `client_type_fit`, `typical_workplaces` | Fit for the chosen client (or the best of three with no preference). ±0.12/0.1 if a workplace matches the chosen industries (with aliases). Penalty for individual clients on organizational roles. |
| `work_mode_match` (12) | `work_mode`, `location_scope`, `primary_goal`, role `work_mode_fit` | Best fit among chosen modes. Penalty when the visitor needs remote-only and the role's remote fit is below 3. |
| `entry_accessibility_match` (10) | `current_level`, `time_to_entry`, `primary_goal`, `must_have_portfolio`, `english_level`, role `entry_difficulty` | Beginners in a hurry get inverted difficulty (+ penalties for hard roles); others get fixed values by difficulty; reduced for "no portfolio" in software/design and for basic English on hard roles. |
| `income_match` (8) | `income_target`, `primary_goal`, role `income_ceiling` | Ambitious → ceiling; above market → 0.95/0.65/0.3; otherwise 0.85. Averaged with the ceiling for the "career growth" goal. |
| `stability_match` (7) | `security_priority`, `primary_goal`, role `job_stability` | Only matters when stability is important; otherwise 0.75. |
| `insurance_match` (6) | `insurance_requirement`, role `insurance_likelihood_in_employee_roles` | Mandatory → likelihood (penalty below 3); preferred → 0.4 + 0.6 × likelihood; otherwise 0.8. |
| `stress_match` (3) | `stress_tolerance`, role `stress_level` | 1 within tolerance, −0.3 per level above. |
| `responsibility_match` (2) | `responsibility_preference`, role `responsibility_level` | 1 − 0.25 × distance (min 0.2). |
| `routine_match` (3) | `routine_preference`, role `routine_level` | 1 − 0.22 × distance (min 0.2). |

The **on-call check** (no factor, only a penalty) applies when the visitor refuses on-call work and the role has stress ≥ 4 or the `high_stress_or_urgency_possible` label: 18 points for infrastructure/security roles, 10 for others.

**Strict insurance filter:** when the visitor turned it on and insurance is mandatory, roles with likelihood below 3 are marked `excluded` and removed by ranking.

## Functions

| Function | What it does |
|---|---|
| `lookup(map, key)` | Own-property lookup in the constant preference maps. |
| `ratingsOf(role)` | The role's `ratings_1_to_5` or `{}`. |
| `fitGroupOf(role, group)` | One fit group of the ratings or `{}`. |
| `contains(list, value)` | Safe `indexOf` check on maybe-missing lists. |
| `invertedDifficulty(difficulty)` | Normalized `6 − difficulty` (easy roles score high). |
| `interestBaseValue(knownCount, matchedCount)` | Interest value before the contact adjustment. |
| `adjustForCustomerContact(value, roleTags, contact)` | Contact preference adjustment. |
| `interestFactor(role, answers, assessment)` | The interest factor + its reason or risk. |
| `interestLabels(tags, context)` | Up to 3 interest labels joined with the comma separator. |
| `arrangementFitFor(arrangement, role)` | Fit of one work arrangement (special rule for internship). |
| `bestArrangementFit(chosen, role)` | Best fit among the chosen arrangements. |
| `arrangementFactor(role, answers, assessment)` | The arrangement factor + reason or compromise/risk. |
| `clientFitFor(client, role)` | Fit for one client type, or the best of three with no preference. |
| `expandIndustries(chosen, aliases)` | Chosen industries plus their aliases as a lookup. |
| `adjustForIndustries(value, role, answers, context)` | Bonus/penalty for matching chosen industries. |
| `clientFactor(role, answers, assessment)` | The client factor + reason, or penalty/compromise/risk. |
| `isFieldRole(fit)` | On-site 5 and remote ≤ 2: a field job. |
| `modeFitFor(mode, fit)` | Fit of one work mode. |
| `bestModeFit(modes, fit)` | Best fit among chosen modes. |
| `requiresRemoteOnly(modes, answers)` | Only remote chosen, or "anywhere in Iran if remote", or the remote goal. |
| `wantsRemote(modes, answers)` | Remote is among the wishes. |
| `workModeFactor(role, answers, assessment)` | The work-mode factor + remote penalty or reason. |
| `isNovice(answers)` | Beginner, learner or career switcher. |
| `urgentNoviceEntry(difficulty, assessment)` | Beginner with < 3 months: penalties for difficulty 4–5, reason otherwise. |
| `shortNoviceEntry(difficulty, assessment)` | Beginner with 3–6 months: penalty for difficulty 5. |
| `relaxedEntry(difficulty, answers)` | Entry value for everyone else. |
| `entryBaseValue(difficulty, answers, assessment)` | Chooses one of the three above. |
| `blendWithFastEntryGoal(value, difficulty, answers)` | Averages with inverted difficulty for the "fast entry" goal. |
| `adjustForPortfolio(value, role, answers, assessment)` | −0.15 and a compromise for "no portfolio" in software/design. |
| `adjustForEnglish(value, difficulty, answers, assessment)` | −0.08 and a gap for basic English on hard roles. |
| `entryFactor(role, answers, assessment)` | The entry factor. |
| `ambitiousIncome(ceiling, assessment)` | Income value for very high targets, with reason or risk. |
| `aboveMarketIncome(ceiling)` | Income value for "above market". |
| `incomeBaseValue(ceiling, answers, assessment)` | Chooses the income rule. |
| `incomeFactor(role, answers, assessment)` | The income factor. |
| `caresAboutStability(answers)` | Stability is high/critical or the goal. |
| `stabilityFactor(role, answers, assessment)` | The stability factor + compromise/risk for unstable roles. |
| `insuranceLikelihood(role)` | The role's insurance likelihood (3 if missing). |
| `mandatoryInsurance(likelihood, assessment)` | Insurance value when mandatory, with penalty or reason. |
| `insuranceFactor(role, answers, assessment)` | The insurance factor. |
| `stressLevel(role)` | The role's stress level (3 if missing). |
| `mayNeedUrgentResponse(role)` | Stress ≥ 4 or the urgency label. |
| `recordOnCallConflict(role, answers, assessment)` | The on-call penalty, compromise and risk. |
| `stressFactor(role, answers)` | The stress factor. |
| `distanceFactor(level, preferred, step)` | `max(0.2, 1 − step × |level − preferred|)`. |
| `responsibilityFactor(role, answers)` | The responsibility factor. |
| `routineFactor(role, answers)` | The routine factor. |
| `recordLocalMarketGap(role, answers, assessment)` | Gap message for "own city only" + on-site + small market. |
| `addFallbackReasons(role, answers, routineValue, assessment)` | Fills reasons up to 3: market demand, routine fit, balance. |
| `weightedTotal(factors, weights)` | Σ factor × weight (missing weight = 0). |
| `finalScore(factors, answers, penalty, scoring)` | Applies bonus, penalty and bounds; rounds. |
| `isStrictlyExcluded(role, answers, context)` | The strict insurance filter rule. |
| `scoreRole(role, answers, context)` | **Public.** Runs every factor in the original order (this order decides which 3 reasons and 2 risks are shown) and returns the result. |

## Relations

- Uses: [`format`](format.md), [`assessment`](assessment.md), [`dataset`](dataset.md) (labels). Used by: [`ranking`](ranking.md).
- Settings: [`scoring.json`](../../data/scoring.md). Messages: [`interface-text.json`](../../data/interface-text.md) `messages`.
- Changing anything here? Run the parity check in [TESTING.md](../../../TESTING.md#4-parity-check-after-changing-scoring-or-ranking). The factor names must match `scoring.json → weights` (`DatasetIntegrityTest`).
