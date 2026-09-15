# Reviewer guide

## Purpose

Dual Learning lets one Moodle course present either a self-paced or teacher-guided overview while retaining the same activities and learning records.

## Installation

Install the release ZIP using Moodle's plugin installer. The ZIP contains one top-level directory named `duallearning`. No dependency download, build step, external account, API key, or subscription is required.

## Five-minute functional check

1. Create or open a course that has at least one visible activity and one assignment.
2. Edit the course settings and set **Course format** to **Dual Learning**.
3. Set **Learning mode** to **Self paced** and save.
4. View the course as a learner. Confirm that the overview shows the next visible incomplete activity and the assignment state above the standard Topics sections.
5. Add or show a forum whose type is **Standard forum for general use** and make it visible to the learner.
6. Edit the course settings, change **Learning mode** to **Teacher guided**, and save.
7. View the course again as the learner. Confirm that the visible question forum is included in the work links.
8. Change the mode back to **Self paced**. Confirm that activities, completion state, submissions, grades, deadlines, restrictions, and course sections remain unchanged.

An optional LTI activity with `customtype` set to `pythonlab` is shown as a Lab work link when it is visible. LTI is not required to review the plugin's principal behaviour.

## Expected boundaries

- The overview is read-only and delegates detailed submission, group, extension, grade, and feedback views to Moodle's standard assignment page.
- Hidden or unavailable activities are not presented as learner actions.
- The progress count represents manually marked course materials. It is separate from submission acceptance and grades.
- The displayed deadline is the assignment's base deadline. Individual overrides remain on the assignment page.

## Automated checks

GitHub Actions tests Moodle 5.2 with PHP 8.3 and PHP 8.4. It runs PHP lint, Moodle Code Checker with zero warnings, PHPDoc Checker with zero warnings, plugin validation, upgrade savepoint validation, PHPUnit, and a deterministic release ZIP check.

Latest successful workflow: https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/34931429547