# File submission UI review — 11 October 2026

[日本語](FILE_SUBMISSION_UI_REVIEW_2026-10-11.ja.md)

The return path works through real assignment forms in Self paced, Topics and Teacher guided. Student downloads were subsequently verified byte-for-byte. Unassisted automated uploads remain unresolved, so the complete file workflow is not an unconditional pass. This is a developer-run audit with fictional accounts, not a novice-user study.

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


## Continuation: Topics, Teacher guided and download verification

This section supersedes the pending comparison/download items above. Earlier observations are retained as a chronological record.

The user's screenshot showed a manually saved Topics draft. On resuming browser control, Assignment72 was already Submitted for grading. This establishes successful manual saving/submission, but a screenshot alone does not establish whether an intermediate upload stall occurred. The user-provided file was left intact and its contents were not inspected.

Topics completed two return cycles. The teacher released feedback-1.txt; the student saw it. Reopening created attempt 2, with the previous submission in history and no current feedback file. The student submitted the synthetic submission-2.txt, and the teacher's attempt-2 feedback area was empty. After feedback-2.txt was released, the student saw only that current feedback file, then reached Page73 through the standard course index. The inherited Released label and absent historical no-grade feedback also occurred here. This continuation did not repeat the Topics In marking visibility check.

Teacher guided completed the same two-attempt cycle. Its overview changed from Submitted to Submitted; feedback returned after the first release. Reopening changed it to Resubmission required, removed the previous review link and made Assignment75 the next target. After resubmission, it showed Submitted without a feedback-review link. The teacher's second-attempt feedback area was empty. Releasing feedback-2.txt restored the current-attempt review action, which opened the standard assignment with feedback-2.txt and the old submission separately in history. The student returned via the course breadcrumb and Next item to check to Page76. Preparation was marked complete solely to exercise navigation. In marking visibility was not repeated in this course.

Both guided-mode feedback downloads were verified using normal student-facing links. The downloaded files matched their local synthetic source files by SHA-256:

| File | Bytes | SHA-256 |
| --- | --- | --- |
| feedback-1.txt | 67 | C06077A7D808F67CDF11220703538304BE744810E3B1C8A6B0AE5D8E6758721C |
| feedback-2.txt | 90 | 49FC411A4C00C947B1B631631DF7F32A36CEFA35D455F5600CF1A8CDF5F0C63B |

The first was downloaded as D:/Downloads/feedback-1 (1).txt and the second as D:/Downloads/feedback-2.txt. This verifies these two guided-course downloads; it does not retrospectively certify every earlier download attempt.

Automated upload recovery remains a limitation. Topics and Teacher guided needed Close → Files after a stalled response. In Teacher guided, an empty refreshed list was transient: retrying before the first upload had settled produced submission-2.txt and submission-2 (1).txt. The synthetic duplicate was deleted using the standard file dialog, and only submission-2.txt was saved and formally submitted. An initial teacher save also contained no feedback file; the later successful upload was confirmed in the list before saving. Do not treat an empty refresh as proof that an upload failed, or a click on Save as proof that a file was included. No browser protection settings or core code were changed.

With submission-only completion, both Dual Learning modes can point to the follow-up material before feedback exists. The overview does not claim the feedback has been read or that the student has mastered the material. Whether a feedback-dependent activity should be offered at that point remains a course-design question, not a newly validated readiness rule.

The planned two-attempt navigation comparison is now covered, subject to upload recovery. The remaining technical gate is isolating the stalled automated upload response from ordinary manual behaviour. Independent-user, mobile and assistive-technology evaluation remain outside this audit. No product code changed; no new CI run was required for this documentation update.
