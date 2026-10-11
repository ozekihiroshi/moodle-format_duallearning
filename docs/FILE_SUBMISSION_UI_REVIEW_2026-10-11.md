# File submission UI review — 11 October 2026

[日本語](FILE_SUBMISSION_UI_REVIEW_2026-10-11.ja.md)

The return path works through real assignment forms. The complete file workflow is not yet a pass: uploads stalled after storing files, and download completion/content were not confirmed. This is a developer-run audit with fictional accounts, not a novice-user study.

## Setup

The localhost:8098 lab used Moodle 5.2.4, PHP 8.3.33, Boost and product commit 907e6b9 (documentation head f6ba283). Course scaffolding was created through Moodle APIs, but submissions, teacher feedback, release and reopening in the Self paced scenario below were performed through normal browser forms. No submission/grade/feedback records were seeded for these courses.

| Course | Format/mode | Preparation / Assignment / Follow-up module IDs |
| --- | --- | --- |
| 24 | Dual Learning / Self paced | 68 / 69 / 70 |
| 25 | Topics | 71 / 72 / 73 |
| 26 | Dual Learning / Teacher guided | 74 / 75 / 76 |

Assignments used grade None, empty enabled comments, feedback files, a single .txt submission up to 1 MB, drafts, marking workflow, manual reopening and two attempts. Completion required submission only. Fictional comparisonstudent and comparisonteacher were used; notification checkboxes remained off. Preparation was marked complete to exercise navigation, not to assert comprehension.

## Self paced results

1. The student uploaded submission-1.txt. The picker stalled, but Close followed by the normal Files link refreshed the list and showed the saved file. Save changes produced Draft (not submitted). Submit assignment and Continue produced Submitted for grading.
2. The overview showed Submitted and moved to the follow-up under submission-only completion. It did not claim that feedback existed or had been read. This still permits the next item to precede feedback; course authors must not equate completion order with readiness.
3. The teacher opened Grade, uploaded feedback-1.txt, used the same picker recovery, selected In marking and saved. A separate student login showed no feedback file.
4. The teacher selected Released and saved. The student overview displayed View this attempt’s feedback; its link opened feedback-1.txt in the standard assignment's Feedback files list.
5. Download was attempted. The automation download event timed out and no downloaded file was found by an exact-name check in the two likely local download folders. This does not prove a site download defect, but downloaded bytes/content were NOT verified.
6. The teacher set Allow another attempt to Yes and saved. The student overview showed Resubmission required and returned the Assignment as the next target. No old feedback-review action remained.
7. The student used Add a new attempt, uploaded submission-2.txt with the same picker recovery, saved a draft and explicitly submitted. The standard page separated the current submission from submission-1.txt in Previous attempts. Its grading status still said Released before the new feedback was entered; no current feedback file appeared.
8. The teacher's attempt-2 feedback area was empty. The teacher uploaded feedback-2.txt, recovered the picker, selected Released and saved. The student's review action opened feedback-2.txt, with submission-1.txt still in history. The student returned through the course breadcrumb and used Next item to check to open the follow-up Page70.

Historical no-grade feedback-1.txt remained absent from the standard previous-attempt display, reproducing the earlier recorded limitation. It was not presented as attempt-2 feedback. See FILE_FEEDBACK_REVIEW_2026-10-11.md.

## Upload failure and comparison

After each upload, the hidden YUI upload iframe displayed a Chrome blocked-page message. The console reported a cross-origin frame document-access SecurityError in the YUI upload-completion handler. The server recorded an upload POST with HTTP 200. Read-only inspection after the initial failure found submission-1.txt in the student's draft file area (33 bytes). Normal Close → Files recovered the list; no security settings or core code were changed to bypass the response.

Topics Assignment72 reproduced the same upload stall with the same submission-1.txt through the normal picker. This rules out assuming the problem is specific to Dual Learning, but does not establish a Moodle core defect: Chrome, the automation connection and lab configuration remain possible contributors. Initial extension file-access denial was a separate issue resolved after user reconnection. Later tab-control interruptions required a new test tab.

The Topics upload was left at the picker after the failure. Its full return/resubmission cycle and the Teacher guided scenario remain untested. The user was asked whether a manual upload in Topics also stalls, to distinguish the automation connection from ordinary browser behaviour. Do not report these comparisons as complete.

## Next gate

Resolve or explain the upload-response failure and verify a normal download's contents before treating the workflow as passed. Resume the Topics lifecycle and Teacher guided navigation after that. No product code changed during this audit and CI was not rerun; the successful 907e6b9 run remains the code reference. Mobile, assistive technology and independent users remain outside this audit.
