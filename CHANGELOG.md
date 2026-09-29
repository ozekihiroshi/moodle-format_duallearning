# Change log

## 0.1.0-alpha10 - 2026-09-30

- Refresh course-index display state when switching to a student role or returning to the original role, including rapid transitions within the same second.
- Add regression coverage for both learning modes under normal timing and same-second cache conditions. Moodle capability checks and course content remain unchanged.

## 0.1.0-alpha9 - 2026-09-29

- Group existing materials with their visibility information and editing actions in unit authoring.
- Add a standard Moodle student-role check and a route back to unit authoring without publishing hidden drafts. Role checks do not simulate an individual learner's access conditions.
- Improve touch targets and wrapping for authoring and role-check controls.
- Add permission and browser regression coverage for both learning modes, plus bilingual research-informed design principles and a first-time teacher evaluation kit. Participant evaluation remains pending.

## 0.1.0-alpha8 - 2026-09-29

- Stack activity navigation on small screens with full-width touch targets, wrapping long lesson names while preserving keyboard order. Desktop navigation retains three columns.

## 0.1.0-alpha7 - 2026-09-19

- Keep progress and the next learning action above work links and assignment lists (issue #8).
- Use keyboard-accessible disclosures for lists; up to five entries start expanded and longer lists start collapsed. All accessible links remain available in course order.
- Remove Python Lab-specific guidance from the generic overview in English and Japanese (issue #9).
- Add regression coverage for both modes, list lengths, hidden activities and non-Python external tools.
- Refresh English overview screenshots and prepare a teacher evaluation kit; actual teacher evaluation remains pending (issue #5).

## 0.1.0-alpha6 - 2026-09-19

- Fix previous/next links and the activity selector across subsection boundaries, completing issue #2.
- Preserve visibility/access filtering and theme course-index behaviour without modifying Moodle core.
- Add end-to-end forward/backward browser coverage in both learning modes.
- Update review guidance and audit the implemented trial feedback against Issues.

## 0.1.0-alpha5 - 2026-09-18

- Fix next-activity order in courses with subsections using Moodle display-order sorting (issue #2).
- Add nested/flat course regression tests and learner browser coverage for both learning modes.

## 0.1.0-alpha4 - 2026-09-18

- Show returned comments for ungraded individual assignments, respecting feedback availability and marking workflow release.

## 0.1.0-alpha3 - 2026-09-18

- Offer a direct Markdown lesson authoring entry when LessonMark is available.
- Hide assignment summaries when the learner has no accessible assignments.
- Clarify that manual progress marks do not verify code execution or understanding.
- Handle students without an assignment submission record without a type error.

## 0.1.0-alpha2 - 2026-09-18

- Clarify that authors choose a learning design at course creation; preparation and revision belong in both modes.
- Add editable English/Japanese unit outlines and hidden draft creation.
- Add entry points for standard explanation pages, quizzes, and assignments.
- Add existing-material editing links and contextual return links to unit authoring.
- Make instructional hints optional while retaining visibility and unsaved-form information.
- Add permission, cross-course isolation, and activity-move regression tests.
- Preserve standard activities, submissions, grades, and completion records.
- Readiness checks and assisted learning-design changes remain future work.

## 0.1.0-alpha1 - 2026-09-15

- Prepare the first Marketplace application candidate.
- Keep self-paced and teacher-guided behaviours in one switchable course format.
- Separate mode-specific presentation decisions behind internal mode classes.
- Preserve standard Moodle activities, completion, submissions, grades, and deadlines.
- Add English and Japanese language support, Privacy API declaration, documentation,
  and complete GPL licensing metadata.

## 0.1.0-pilot - 2026-09-14

- Validate the two learning modes with local Moodle courses.
- Add visible activity, completion, assignment state, deadline, Lab, and forum summaries.
