# Overlapping assignment deadlines — 11 October 2026

[日本語](DEADLINE_PRECEDENCE_REVIEW_2026-10-11.ja.md)

The student's applicable date differs from the base date. Dual Learning currently labels its base date accurately, but makes students reconcile it with the overridden date in Moodle's activity list. This strengthens access-review improvement A1.

## Method

The existing isolated Moodle 5.2.4 (Build 20261005), PHP 8.3.33, Boost lab was used. Product code remains c0a8b91. Four new assignments per course were added to Dual Learning / Self paced course19 and Topics course20. Previous assignments and submissions were preserved. Two additional fixture groups contain both fictional students; these do not change membership in the previous restricted-material groups.

Assignments were created through add_moduleinfo, extensions through assign::save_user_extension. Override rows were seeded directly into assign_overrides and course caches rebuilt. This tests runtime behavior of those records, not teacher-form validation, calendar synchronization or notifications. No real users or production systems were touched.

The base deadline is 20 October 2026 12:00 UTC. Group priority1 sets 22 October, priority2 sets 26 October. Student A has the case-specific user override/extension below; B has neither. All dates below are at 12:00 UTC. Europe/London displays 13:00 before the DST transition and 12:00 afterwards, as observed in the browser.

| Case | Student A settings beyond groups | Selected override | Remaining-time date |
| --- | --- | --- | --- |
| 1 | None | 22 Oct, higher-priority group | 22 Oct |
| 2 | User override 21 Oct | 21 Oct | 21 Oct |
| 3 | User override 21 Oct, extension 25 Oct | 21 Oct | 25 Oct |
| 4 | Extension 25 Oct saved first, later user override 28 Oct | 28 Oct | 28 Oct |

Student B consistently resolves to 22 October in all eight assignments, with no extension. Read-only API checks covered both users and all assignments. Browser checks covered A's four Dual assignments, Topics' overview plus cases3/4, and B's case4 in both formats. No student submission or actual deadline passage was performed.

## Findings

**D1 — Group priority matters.** The highest-priority matching group wins even when another matching group has a later date. In this core version, assign::override_exists orders rows by sortorder ASC and selects the first. Explicit user due-date overrides replace that group date. Do not implement a maximum across all group dates. A nearby update_effective_access doc comment still says “most lenient”; the executed override_exists code and measured result instead use priority.

**D2 — Extensions do not shorten the remaining-time date.** The standard renderer's get_time_remaining uses max(overridden due date, extension) when an extension exists. Case4 therefore uses 28 October. However, the standard submission table still displays “Extension due date: 25 October”, while its header shows 28 October and remaining time corresponds to 28 October. Both formats reproduce this confusing combination. This is a core display issue, not a Dual-only calculation error. No core file was changed or external issue posted.

**D3 — The overview needs the student's applicable date.** All four Dual overview entries remain at the base 20 October date. Standard activity-date labels within both course formats already show the group/user override: A sees 22,21,21,28 October respectively. Extension dates appear inside the assignment. The existing base-date warning prevents an unqualified false claim, but does not remove the learner's reconciliation work.

The next small improvement should resolve overrides through Moodle's standard API for the explicit student, then account for extension without shortening a later due date. Keep due date distinct from cutoffdate: lateness and submission acceptance are different decisions. Preserve teacher-facing base-date semantics and avoid persisting student-specific mutated assignment instances across users. Before implementing, include absent/no due date, zero/null override fields, multiple groups, timezone, and user isolation in regression tests. Cutoff, opening time, timelimit, null fields and grade/submission effects were not experimentally covered here.

## Evidence and status

Core sources inspected in the lab: mod/assign/locallib.php::override_exists, update_effective_access and save_user_extension; mod/assign/classes/output/renderer.php::get_time_remaining and extension-date row rendering. API snapshots read get_instance after update_effective_access and get_user_flags; the remaining-time rule was checked against code and visible browser durations, not invented as a new plugin rule.

Local ignored reproduction scripts: moodle-rescue/build/dual-learning-audit-20261011/comparison/deadline-fixture.php and deadline-state.php. Fixture creation refuses duplicates. Dual activity IDs49–52 (assignment7–10); Topics53–56 (assignment11–14). All are unsubmitted and retained. Direct override seeding does not certify teacher UI acceptance of every combination.

No product code, version, main or deployment changed. Documentation and local fixtures only; CI was not rerun. The prior product-code [CI passed](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38123627322). This is developer verification, not usability research or proof of learning effectiveness.
