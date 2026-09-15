# Moodle Marketplace listing

This file contains copy-ready text and metadata for the initial Marketplace application.

## Identity

- Name: Dual Learning
- Component: `format_duallearning`
- Plugin type: Course format
- Initial version: `0.1.0-alpha1` (`2026091500`)
- Maturity: Alpha
- Supported Moodle version: Moodle 5.2
- Licence: GNU GPL v3 or later
- Author and maintainer: Hiroshi Ozeki

## Short description

Switch one Moodle course between self-paced and teacher-guided overviews while preserving its standard activities and learning records.

## Full description

Dual Learning is a course format for programmes that use the same Moodle course for two learning experiences: a teacher-guided class and later self-paced study or review.

The format adds a focused overview above Moodle's standard Topics layout. In **Self paced** mode, each learner sees the first visible incomplete activity, useful work links, completion progress, and assignment state. In **Teacher guided** mode, the same overview also highlights visible question forums so that learners can find the current work, help channel, and submission destination.

Teachers switch the learning mode in the normal course settings. The switch changes presentation only. It does not copy or replace activities and does not rewrite completion records, submissions, grades, deadlines, groups, restrictions, or calendar events. A course can therefore move from a guided class to self-paced review while retaining its learning history.

Moodle remains the source of truth. The overview links learners and teachers back to the standard activity pages for detailed submission state, individual extensions, group submissions, grades, and feedback. Hidden and unavailable activities are excluded through Moodle's course information APIs.

The plugin:

- provides one course format with **Self paced** and **Teacher guided** modes;
- inherits the standard Topics format and keeps its normal section layout;
- identifies the learner's next visible incomplete activity;
- lists visible Lab links and, in guided mode, visible question forums;
- summarises visible assignment status and the base deadline;
- includes English and Japanese interface strings;
- requires no external service, API key, subscription, or JavaScript build step;
- stores no additional personal data; and
- works independently of the optional Dual Learning theme.

## Installation

1. Download the release ZIP.
2. Install it through **Site administration > Plugins > Install plugins**, or place the extracted `duallearning` directory in `course/format/duallearning`.
3. Complete Moodle's standard plugin upgrade.
4. Edit a course and select **Dual Learning** as its course format.
5. Under **Learning mode**, select **Self paced** or **Teacher guided**.

No non-standard post-installation step is required.

## Requirements and limitations

- Requires Moodle 5.2 (`2026042000`).
- Depends only on Moodle's standard Topics course format.
- Assignment details such as individual overrides and group submission membership remain on the standard assignment page.
- The deadline displayed in the overview is the assignment's base deadline.
- A mode change does not rewrite course instructions, deadlines, activities, or forums.
- As with any structural course-format change, administrators should back up production courses before migrating formats.

## Privacy

Dual Learning stores only its course-level format option. It reads existing course, activity, completion, assignment, and grade state for the current request and does not store additional personal data. It does not send data to an external service.

## Links

- Source code: https://github.com/ozekihiroshi/moodle-format_duallearning
- Documentation: https://github.com/ozekihiroshi/moodle-format_duallearning#readme
- Bug tracker: https://github.com/ozekihiroshi/moodle-format_duallearning/issues
- Security policy: https://github.com/ozekihiroshi/moodle-format_duallearning/security/policy
- Initial release: https://github.com/ozekihiroshi/moodle-format_duallearning/releases/tag/v0.1.0-alpha1

## Suggested tags

`course format`, `self-paced learning`, `teacher-guided learning`, `completion`, `blended learning`

## Version release notes

Initial Marketplace candidate. Adds a single Topics-based course format with switchable self-paced and teacher-guided overviews. The mode switch preserves standard Moodle activities, completion, submissions, grades, deadlines, restrictions, and calendar data.

## Screenshot captions

1. **Self-paced learner overview** — The next visible incomplete activity, completion progress, work links, and assignment state appear above the standard course sections.
2. **Teacher-guided learner overview** — The same course highlights current work, visible question forums, and submission destinations without changing learning records.
3. **Learning mode setting** — Teachers switch between Self paced and Teacher guided in the standard course settings page.