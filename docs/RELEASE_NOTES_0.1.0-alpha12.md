# Dual Learning 0.1.0-alpha12

This release repairs the route back to saved units and restores Moodle's standard material choices within unit authoring. It addresses problems encountered while creating course materials on an iPhone, with changes shared by desktop and mobile users.

## Changes

- Make existing units and hidden drafts reachable from the course and new-unit screen, including when editing is off for authorised teachers. Prioritise Open existing units when units exist and Create a new unit in an empty course.
- Correct the scope of authoring navigation so the selected unit opens without repeated create/resume controls.
- Add reversible clearing of the unit introduction while preserving its title and protecting text entered after clearing.
- Clarify saved-unit visibility and the distinction between a unit introduction and separate learning materials.
- Use Moodle's standard activity/resource chooser in unit authoring, retaining the target unit and standard Page, File, URL, Quiz and Assignment forms. Clarify the purposes of Quiz and Assignment.
- Clarify teacher guidance for submission lists versus individual grading, and name both grade and feedback in the student review action.
- Keep optional student-role checks and preview in a disclosure; preserve standard course editing and permission checks.
- Add regression coverage for authoring recovery, Page/PDF/URL placement and hidden-material access. Document assisted iPhone evaluation and remaining accessibility checks.

## Compatibility and upgrade

- Component: `format_duallearning`; build: `2026101200`; maturity: Alpha.
- Supported Moodle version: 5.2. No additional Moodle-version compatibility is claimed.
- Upgrade the existing course-format plugin normally. No new database schema is introduced; saved units, activities and learning records are retained.
- The Dual Learning theme and LessonMark are optional and do not need a new release for these changes.

## Evaluation and limits

Automated coverage compares standard Topics and Dual Learning for adding Page, PDF File and URL resources to the intended section, including hidden-material access. Existing role-switching and learner navigation regressions remain in scope. Final release-candidate CI results are recorded separately.

A continuing participant reported using an iPhone to resume editing, add materials and complete a student submission and teacher-feedback check. Desktop screenshots corroborate the saved submission, published grade and feedback; these are assisted observations, not an independent first-time user study or proof of learning effectiveness. The learner understood what to revise after reading feedback. The existing grade-link destination was accepted as usable and is unchanged.

The standard chooser adds selection steps compared with the previous fixed shortcuts, but preserves access to Moodle's available resource types and familiar forms. Experienced-user efficiency and VoiceOver testing remain open. This release does not claim complete accessibility validation.
