# Moodle Marketplace listing

Copy-ready metadata for the alpha3 update to the existing format_duallearning submission.

## Identity

- Name: Dual Learning
- Component: `format_duallearning`
- Plugin type: Course format
- Version: `0.1.0-alpha3` (`2026091801`)
- Maturity: Alpha
- Supported Moodle version: Moodle 5.2
- Licence: GNU GPL v3 or later
- Maintainer: Hiroshi Ozeki

## Short description

Build teacher-guided or self-paced courses with editable unit outlines, focused authoring actions, and learner guidance. Keep standard Moodle activities and learning records.

## Full description

Dual Learning helps authors design a course for how learners will be supported: by a teacher, or primarily by the materials themselves. Choose Teacher guided or Self paced when starting a course. Preparation and revision belong in both approaches; reviewing a lesson does not require changing the learning mode.

For authors, the format provides an editable unit outline suited to the chosen approach. Start with a title and learner-facing text, save a hidden draft, then add explanations, practice questions, and submission activities through Moodle's standard forms. Existing-material links make it easy to reopen content, settings, and quiz questions. A contextual return link leads back to the same unit from standard activity and editing pages.

Actions remain visible while optional hints can be opened when needed. Authors do not need to fill in an additional design questionnaire or choose an experience level. Visibility information and reminders to save form changes remain visible.

For learners, a focused overview above standard Topics sections shows the next accessible incomplete activity with completion tracking, work links, materials progress, and assignment state. Teacher-guided mode also includes accessible question forums. Materials marked complete, assignment submissions, and published grades are distinguished. Standard activity pages remain the place for detailed grades, feedback, individual extensions, and group submissions.

Changing the learning mode changes the overview; it does not automatically adapt the teaching content to a different learning design. It does not rewrite activities, completion records, submissions, grades, deadlines, or restrictions.

The plugin includes English and Japanese interface strings, works with standard Moodle themes including Boost, and has no mandatory dependency on the Dual Learning theme, LessonMark, or an external Lab service. The only plugin dependency is Moodle's standard Topics course format.

This is an alpha for evaluation. Standard activity forms retain their normal settings. Automated readiness checks, a redesigned initial mode-selection screen, and assisted learning-design changes are not yet implemented. Ease-of-use improvements have not yet been measured in a teacher study.

## Installation

Install the ZIP through Site administration > Plugins > Install plugins, or place its duallearning folder in course/format/duallearning. Complete Moodle's standard upgrade. Choose Dual Learning in course settings and select the learning mode. No build step, external account, API key, or subscription is required.

## Privacy

The plugin stores its course-level learning-mode option. Author-created outlines are standard course sections and summaries; activities are created through standard Moodle forms. It uses standard Moodle storage rather than a separate personal-data store and does not send data to external services.

## Links

- Source: https://github.com/ozekihiroshi/moodle-format_duallearning
- Documentation: https://github.com/ozekihiroshi/moodle-format_duallearning#readme
- Issues: https://github.com/ozekihiroshi/moodle-format_duallearning/issues
- Release: https://github.com/ozekihiroshi/moodle-format_duallearning/releases/tag/v0.1.0-alpha3
- Branch: main
- Tag: v0.1.0-alpha3

## Version release notes

This update addresses feedback from adapting a Japanese Python introductory unit to Dual Learning.

- Add a direct Markdown lesson authoring action when LessonMark is enabled and the author has permission to add it. LessonMark remains optional.
- Hide the assignment summary when the learner has no accessible assignments in the course.
- Clarify that manual completion marks do not verify code execution, understanding, submission or assessment.
- Add an optional hint to avoid repeating the activity title as an H1 in Markdown content.
- Fix an overview type error for students without an assignment submission record.

Existing activities, content, submissions and grades are preserved. Moodle 5.2; Alpha maturity.

## Screenshots

Existing overview screenshots remain valid. They use Japanese demonstration content; English-language demonstration screenshots remain a separate documentation task.
