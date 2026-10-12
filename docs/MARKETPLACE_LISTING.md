# Moodle Marketplace listing

Metadata prepared for the alpha12 format_duallearning release. The live listing uses shorter opening sentences and separate authoring, learner guidance, integration and current-scope sections.

## Identity

- Name: Dual Learning
- Component: `format_duallearning`
- Plugin type: Course format
- Version: `0.1.0-alpha12` (`2026101200`)
- Maturity: Alpha
- Supported Moodle version: Moodle 5.2
- Licence: GNU GPL v3 or later
- Maintainer: Hiroshi Ozeki

## Short description

Build teacher-guided or self-paced courses with editable unit outlines, focused authoring actions, and learner guidance. Keep standard Moodle activities and learning records.

## Full description

Dual Learning helps authors design a course for how learners will be supported: by a teacher, or primarily by the materials themselves. Choose Teacher guided or Self paced when starting a course. Preparation and revision belong in both approaches; reviewing a lesson does not require changing the learning mode.

For authors, the format provides an editable unit outline suited to the chosen approach. Start with a title and learner-facing text, save a hidden draft, then add explanations, practice questions, and submission activities through Moodle's standard forms. Existing-material links make it easy to reopen content, settings, and quiz questions. A contextual return link leads back to the same unit from standard activity and editing pages.

Return to saved work through Open existing units, including hidden drafts. Use Moodle’s standard activity/resource chooser to add Page, File, URL, Quiz, Assignment or another available type to the same unit. Optional student-role checks can be opened when needed. Authors do not need to fill in an additional design questionnaire or choose an experience level. Visibility information and reminders to save form changes remain visible.

For learners, a focused overview above standard Topics sections shows the next accessible incomplete activity with completion tracking, work links, materials progress, and assignment state. Progress and the next learning action appear before work links and assignments. Lists of up to five entries start expanded; longer lists can be opened when needed. Teacher-guided mode also includes accessible question forums. Materials marked complete, assignment submissions, and published grades are distinguished. Learners see their effective due date, including Moodle overrides and individual extensions, and can open the current attempt’s released results. Standard activity pages remain the place for detailed grades, feedback and group submissions.

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
- Release: https://github.com/ozekihiroshi/moodle-format_duallearning/releases/tag/v0.1.0-alpha12
- Branch: main
- Tag: v0.1.0-alpha12

## Version release notes

Use the English [alpha12 release notes](RELEASE_NOTES_0.1.0-alpha12.md).
Publication is pending final release checks; do not infer publication from this preparation document.

## Screenshots

English screenshots now use the completed Python Foundations Self paced and
Teacher guided courses, with separate test-unit authoring captures. See
[files, suggested order and captions](screenshots/README.md).
The optional Dual Learning theme supplies the visual styling; the course
format also supports Boost. Overview images 01 and 03 show the alpha7 layout
using a test teacher's Student role preview. Progress figures in these captures
are illustrative role-preview state, not evidence of a real learner's work.

Upload the alpha12 ZIP to the published plugin after release validation.
Existing alpha7 screenshots illustrate the earlier layout; they do not show every alpha12 improvement.
Marketplace acceptance and automated review must be confirmed on the site.
