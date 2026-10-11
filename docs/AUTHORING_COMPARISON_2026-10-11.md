# Authoring loop comparison

English | [日本語](AUTHORING_COMPARISON_2026-10-11.ja.md)

2026-10-11. Follow-up to the [design audit](DESIGN_IMPLEMENTATION_AUDIT_2026-10-11.md).

## Result

**All four combinations completed the loop.** An editing teacher created a Page, checked the course as a student, returned to editing, and revised the Page title. The same activity remained in place, with its body and hidden state preserved. An independently enrolled student could neither see the draft nor open its activity directly.

| Course format | Theme | Create / student-role check / return / revise | Independent student |
| --- | --- | --- | --- |
| Topics | Boost | Passed | Hidden draft inaccessible |
| Dual Learning | Boost | Passed | Hidden draft inaccessible |
| Topics | Dual Learning | Passed | Hidden draft inaccessible |
| Dual Learning | Dual Learning | Passed | Hidden draft inaccessible |

This establishes a working route, not a measured reduction in novice difficulty or an improvement in learning outcomes.

## Controlled environment

An isolated site at `http://localhost:8098` runs Moodle 5.2.2 (Build: 20260810), PHP 8.3.33, Format 0.1.0-alpha10 (2026093000), and Theme 0.1.0-alpha7 (2026101000). Installed database versions match the mounted release sources. Sources were extracted from locally retained release ZIPs, with hashes checked against prior deployment records. LessonMark is absent.

Containers, database, data volumes and networks are separate. The existing site at port 8083 was not upgraded or changed. Standard comparison means Topics and Boost on this shared base; it does not imply an otherwise plugin-free Moodle distribution.

The four courses started with equivalent empty hidden sections. Course creation and section creation are outside this exercise. The teacher had the editing-teacher role, not administrator privileges. A separate fictional enrolled student provided a second permission check.

## Observed routes

**Dual Learning keeps the unit context explicit.** With editing enabled, the teacher selected the draft through “Continue building a unit”, then chose “Add an explanation”. Without LessonMark, the authoring screen offered three purpose-oriented additions: explanation, practice questions and submitted work. The explanation opened Moodle's standard Page form.

After saving, “Back to unit authoring” restored the unit. The unit showed both the hidden unit and hidden activity settings. “Check with role: Student” opened the student course view; its return action restored the same unit. The existing material's edit link reopened the standard form for revision.

**Topics retains its general activity chooser.** In the draft section, “Insert content” → “Activity or resource” opened the chooser. Selecting Page and Add opened the same standard form. Student-role switching and restoration used the user menu; the teacher then reopened the Page and its Settings to revise it.

The format therefore supplies a direct context-return route. The form itself retains Moodle's settings and complexity. Both themes supported the tested routes; the Dual Learning theme did not introduce another authoring interaction in this scenario.

## Findings and next decisions

1. **Retain the contextual return.** Returning to the same unit and editing the existing material worked. This is a concrete affordance to test with first-time teachers, not yet evidence that they find it easier.
2. **Clarify the empty learner view.** With only hidden materials, Dual Learning still displayed a review/continue heading without an actionable target. Consider suppressing that overview or giving an accurate empty-state message. The hidden materials remained inaccessible; this is a presentation concern.
3. **Evaluate initial entry.** The authoring route required editing mode and unit selection. Its discoverability remains open. Test first entry before adding another button or concept.
4. **Keep standard routes available.** Advanced configuration remains in standard Moodle forms. Simplify the hand-off where evidence warrants it rather than duplicating those forms.

Prefer these focused presentation and navigation decisions over adding features. Teacher-guided and self-paced behaviour need separate scenarios before making mode-specific claims.

## Evidence and limits

The local laboratory is under `moodle-rescue/build/dual-learning-audit-20261011/comparison/`. `baseline.json` records versions and empty fixtures; `after.json` records final Page IDs, bodies, visibility and per-user access. `artifacts.json` records ZIP hashes. `fixture.php` and `verify.php` provide setup and read-only state checks. The directory contains local runtime secrets in `runtime.env` and must not be published wholesale.

Final state: one Page per course; course/activity IDs are 2/2, 3/1, 4/4 and 5/3. All titles became “Checking a data table — revised”. All bodies retained the same data-table exercise. Both section and activity visibility remained zero. Teacher access was true and independent-student access false in all four cases. Browser checks also found no draft in the student's course views. Direct activity requests returned to the course; the final checked request displayed “Sorry, this activity is currently hidden”.

This was a developer-operated walkthrough. Format/Boost was exercised in the in-app browser; the other full loops used Chrome, with different viewport dimensions. The in-app browser did not open the standard activity chooser reliably; Chrome did, so this was excluded from product findings. No time or click-count advantage is claimed. Initial Moodle tours also interrupted navigation.

This exercise checked the visibility of an unpublished draft, not a student's reading of a published lesson. First-time participants, visible-content comprehension, mobile use, teacher-guided pacing, conditional availability, completion edge cases, long units and subsections remain untested here. A role switch does not substitute for individual-student access checks.

## First follow-up change: empty overview

The candidate now omits the overview card when it has no completion targets, shortcuts or assignments. The standard course content list remains available. A completed target still retains the completion summary. This avoids adding another instruction or control merely to explain an empty panel.

Local validation passed eight rendering checks across the Boost and Dual Learning theme courses: hidden draft, visible Page without completion, incomplete Page, and completed Page. Chrome confirmed that the hidden-only student's Boost course retained the standard section outline without the empty heading. Temporary database changes were rolled back and shared caches purged; all four original fixture states matched the saved baseline afterward. PHP syntax and diff whitespace checks passed. Regression assertions were added to the existing PHPUnit test; the full PHPUnit suite and remote CI have not run because this comparison image has no PHPUnit runtime.

Port 8098 now runs the candidate mount from `compose.candidate.yml`. The original release extraction and `compose.override.yml` remain available to restore the release baseline. No release version was changed and no production deployment was performed.

The next [first authoring entry review](AUTHORING_ENTRY_REVIEW_2026-10-11.md) examines empty-course entry points and separates observed routes from participant hypotheses.

