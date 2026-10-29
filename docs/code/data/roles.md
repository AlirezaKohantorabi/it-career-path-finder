# `data/roles.json`

**Purpose:** the 87 bundled IT careers with their ratings, interests, workplaces, skills and the new plain-language explanation. Admin edits are stored separately and laid over this list (D6); this file is never written by the plugin. Format and field rules: [DATA.md](../../DATA.md#rolesjson); Persian how-to: [DATA.fa.md](../../fa/DATA.fa.md).

## Categories in 1.0.0

Software development 15, QA and testing 3, data and AI 8, infrastructure and operations 15, cybersecurity 7, design/product/business analysis 7, enterprise applications 5, sales and customer success 4, digital marketing and content 7, education and communication 5, hardware and IoT 6, specialized IT 5.

## History

- 1.0.0: migrated from the original tool's JSON (`schema_version` 1.0.0 → 2.0.0). Added `plain_description` for all 87 careers (D9); removed the template `summary` (L3, D10) and the `agile_team_fit` rating (D8). Ratings, tags, workplaces, skills and entry routes are unchanged.

## Relations

- Read by: [`BundledData::roles()`](../src/Data/BundledData.md) → [`RoleRepository`](../src/Data/RoleRepository.md). Exported files from the admin have the same format.
- Checked by: [`DatasetIntegrityTest`](../tests/DatasetIntegrityTest.md) and [`RoleSanitizerTest`](../tests/RoleSanitizerTest.md) (every career must survive sanitizing unchanged).
