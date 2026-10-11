# Assignment, feedback and learning resumption — 11 October 2026

[日本語](ASSIGNMENT_FEEDBACK_REVIEW_2026-10-11.ja.md)

The individual assignment loop works. Separate teacher and student accounts completed submission, marking, feedback release, feedback viewing and a follow-up material in both Dual Learning and standard Topics. Two guidance gaps remain candidates for improvement; this walkthrough does not establish novice usability or learning effectiveness.

## Scope and method

The isolated local lab used Moodle 5.2.2 (Build 20260810), PHP 8.3.33 and Boost. The Dual Learning candidate was commit `61472de8fe5e0c18b59e506c7b2fefc8ea6a507d` on `codex/authoring-ux-regression-20261011`. This is the same plugin candidate as the previous regression, but not the exact Moodle patch version used by CI (5.2.4). LessonMark was absent and the optional Dual Learning theme was not active.

Two new courses used equivalent activities, settings and fictional enrolled users. Course 12 used Dual Learning / Self paced; course 13 used Topics. Original comparison courses 2–5 were untouched. Submission, grading and resumption were exercised through Chrome, with actual account changes rather than the student-role preview. A read-only Moodle API check corroborated workflow, grade and completion states.

Each course contained a manually completed preparation Page, an online-text Assignment, and a manually completed follow-up Page. The Assignment required explicit final submission, used marking workflow, and required both submission and receipt of a grade for automatic completion. Grade maximum was 100 and the passing grade was 50. Notifications were disabled for the fictional exercise; email and notification delivery were not tested.

The student submitted “The temperature increased by 3.” The teacher saved 75/100 and a comment asking for the unit, then directed the student to explain a second temperature change. This intentionally connected feedback to a subsequent learning action. The follow-up content was authored explicitly; the plugin did not generate that learning connection.

## Observations

| Stage | Dual Learning / Boost | Topics / Boost |
| --- | --- | --- |
| Initial work | Overview identified the preparation Page; assignment was “Not submitted” | Standard ordered activity list |
| Save draft | Assignment remained “Draft (not submitted)” and final submission was still required | Same standard draft/submission process |
| Confirm submission | Overview showed “Submitted; no published grade”; assignment showed “Submitted for grading” | Assignment showed “Submitted for grading” and “Not marked” |
| Save grade and comment as In marking | Student assignment showed “In marking”; neither score nor feedback table was shown. Overview retained “Submitted; no published grade” | Same absence of score and feedback before release |
| Release grade and comment | Overview changed to “Submitted; grade published”; assignment completion passed and next target became the follow-up Page | Standard completion showed Done; student could open the assignment from the activity list |
| Read feedback | Overview assignment link opened the standard feedback table with 75/100. “View full” exposed the complete comment | Same feedback table and expansion control |
| Resume learning | Course breadcrumb returned to the overview; next-target link opened the intended follow-up Page | Course breadcrumb returned to the activity list; ordinary activity link opened the same follow-up content |
| Complete follow-up | Manual-material count became 2/2; next-target link disappeared and the currently available tracked-activities-complete message appeared. Assignment/feedback link remained available | All three activity completion indicators showed Done |

In both courses, the workflow API reported `inmarking`, no released grade and incomplete Assignment before release. After release it reported `released`, grade 75 and `COMPLETION_COMPLETE_PASS`. The Dual Learning overview pointed to the follow-up Page before the student had opened feedback.

## Guidance findings and decisions

**F1 — Waiting for marking is not a new student action.** After final submission, “Next item to check” continued to point to that Assignment until its receive-a-grade condition was satisfied. This follows the documented first-incomplete rule, but provides no separate direction for a student waiting on the teacher. The Assignment itself remained accessible and its state was accurate. Treat this as a guidance candidate, not a failed submission or broken completion rule.

**F2 — Published feedback can be bypassed.** Releasing the grade completed the Assignment and moved the next-target link to the follow-up material before feedback was opened. The assignment-status area still linked to the feedback, but the primary next-target action did not direct attention to it. Grade publication, activity completion, viewing feedback and understanding feedback are different events. No unread/read indicator was present or inferred.

**F3 — Standard feedback may need expansion.** Both formats used Moodle's standard “View full” control for the complete comment. This is shared standard behaviour, not a course-format regression. Long comments need to be included in future mobile and keyboard checks.

Keep the current implementation for this verification round. Before changing the next-target algorithm, compare a small, explicit feedback action and waiting-state wording against the current presentation. Do not silently skip teacher-required work, invent an unread state, add a second completion system or equate an opened page with learning. Any proposed action must distinguish a published grade from the actual availability of comments or feedback files.

The design hypothesis is that clearer separation of “waiting for the teacher”, “feedback available” and “continue learning” will reduce uncertainty without adding tracking decisions. The walkthrough supports investigating this hypothesis; it does not prove it. A participant task should ask the learner to find the teacher's advice and apply it, without telling them which link to click. Record missed feedback, interpretation of completion, assistance needed and the chosen next action in both formats.

## Remaining coverage

- Reopened attempts and resubmission: ensure previous feedback is not presented as the new attempt's result.
- Ungraded comments-only feedback, hidden grades, feedback files, file submissions, individual extensions, groups and availability restrictions.
- Teacher guided mode, Japanese presentation, phone layouts and keyboard/screen-reader operation for this lifecycle.
- Notifications and arrival directly from a notification link.
- Actual first-time teachers and learners; no human participant observation or learning-outcome study took place.

These are separate follow-up checks, not claims that all assignment variants passed. No product code, release version, production environment or published package changed in this round. The prior full CI run applies to the unchanged candidate; this walkthrough is additional manual evidence, not a new CI run.

Local reproducibility: ignored lab files `assignment-cycle-fixture.php` and `assignment-cycle-state.php` under `build/dual-learning-audit-20261011/comparison` in the moodle-rescue workspace. The fixture intentionally refuses to recreate existing courses. Assignment course-module IDs were 14 and 17; follow-up Page IDs were 15 and 18.

## Follow-up: reopened attempt

A second attempt exposed a confirmed summary defect. Both assignments were reopened through the standard teacher grader with Allow another attempt = Yes, leaving the first attempt's released 75/100 and comment intact. As the student, a new response was created, saved and finally submitted: “Attempt 2: The temperature increased by 3 C, from 18 C to 21 C.” No teacher graded this second response.

Immediately after reopening, Dual Learning correctly showed Resubmission required and restored the Assignment as the next target. After the second submission, however, it showed Submitted; grade published, no next target, and all available tracked activities complete.

Read-only API evidence in both courses: current attemptnumber = 1 (the UI's attempt 2), current attempt grade = -1 (ungraded), retained gradebook grade = 75, workflow = released, completion = 2 after resubmission. The standard Assignment screen also retained Released and a top-level 75-point Feedback table. The old comment appeared under Previous attempts / Attempt 1, rather than as a comment on attempt 2. These underlying Moodle behaviours must be distinguished from the format's summary defect.

The source confirms the gap in classes/local/overview.php::has_published_grade(): it checks submission status, visible gradebook grade and workflow release, but does not check whether the current attempt has a grade. The comments-only path already looks up the specific attempt. A regression should preserve the old released grade, resubmit an ungraded new attempt, and require that the overview does not label it as newly graded. It should then check an actual second-attempt grade before and after release. Include workflow-disabled and manually overridden gradebook cases before choosing final wording.

Do not rewrite Moodle completion records to fix this summary. The completion message is based on Moodle's retained grade and should not be represented as evidence of second-attempt assessment. The UI needs to distinguish standard completion from the current attempt's assessment status. No product fix was made during this verification; both courses remain at the submitted, ungraded second attempt as reproducible fixtures. This completes the requested reopened-attempt check, not the remaining assignment variants or second-attempt grading/release regression.

## Correction and second-attempt release regression

The overview now requires a non-negative grade record for the current submission attempt before reporting a published grade, in addition to gradebook visibility and workflow release. English and Japanese labels explicitly refer to this attempt. Zero is a valid grade; missing records and -1 are not. Gradebook overrides alone do not establish that the current attempt was assessed. Moodle completion records and the next-target algorithm remain unchanged.

The browser regression in course 12 passed: with the old 75 retained and attempt 2 ungraded, the overview said no published grade for this attempt. The teacher then saved 90 and an identifiable Attempt 2 feedback comment as In marking. The student could see neither the new score nor new comment, while the first attempt's feedback remained in Previous attempts. After release, the overview reported a published grade for this attempt, the current Feedback table showed 90 and the new comment, and Previous attempts still showed 75 and the original comment. Course 13 remains the ungraded second-attempt baseline.

The automated regression covers retained grades, absent and ungraded current-attempt records, read-only lookup, workflow enabled/disabled, a gradebook override, zero grades, release and hidden grade items. An initial CI run passed PHPUnit but caught only a multiline-call formatting error in the added test; the formatting was corrected for the subsequent run. Product release and main remain unchanged. Standard completion may still be satisfied by a retained prior grade immediately after resubmission; this is explicitly outside this bounded assessment-label fix and remains a guidance issue.
