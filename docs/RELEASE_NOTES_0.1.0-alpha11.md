# Dual Learning course format 0.1.0-alpha11

Build courses with clearer starting points. This alpha improves unit authoring and helps learners distinguish what to submit, which deadline applies to them and which feedback belongs to their current attempt.

## Changes

- Make the new-unit action primary in an empty course and hide the unused learner overview until content is available.
- Refresh the learner overview after marking a material complete or undoing completion, without reloading the course page.
- Require a grade for the current assignment attempt before showing a published-grade result; do not reuse an earlier attempt's grade.
- Show neutral submitted wording for assignments without a grade, and distinguish a pending graded submission from work that needs resubmission.
- Display the learner's effective due date, respecting Moodle's user/group overrides and individual extensions. Teacher summaries retain the base date.
- Add direct actions to view the current attempt's released grade, comments or feedback files, while respecting Moodle's visibility and marking workflow.
- Validate overview refresh access against the current user and course context, and include compiled JavaScript in the installable ZIP.
- Add regression coverage and bilingual developer audit records for authoring, access conditions, deadlines and the submission/feedback cycle.

## Compatibility and upgrade

- Component: `format_duallearning`; build: `2026101100`; maturity: Alpha.
- Supported Moodle version: 5.2. The plugin retains its Moodle 5.2 minimum requirement.
- Install the `moodle-format_duallearning-0.1.0-alpha11.zip` package through Moodle's normal plugin upgrade process. Back up the site before upgrading.
- No database schema migration is introduced. Existing course content, submissions, grades and completion records are preserved.
- Moodle's standard Topics format remains required. LessonMark and the Dual Learning theme remain optional; neither is required to use this course format.

## Scope and evaluation

Developer checks compared Self paced and Teacher guided courses with standard Topics. They covered authoring and student-role checks, learner-specific access and deadlines, and two rounds of file submission and feedback. Downloaded feedback files were checked against their sources. These checks do not establish learning effectiveness or replace evaluation by first-time teachers.

The next-item link follows accessible incomplete activities in course order. Submission-only completion can make the next material available before feedback is returned; it is not a claim that the learner has read feedback or mastered the material. Standard assignment pages remain authoritative for detailed submission and assessment records, including group submissions.

Two standard Moodle display limitations were recorded separately: a previously stored extension date may remain visible beside a newer override, and historical feedback for a no-grade assignment may be absent from the previous-attempt display. This release does not change those core displays.

The browser audit also isolated an upload stall to an active Chrome debugging connection. The user confirmed normal manual uploading after disconnecting it. This is a limitation of the observed test setup, not a verified Dual Learning upload defect; no browser protections or Moodle core code were altered.

Independent-user evaluation, broader mobile and assistive-technology checks remain pending. English and Japanese interface strings are included; the local audit did not test Japanese rendering with a Japanese Moodle core language pack.
