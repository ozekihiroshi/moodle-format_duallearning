# File feedback review — 11 October 2026

[日本語](FILE_FEEDBACK_REVIEW_2026-10-11.ja.md)

File feedback needs its own return path. The standard assignment page displayed a released response file, but the Dual Learning overview stayed at Submitted without a result-review action. This developer-run comparison identifies a product guidance gap; it does not establish novice usability or learning effectiveness.

## Scope and method

The isolated localhost:8098 lab ran Moodle 5.2.4 (Build 20261005), PHP 8.3.33 and Boost. Product code was a1abd21, with documentation head 81f4157. Course 22 used Dual Learning / Self paced; course 23 used Topics. Assignment modules were 63 and 66. Existing fixtures were preserved.

Both assignments used online-text submissions, grade type None, marking workflow, two attempts, and submission-only automatic completion. File feedback was enabled. The comments plugin was enabled but its content was empty: this tests file-only feedback content, not a configuration with that plugin disabled. Fictional student comparisonstudent viewed both courses in Chrome.

Fixture scripts created submissions, grade records, workflow states and a response file through Moodle APIs/file storage plus database setup. This was not a test of student submission forms or teacher upload/release forms. First-attempt feedback was attempt-1-feedback.txt, containing “Attempt 1 feedback: Include the unit C.” After release, a second submitted attempt was seeded with no new feedback.

## Observations

| State | Dual Learning / Boost | Topics / Boost |
| --- | --- | --- |
| First attempt, In marking | Standard assignment page did not display the response file | Same |
| Released | Standard assignment page displayed the file link; overview remained Submitted with no result-review action | Standard page displayed the file link |
| Second attempt, no new feedback | No current feedback; previous-attempt history showed submitted text but omitted the old response file | Same |

Read-only standard file-plugin inspection confirmed the old file remained associated with attempt 0 in both courses (grade IDs 9 and 10). Attempt 1 (grade IDs 11 and 12) was empty. No loss of the stored file was observed. Download contents through the browser were not tested.

## Findings and next correction

**F1 — The overview misses file-only returned feedback.** Its no-grade return check currently detects comments only. Extend it to recognise enabled, visible file feedback on the current attempt, using the standard plugin's is_empty check and the existing workflow-release guard. Offer a generic “View this attempt’s feedback” action when files are present, including when comments and files coexist. Link to the standard assignment page; do not copy filenames, contents or direct download URLs into the overview.

The regression must cover unreleased and released files, empty or disabled feedback plugins, mixed comments/files, and resubmission where only the previous attempt has a file. Preserve the existing graded-assignment rules and verify both learning modes. This correction has not been implemented in this audit.

**F2 — Historical no-grade feedback is omitted by the standard view.** Both formats reproduce the existing no-grade history limitation on Moodle 5.2.4. Keep this as a core observation alongside COMMENTS_ONLY_REVIEW_2026-10-11.md. Do not modify Moodle core or introduce a format-owned history view.

**F3 — A disabled-comments configuration needs separate investigation.** The initial grade-None fixture with file feedback enabled and comments disabled produced “Cannot load the grade item” on the standard assignment page. Enabling empty comments and updating the grade item restored the page. The inspected grade-item logic creates a text item when a gradebook feedback plugin is enabled. This was a scripted fixture, so the finding does not establish what the complete teacher UI would create. Record it without treating it as a format defect or publishing an upstream report.

## Limits

No product source changed in this audit, so the existing passing a1abd21 CI run remains the product validation reference (GitHub Actions 38127430802). File uploads/download bytes, teacher form workflow, Teacher guided end-to-end behaviour, mobile and assistive technology remain untested here. The next step is the bounded F1 correction and its regressions, not a claim that the overall audit is complete.
