# Reviewer guide

## Purpose and scope

Dual Learning supports authoring for a teacher-guided or primarily self-paced course. Authors normally choose the approach at the start; both include preparation and revision. Alpha2 adds authoring tools to the existing learner overview. It does not automatically convert teaching materials when the learning mode changes.

## Installation and upgrade

Install the release ZIP using Moodle's plugin installer. It contains one top-level duallearning directory. Version 2026091801 upgrades alpha1 and alpha2 through the normal plugin upgrade process. No new tables, custom backup fields, external services, or build steps are introduced.

## Authoring check

1. Create a test course using Dual Learning. Choose a learning mode and enable editing as an editing teacher.
2. Select Start a unit from an outline. Adapt the title and learner-facing text, and create the hidden draft.
3. Verify that the draft is hidden and the author is taken to its authoring screen. Cancel a second draft and confirm no section is added.
4. Use Add an explanation, Add practice questions, or Collect submitted work. These open standard forms; opening them alone does not create an activity.
5. Save an activity and use Back to unit authoring above its content. Verify it returns to the correct unit.
6. Reopen the activity through the existing-material list. For a quiz, test the separate questions-and-feedback editing link.
7. Confirm hints are initially collapsed and can be expanded with keyboard or pointer. Activity actions work without opening hints. Editing forms remind authors to save before returning.
8. Verify learners do not see authoring controls. Module-level editing restrictions should suppress the respective editing links.
9. Move a test activity to another standard unit. Its return link should follow its current section.
10. Repeat outline creation with the other learning mode. Preparation and revision remain available in both.

Use a test course for these checks. Standard Moodle visibility, grading, completion, and access rules remain authoritative.

## Learner overview check

With visible materials, an assignment, and completion tracking enabled, check the next accessible incomplete activity and distinct materials/submission/grade states. Guided mode additionally lists accessible general-purpose question forums. Completed materials remain accessible under normal Moodle permissions. Changing the learning mode does not rewrite the activities or learner records.

## Boundaries

Standard activity settings and quiz question editing are not replaced. Assignment overrides and group submissions are detailed on standard activity pages; the overview shows the base deadline. Readiness checks and assisted changes of learning design are not implemented. Classroom usability improvements have not been quantified.

## Automated checks

GitHub Actions checks Moodle 5.2 with PHP 8.3 and 8.4: lint, Code Checker and PHPDoc with zero warnings, plugin validation, savepoints, PHPUnit (14 tests), and reproducible ZIP generation. Tests cover draft creation, authorisation, cross-course section rejection, module permissions, and return routes after moving activities.

Workflow runs: https://github.com/ozekihiroshi/moodle-format_duallearning/actions
