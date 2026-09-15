# Dual Learning course format

Dual Learning is a Moodle course format that lets a teacher switch one course between
self-paced and teacher-guided presentation. The switch changes the course overview; it
does not rewrite activities, completion records, submissions, grades, deadlines, or
other standard Moodle learning data.

## Features

- Self-paced mode points each learner to the first visible incomplete activity.
- Teacher-guided mode adds visible question forums to the work links.
- Both modes show visible Lab links and assignment status using standard Moodle state.
- Course settings contain one explicit **Learning mode** selector.
- English is the source language and a Japanese translation is included.
- The format inherits Moodle's Topics format and works with standard Moodle themes.
- The plugin stores no additional personal data.

## Requirements

- Moodle 5.2 (`2026042000`)
- The standard Topics course format

No external service or JavaScript build step is required. Activities such as an LTI
Lab are optional; when present and visible, the format includes their standard Moodle
links.

## Installation

Install the directory as `course/format/duallearning`, then complete Moodle's standard
plugin upgrade. To use it, edit a course, select **Dual Learning** as the course format,
and choose **Self paced** or **Teacher guided** under **Learning mode**.

## Behaviour and limitations

The overview is read-only. Moodle activities remain the source of truth for access
restrictions, completion, submissions, grading, extensions, and calendar dates.
Assignment deadlines shown in the overview are the base deadlines; learners should
open the assignment for individual overrides and extensions. Group submission status
is deliberately delegated to the standard assignment page.

Switching learning mode does not change learning data. Switching away from this format
uses Moodle's normal course-format migration behaviour; create a course backup before
making structural changes to a production course.

## Privacy

Dual Learning stores only its course-level format option. It reads existing Moodle data
for the current request and does not store additional personal data.

## Licence

Copyright 2026 Hiroshi Ozeki.

This plugin is licensed under the GNU GPL v3 or later. See `LICENSE`.

## Documentation

- [Reviewer guide](docs/REVIEWER_GUIDE.md)
- [Marketplace listing text](docs/MARKETPLACE_LISTING.md)
- [Marketplace screenshots](docs/screenshots/README.md)

## Development and support

- Source: https://github.com/ozekihiroshi/moodle-format_duallearning
- Issues: https://github.com/ozekihiroshi/moodle-format_duallearning/issues
- Security: https://github.com/ozekihiroshi/moodle-format_duallearning/security/policy
