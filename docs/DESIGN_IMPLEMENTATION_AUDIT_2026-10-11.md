# Dual Learning: design and implementation audit

English | [日本語](DESIGN_IMPLEMENTATION_AUDIT_2026-10-11.ja.md)

2026-10-11. Initial source audit and administrator walkthrough. No first-time participant study, measured Topics/Boost comparison or learning-outcome study has been completed in this audit.

## Principles

**Less complexity for teachers. More clarity for learners.**

**Every feature must justify its complexity.**

These guide decisions; they do not describe a demonstrated outcome. Apply them to retaining existing features. Prefer removal, consolidation, clearer presentation and safer defaults before adding controls. Research supplies design hypotheses, not evidence that Dual Learning improves learning. Both principles are now in revision 4 of the [shared development principles](DEVELOPMENT_PRINCIPLES.md).

## Evidence boundary

Source inspected: Course Format 0.1.0-alpha10 (2026093000) and Theme 0.1.0-alpha7. Browser observation: Moodle 5.2.2 on localhost:8083, an existing teacher-guided course, administrator, editing off.

The environment is not a release acceptance baseline. The mounted format file declares alpha10, but plugin-manager metadata reported alpha8 and database version 2026091901. The running theme mounts the legacy alpha5 source rather than the independent alpha7 repository. An administration upgrade screen appeared; direct course access worked. No global upgrade, course-content change or deployment was performed.

## Five dimensions

| Dimension | Implemented value | Open question |
| --- | --- | --- |
| Author decisions | Hidden unit creation, standard activity forms, return to the unit | Standard course creation remains; mode selection and starter text add decisions |
| Learner orientation | Visible activities and Moodle completion determine a next candidate | First incomplete is not last visited; no tracked activities means no candidate |
| Learning modes | Different headings, starter text and forum shortcuts | Both modes use the same first-incomplete rule, not a teacher's current lesson |
| Moodle compatibility | Topics, Boost, standard permissions, submission and assessment | Transitions between custom and standard screens, especially subsections |
| Interaction consistency | Actions remain visible while hints collapse | Two editing routes, mixed purpose/format labels, add actions below existing materials |

## Priority investigation candidates

These are source-backed concerns, not measured participant failures.

1. **Entry and return.** `format.php` exposes authoring entry points under editing mode and capabilities. `unit.php` places add actions after existing materials. Compare an empty course and a long unit before adding more entry points.
2. **Purpose versus format.** `classes/local/unit_actions.php` offers Page, LessonMark, Quiz and Assignment. Explanation and Markdown lesson overlap in purpose. Forum, File and Book have no direct shortcut here, although standard Moodle routes remain available. Evaluate discoverability rather than declaring these capabilities missing.
3. **Meaning of next.** `classes/local/overview.php::first_incomplete()` serves both modes. Test completion disabled, old unfinished work, conditional availability, failed activities and all currently visible targets complete. Check whether headings such as current work or continuation fit the actual rule.
4. **Collapsed information.** Overview shortcuts and assignments collapse above five items. At six, finding a deadline or status requires expansion. Compare a time-sensitive submission task; fewer visible rows do not automatically mean lower effort.
5. **Subsections.** Existing-material enumeration addresses the selected section directly; `authoring_navigation.php` excludes delegated sections as return destinations. Reproduce editing a child activity and returning to the original task before deciding the change.

The theme supports presentation rather than instructional sequencing: Boost structure, visible focus, readable line widths and local scrolling. These are useful affordances, not proof of easier decisions. Verify the alpha7 navigation configuration with standard Page and Book independently of the format and LessonMark.

## Browser observation

The teacher-guided homepage showed a current-work heading, collapsed work-entry and submission/status groups, the standard content list below and a course index. Some administrator-visible entries repeated titles across LessonMark and hidden Page resources. This includes fixture/content design; it does not establish duplicate student-visible content. Neither current lesson identification nor novice understanding was established by this observation.

## First milestone: verify one authoring and checking loop

**Establish equivalent conditions first.** Use an isolated release-pinned environment with matching database and source versions. Do not update all existing plugins or existing courses merely to obtain a comparison.

| Stage | Task | Acceptance evidence |
| --- | --- | --- |
| Baseline | Topics+Boost, Format+Boost, Topics+Theme, Format+Theme with equivalent fictional content | Record Moodle/PHP/plugin versions, role, completion settings and viewport; include absence of LessonMark |
| Author | Create one explanation in a hidden unit, save, check student visibility, rename and return | Correct saved content and visibility, identifiable return destination, advanced settings still reachable |
| Learner | Identify purpose, next action, help route and submission state | Separate enrolled student account; role switching does not establish individual access |
| Edge cases | Completion off, locked work, old unfinished work, six assignments, subsection | Separate reproducible observation from inferred confusion |
| Decision | Compare removal, consolidation, layout and defaults | User/task, value, added complexity, simpler alternative, unknowns and acceptance check for each decision |

Use the existing [evaluation kit](AUTHORING_UX_EVALUATION.md). Record task, expected result, actual path, hesitation, errors, help requests, transitions, elapsed time and state correctness. Record comparison order and prior Moodle experience. Mark developer assistance explicitly. No new measurements are available yet.

First-time participant observation follows developer checks and automated preparation. Neither CI nor a small usability study proves better learning outcomes. Keep all three plugins independently useful.

## Follow-up comparison

A release-pinned environment and the four-way authoring loop comparison are now complete. See [Authoring loop comparison](AUTHORING_COMPARISON_2026-10-11.md). This adds functional evidence; novice usability and learning outcomes remain unmeasured.

