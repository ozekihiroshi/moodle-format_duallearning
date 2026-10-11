# Comments-only assignment review — 11 October 2026

[日本語](COMMENTS_ONLY_REVIEW_2026-10-11.ja.md)

Comments-only feedback respects release and the current attempt. A separate guidance problem and a standard Moodle history limitation were found. This is a developer-run browser comparison, not an observation of novice users or evidence of learning effectiveness.

## Environment and scenario

The isolated lab used Moodle 5.2.2 (Build 20260810), PHP 8.3.33, Boost and the Dual Learning candidate at 772f30b. The implementation matches the previous successful CI candidate, but the lab's Moodle patch version differs from CI's 5.2.4. Do not generalise the core-history finding to later patches before reproducing it there.

New course 14 used Dual Learning / Self paced; course 15 used Topics. Both had separate fictional comparisonteacher/comparisonstudent accounts, equivalent preparation and follow-up Pages, and an online-text Assignment. Grade type was None (grade = 0), marking workflow was enabled, two attempts were allowed, and automatic completion required submission only. Assignment module IDs were 20 and 23. Existing fixtures were preserved.

The student submitted “The temperature increased by 3.” The teacher saved the following feedback as In marking, then released it: “Comments-only feedback 1: Include the unit C. Revise your explanation, then compare 21 C and 19 C.” The teacher subsequently allowed another attempt. The student submitted “Comments-only attempt 2: The temperature increased by 3 C.” No feedback was entered for attempt 2.

## Results

| Check | Dual Learning / Boost | Topics / Boost |
| --- | --- | --- |
| First submission | Assignment complete under the configured submission-only rule; next target moved to the follow-up Page | Same standard completion rule |
| Saved, not released | Student saw In marking and no feedback table | Same |
| Released | Overview showed Submitted; comments returned. Its assignment link opened the comment with no score | Same comment in the standard Feedback table, with no score |
| Attempt reopened | Overview showed Resubmission required and restored the Assignment as the next target | Reopened attempt 2 shown |
| Second submission without new comment | Overview did not claim comments returned; next target moved to follow-up under standard completion | Submitted attempt 2 shown |
| Old feedback after reopening/resubmission | Previous attempts showed attempt 1's submitted text but no feedback comment | Same missing feedback comment in history |

The old comment was retained in storage. Read-only API/database inspection found attempt 0's grade record with grade -1 and the original comment; attempt 1 had grade -1 and no comment in both courses. This is a history-display limitation, not observed data loss or the previous comment being presented as current feedback.

## Findings and decisions

**C1 — A no-grade assignment should not imply a pending score.** Before comment release and after resubmission, the overview used “Submitted; no published grade for this attempt”. That is technically consistent with no published grade, but suggests a score might arrive even though grading is disabled. A bounded future correction should use a neutral submitted label when grade type is None and no current returned comment exists. Do not promise feedback will be sent, because it may never be entered. Cover comments enabled/disabled and both languages in its regression.

**C2 — Historical comments become inaccessible through the tested standard view.** In this Moodle 5.2.2 installation, mod/assign/classes/output/renderer.php::render_assign_attempt_history only selects a historical grade record when its grade is not ASSIGN_GRADE_NOT_SET (around line 930). Feedback-plugin rendering is inside the subsequent if ($grade) block (around lines 976–1033). The stored comments-only record has -1, so history omits its feedback. Both formats reproduce this behaviour. Reproduce on the supported current Moodle patch before proposing a core report or compatibility response. Do not change core files or copy private comments into an unguarded format-owned history view.

**C3 — Completion does not wait for feedback.** Under the intentional submission-only completion rule, the next target points to the follow-up Page before feedback arrives. That Page asks the learner to read feedback first. This reinforces the earlier design concern: course-order completion alone does not express pedagogical readiness. Consider waiting-state guidance and authoring guidance without inventing read receipts or replacing Moodle completion rules.

No product code, release or main-branch change was made in this round. No new CI run was necessary for these documentation-only findings. The previous candidate CI remains successful: https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38121264323 . Course 14 and 15 remain at submitted attempt 2 with no new feedback, retaining the original comment in storage. The local ignored fixture and read-only inspection scripts are comments-cycle-fixture.php and comments-cycle-state.php under the moodle-rescue build/dual-learning-audit-20261011/comparison directory.

This round does not cover second-attempt comment release, workflow-disabled browser behaviour, feedback files, groups, extensions, mobile/keyboard operation or participant observation. The most immediate next step is aligning the Moodle patch version and checking C2 again, alongside the small no-grade wording correction C1.

## Patch alignment and wording correction

The isolated lab was upgraded successfully to Moodle 5.2.4 (Build 20261005), code/database version 2026042004, retaining PHP 8.3.33 and the existing course/submission records. This matches the CI release/build string. CI follows MOODLE_502_STABLE; an identical core git revision is not asserted. The official archive SHA-256 is 8569b63f1e97416ecb67ec75767dd20675892c7a62a5e90d1647366fddf873cf. Database and moodledata were backed up before the upgrade; the separate 8083 site was untouched. The lab README records its patch-specific Compose override and restoration requirements.

C2 reproduces on 5.2.4. As the enrolled student, both Assignment 20 (Dual Learning) and Assignment 23 (Topics) showed the second submission and the first submission's text under Previous attempts, but no original feedback comment. A read-only check confirmed that the original comment still belongs to attempt 0 and attempt 1 has no comment. The standard renderer retains the ASSIGN_GRADE_NOT_SET exclusion at line 930 and feedback rendering behind if ($grade). Its SHA-256 is ba681f018b7a7a9636ae3692c14c880bceff43313453f06f1265a50f857d3c75. No core modification or external issue report was made.

C1 is corrected in plugin commit c0a8b91. An ungraded submitted assignment without a returned current-attempt comment now says Submitted (Japanese: 提出済み). Draft, reopened and returned-comment states retain their own labels; graded assignments retain their attempt-specific grading labels. The existing comments-only regression now asserts this distinction, including hidden/disabled comments, release and resubmission, and an additional draft-state assertion. The English student overview was checked on 5.2.4. Japanese source text was reviewed; this lab has no installed Japanese core language pack, so Japanese rendering was not claimed (a language-manager probe fell back to English).

Code CI: https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38123627322 . The unresolved item is standard history access to old comments-only feedback, not loss of data or current-attempt status confusion. Keep it separate from this wording correction and from broader novice/learning-outcome evaluation.

CI completed successfully for c0a8b91: all jobs passed, including PHP 8.3/8.4, 32 PHPUnit tests / 226 assertions, browser regressions and reproducible ZIP checks. The later documentation-only commit does not change the tested code.
