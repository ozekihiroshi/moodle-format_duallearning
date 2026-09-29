# H1 prototype review — 2026-09-29

English | [日本語](AUTHORING_UX_H1_REVIEW.ja.md)

Status: local functional prototype on `codex/authoring-ux-first-loop`. This is a developer walkthrough, not a novice usability study. See [H1 and its research limits](RESEARCH_DESIGN_HYPOTHESES.md).

## Observation and change

The prior code listed saved materials with View / Edit links but no per-item visibility settings. Its generic View label did not distinguish teacher viewing from student access. These are code-review findings, not evidence that teachers misunderstood the interface.

The prototype places each saved material's name, course/unit/activity visibility settings and configured access restrictions beside its existing review/edit actions. Hidden course/unit settings are shown when applicable; the activity setting is always shown. These describe settings, not a complete effective-access calculation. View is labelled “View as teacher”, with a short reminder to check individual student access separately. Actions wrap on narrow screens and have a minimum height of 44 CSS pixels. Moodle still owns activity forms, content, restrictions and permissions. No schema changes or dependencies on the other two plugins were added.

## Checks completed

- Local Moodle, Boost, an enrolled editing teacher, standard Page: create hidden unit → create material → save and display → return to unit authoring → edit that same material → save revised name and display. The same activity ID remained in use, and the draft remained hidden.
- The developer used TinyMCE's source editor to enter fixture text. This is not a demonstration that first-time teachers can author without assistance. At a very narrow viewport, scripted pointer activation of the standard save button did not consistently submit; keyboard Enter completed saving. Real-device testing of the standard editor/save controls remains necessary.
- English and Japanese UI checked; Japanese at 390 × 844 had no horizontal page overflow, and both changed action targets measured 44 pixels high. Desktop view was also checked. This does not constitute a complete accessibility audit.
- Local integration checks: teacher actions available; editing targets the same activity; reads do not mutate it; hidden course/unit/activity settings and an access restriction are reported; student cannot access the hidden material or the authoring service. Temporary settings used for checks were rolled back. All 12 assertions passed.
- PHP syntax checked for the changed service, page and PHPUnit test file. Added regression cases cover hidden containers without publication and restricted activities. The PHPUnit suite and hosted plugin CI have not been run for this prototype; local integration checks do not replace them.

## Decision and remaining work

Keep H1 as a reviewable local prototype. Do not call the whole authoring milestone complete: actual student-view verification, novice observation and comparison with Topics remain open. No learning outcomes, cognitive-load reductions or time savings were measured. LessonMark/theme combinations were not revalidated in this change; the tested route uses Boost and standard Page, with LessonMark installed but unused.

Before release, run full plugin CI and review real-device behaviour. Then evaluate whether authors understand saved state and can return to revise. Extend the flow to a truthful student-view check without publishing a hidden draft merely to inspect it. Do not introduce H2/H3 features until they have their own scoped design and checks. Published Marketplace versions and AWS were not changed.
