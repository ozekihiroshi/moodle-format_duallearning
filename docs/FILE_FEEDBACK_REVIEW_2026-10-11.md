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

## F1 correction and regression — 11 October 2026

Implemented in 907e6b9. For individual no-grade assignments, the overview now checks the current attempt's standard file-feedback plugin after the workflow-release guard. Files produce “Submitted; feedback returned” and “View this attempt’s feedback”. When comments and files coexist, the same generic action covers both. Comments-only and scored-assignment behaviour remain unchanged. Japanese strings are included.

The automated regression covers both Self paced and Teacher guided: absent submission/grade records without creating them on read, empty feedback, In marking and Ready for release, Released, disabled file feedback, comments-only fallback, mixed feedback, disabled comments, draft/reopened submissions, a new attempt with no response, a new response on that attempt, workflow disabled, scored-assignment guard, and teacher visibility. Rendered output links to the standard assignment page without filenames, file contents or pluginfile URLs.

In the same Moodle 5.2.4 lab, the actual student account first saw no result action when only the old response existed. A second response file was then seeded on attempt 1 (grade ID 11). In marking showed no action and no file on the standard page. After release, the new overview action opened the standard page displaying attempt-2-feedback.txt. Adding a current comment preserved the generic action, which opened both the current comment and file. These checks used the student browser; teacher upload/release operations were scripted fixtures. The Topics fixture was retained without a new response.

The core-history observation and initial disabled-comments fixture error remain recorded above; this correction does not address them. A complete upload/download workflow, mobile, assistive technology and third-party user observation remain outside this regression.

Validation passed: [Moodle Plugin CI 38128778057](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38128778057), commit 907e6b9. PHP 8.3/8.4 PHPUnit, coding/PHPDoc checks, JavaScript build/lint, learner browser regression, student-role cache regression and reproducible ZIP all passed. PHP 8.4 reported 35 tests and 340 assertions.
