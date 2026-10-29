# `data/interface-text.json`

**Purpose:** every word visitors see that is not a question or a career: header and buttons, question tags and errors, result headings, card labels, the 30-day plan templates, and all reasons, risks, compromises and conflict explanations. Written in a friendly, informal tone (D11). Keeping text here means wording can change without touching code (D12).

| Group | Used by |
|---|---|
| `app` | [`templates/shortcode-app.php`](../templates/shortcode-app.md) (via `Shortcode::app_text()`), [`progress-view.js`](../assets/js/progress-view.md), [`finder.js`](../assets/js/finder.md) |
| `question` | [`question-view.js`](../assets/js/question-view.md), [`finder.js`](../assets/js/finder.md) |
| `results` | [`results-view.js`](../assets/js/results-view.md), [`result-actions.js`](../assets/js/result-actions.md), [`finder.js`](../assets/js/finder.md) |
| `card` | [`role-card.js`](../assets/js/role-card.md), [`results-view.js`](../assets/js/results-view.md) |
| `separators` | Joining lists (`، `, ` / `, ` | `, ` + `) |
| `plan` | [`plan.js`](../assets/js/plan.md) |
| `messages` | [`assessment.js`](../assets/js/assessment.md) (keys passed from [`scoring.js`](../assets/js/scoring.md)) and [`conflicts.js`](../assets/js/conflicts.md) |

`{placeholders}` are filled by `Format.fillTemplate()`. Keep them when rewording.

## Relations

- Read by: [`BundledData::interface_text()`](../src/Data/BundledData.md). Every key used in the JavaScript is checked by `DatasetIntegrityTest::test_interface_text_keys_used_in_scripts_exist` and `test_message_keys_used_in_scripts_exist`.
