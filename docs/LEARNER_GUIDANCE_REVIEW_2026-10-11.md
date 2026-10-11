# Learner guidance review

English | [日本語](LEARNER_GUIDANCE_REVIEW_2026-10-11.ja.md)

2026-10-11. Local integration checks of the candidate, using Moodle state and the rendered overview template. This is not a student usability study or a browser walkthrough of all states.

## Results

**Fourteen state checks passed.** The same seven states were checked separately in Self paced and Teacher guided mode. The comparison fixture used a single Page and an independently enrolled student, on the existing Moodle 5.2.2 laboratory at port 8098. No production records were used.

| State | Page accessible to student | Overview | Next target | Completion message |
| --- | --- | --- | --- | --- |
| Hidden draft | No | Absent | None | No |
| Visible, no activity tracking | Yes | Absent | None | No |
| Visible, manually tracked and incomplete | Yes | Present | Page | No |
| All tracked work complete | Yes | Present | None | Yes |
| Marked incomplete again | Yes | Present | Page | No |
| Restricted to a future date | No | Absent | None | No |
| Course completion tracking disabled | Yes | Absent | None | No |

These outcomes were identical in both modes; the heading changed appropriately. Completion did not remove access to the Page. Empty overview suppression retained the normal accessible activity state. The full standard course-list rendering was not exercised by this script.

## What the result means

**The next target is completion-based.** Source inspection confirms that the implementation selects the first accessible incomplete tracked activity in course order. The single-Page state check confirms removal and restoration of that target; it does not independently test ordering across multiple activities. There is no teacher-selected current lesson in this calculation. Teacher guided changes the heading and includes forum shortcuts, but those shortcuts were outside this Page-only test.

**Untracked materials need another orientation cue.** A visible Page with tracking off has no next-target overview. This avoids inventing progress or reporting completion when no tracked work exists. The ordinary course structure and teacher-authored introduction must provide orientation in that case. Whether that is sufficient for learners remains untested.

**Completion language remains conservative, but can be more relevant.** The message says tracked activities are complete, rather than claiming that learning or the entire course is complete. However, it then says to check submissions and feedback even in this Page-only course, which has neither an assignment nor submission feedback. This is an observed relevance issue, not a data or permission failure.

## Consolidated improvement decisions

| Item | Status | Next decision |
| --- | --- | --- |
| Empty overview heading | Candidate implemented; previous rendering and browser checks passed | Retain in release candidate |
| Continue offered before any unit exists | Candidate implemented; actual creation transition checked | Retain in release candidate |
| Unit text called by different labels | Observed; novice confusion unmeasured | Participant task before changing structure; consider consistent wording |
| Teacher-guided heading “Current work and submissions” | Actual target is completion-based, not teacher pace | Consider more precise wording; do not add a pacing feature merely to justify the heading |
| Submissions/feedback advice in a Page-only completed course | Irrelevant sentence reproduced | Consider conditional or neutral completion wording, preserving the limited meaning of tracked completion |
| No next target without tracking | Expected implementation behaviour | Test whether the ordinary outline and introduction suffice before adding a new recommendation rule |

No additional product code was changed in this review. Bundle confirmed, bounded improvements after regression checks; do not turn unobserved participant difficulties into claims of defects or learning benefits.

## Evidence and boundaries

The ignored local laboratory contains `check-learner-guidance.php`, `learner-path.json`, `learner-guided.json` and `after-learner-guidance.json`. Each mode ran in a separate PHP process. Temporary mode, visibility, completion and availability changes were rolled back, and shared caches were purged. The four original comparison courses' saved records and access results match `after.json` afterward.

These checks do not cover multiple-activity ordering, assignments, forums, groups, failed automatic completion, teacher-paced classroom use, mobile layout or learner comprehension. Existing repository tests may cover some of these, but were not executed here. Full PHPUnit/Behat and remote CI remain pending. Actual first-time teachers and learners have not yet been observed.

## Multiple targets and live updates

A subsequent isolated integration check used three Pages (A, B, C) in each mode, in new courses 9 and 10. Sixteen checks passed: initial A; complete A → B; move C before B → C; hide C → B; restrict B to a future date → no next target and currently accessible tracked work complete; restore B/C → C; complete C → B; complete B → no next target. Each returned target was checked against student-accessible course module information. Evidence: local `check-multiple-targets.php` and `multiple-target-results.json`. No earlier comparison course was changed. These fixtures remain for inspection.

**Completion wording has been corrected in the candidate.** Hidden or restricted incomplete work is excluded from the overview. The English text now says “Currently available tracked activities are complete. You can revisit the materials.” Japanese was updated correspondingly. This limits the claim to available tracked work and removes irrelevant submission advice in Page-only courses. PHP lint passed for both language files, and Chrome with the independent student showed the new English text and all three review links. Japanese browser rendering was not tested.

**A live-update inconsistency was reproduced.** In course 9, the student undid completion of C using the standard button. That button immediately became Mark as done, while the overview still showed 3/3 and the completion message. Reloading changed the overview to 2/3 and Next item to check: Explanation C. The saved state and next-target calculation are correct; the overview does not refresh alongside the standard asynchronous completion control. The format currently renders it on the server and has no AMD update integration. This is a confirmed issue to fix before release, not an inferred novice difficulty. Course 9 is intentionally left with C incomplete; course 10 remains fully complete.

Before release: synchronise the overview after completion changes, verify complete → incomplete and incomplete → complete without reload, preserve focus and standard controls, and rerun the authoring/student-role scenarios. Reuse Moodle's completion events rather than duplicating the completion state. Do not force an unrequested page navigation solely to refresh this card.

The repository CI workflow requires PHP 8.3/8.4 checks, Moodle code/PHPDoc/validation/savepoints, PHPUnit, PHP 8.3 Behat including the same-second role-cache regression, and the reproducible ZIP job. These have not yet been run on the accumulated candidate. The candidate is not release-ready while the live-update inconsistency remains unresolved.

## Live-update candidate fix

The confirmed stale overview has been corrected in the candidate. A Moodle manual-completion event triggers a fresh server-rendered overview for the current user. The course page and its activity controls are not replaced. Older responses are ignored, and a failed refresh clears the outdated overview and uses Moodle's error notification.

Course access is retained. The core fragment service validates the context and calls require_login before the callback. The callback rejects a mismatched course context or another course format, rechecks active course access, and uses the session user rather than accepting a user ID from the client. It must not reinitialise the page after fragment output has started.

Chrome checks with the independent student passed in both Self paced and Teacher guided: 2/3 → 3/3 removes the next target and shows the bounded completion message; 3/3 → 2/3 restores Explanation C as the next target. These transitions required no reload or navigation. Focus remained on the replacement completion button. Course 9 was left at 2/3 and course 10 at 3/3.

A Behat scenario outline now covers both modes and both transitions without a reload step. It has not been executed here. Full PHPUnit/Behat, JavaScript build/lint and remote CI remain release gates; the bundled named AMD module was checked in the local browser. Screen-reader behaviour, slow-network races and genuine first-time participant evaluation remain unverified. This candidate has not been published.
