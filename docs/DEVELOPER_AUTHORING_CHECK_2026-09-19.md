# Developer authoring walkthrough — 19 September 2026

## Scope

Developer-operated browser checks using separate local teacher and student test accounts. This is **not** a teacher usability study, a timing comparison with Topics, or proof of reduced authoring effort. Issue #5 remains open for that evaluation.

The completed English Python courses provided the screenshot content: course 44 (Self paced) and course 45 (Teacher guided). Their teaching content, completion records and submissions were not modified for the screenshots. A separate local authoring course 32 was used for the creation/submission test.

The served source files report format_duallearning 0.1.0-alpha6 and LessonMark 0.3.0-alpha5. The site's theme is the optional Dual Learning theme, not Boost. The database still recorded the earlier installed plugin versions (2026091803 / 2026091800); this walkthrough is not an installation/upgrade test.

## Completed browser checks

| Step | Observed result |
| --- | --- |
| Start a teacher-guided unit | Created a hidden draft from a title and editable learner text; no additional mandatory design questionnaire. |
| Add Markdown material | Created and displayed a Python explanation; code, headings and contents rendered. |
| Re-edit | Reopened the material from unit authoring, changed its name and saved. Save and return led to its section; Back to unit authoring returned to the same unit. |
| Add practice | Created a standard Quiz, then separately added one True/False question with correct, incorrect and general feedback. |
| Check feedback | Teacher preview deliberately answered incorrectly and displayed the reason, general explanation and correct answer. A separate student attempt answered correctly and displayed the corresponding feedback. |
| Add submission | Created an online-text-only Assignment, disabled the initially enabled dates, retained feedback comments, and set a maximum grade of 2. |
| Student path | Student opened the explanation, marked it done, followed Next to the Quiz, completed the attempt and followed Next to the Assignment. Previous/next links respected the three-material order. |
| Submit and return | Student saved a two-sentence answer; status became Submitted for grading. Teacher saw one submission, saved grade 2/2 and comments, with notification disabled. Student saw Submitted; grade published in the overview and 2/2 plus the exact feedback text on the Assignment page. |
| Distinguish completion | Overview changed from 0/1 to 1/1 manually marked materials. This remained distinct from the assignment's submitted/graded status. |
| Optional help | Hints initially collapsed; the practice hint opened independently and closed with Enter. Actions remained available without reading hints. |
| Completed English courses | Inspected Self paced and Teacher guided learner views; captured the different activity arrangements and the existing course mode setting without changing it. |

Local evidence: course 32, section 627, LessonMark cm 2056, Quiz cm 2057 (question 4061), Assignment cm 2058. Teacher account ID 7; student account ID 5. These are synthetic local test records, not real learner results.

## Remaining friction and priorities

1. **High: long-course overview.** Work links precede the next learning action; 32 Lab links and additional guided forums push progress below the initial viewport. Track #8. Keep the next action near the start and retain all links through accessible presentation without adding author settings.
2. **High: instructions for an absent Lab.** The next-item explanation tells a learner to open Python Lab even in the test course with no LTI activity. Track #9. Use neutral instructions unless a relevant accessible activity exists.
3. **Medium: visibility at the end of authoring.** Showing the hidden draft section did not show its three newly created activities. Each activity needed its own visibility control. The existing reminder to check activity visibility is relevant, but the authoring list does not identify which materials are hidden. Consider compact per-material visibility cues; do not automatically publish unfinished work. Follow-up within #5.
4. **Medium: completion prerequisites.** Course 32 initially had completion tracking disabled. Enabling course tracking was not enough: the material also needed a completion condition. A missing next-item link can therefore be a configuration issue. Consider a contextual cue rather than an additional required setup wizard. Follow-up within #5.
5. **Standard-form effort remains.** Quiz questions are added after saving the activity; review timing lives under Review options; Assignment defaults required changes to dates and submission type. The format's entry points and return links do not remove those decisions.

## Boundaries and local cleanup

- No source code or release version changed for this documentation work. No new automated regression test was needed for screenshots and reporting.
- The full creation/submission/return cycle was performed in Teacher guided mode. Self-paced learner screens were inspected, but a second from-empty self-paced authoring cycle was not performed.
- Student enrolment into course 32 and a temporary password change were explicitly authorised by the user. No credential is included in these documents or images.
- Course 32 was temporarily shown for the enrolled test student and returned to hidden after verification. Existing older hidden units stayed hidden. Test materials, submitted answer, grade, feedback, and enrolment remain for inspection. The test course has an English name and completion tracking enabled.
- No actual teacher participant was observed and no time-saving or usability score is claimed. Human evaluation remains in #5.
- English screenshot files and captions are prepared for the README and Marketplace. Marketplace upload is a separate delivery step; creating this document does not claim that listing images were changed.
