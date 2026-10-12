# Audit of alignment with Moodle authoring

English | [日本語](MOODLE_AUTHORING_ALIGNMENT_2026-10-12.ja.md)

2026-10-12. Audit only; no further product behaviour changed in this review.

## Finding

**Standard authoring remains available, but the assisted route bypasses material selection.** The course still provides Moodle's activity chooser in editing mode. The separate unit-authoring screen offers a fixed selection of direct links without a visible route to that chooser. Keeping standard forms is therefore insufficient to establish that the standard authoring workflow is preserved for users entering through assistance.

The participant found the distinction between the unit introduction and Page content unclear. After guidance they saved a Page, then asked where PDF, Markdown and other tools could be selected. This is assisted task completion and a discoverability observation, not evidence that standard Moodle or every user requires the same redesign.

## Evidence and scope

- Moodle 5.2.4 (Build 20261005), Boost, editing teacher Comparison Teacher. Desktop Chrome developer walkthrough, not an iPhone or experienced-teacher usability trial.
- Standard Topics course 2 and Dual Learning developer course 29, in the isolated evaluation site. Participant courses were not modified. Course 28 was inaccessible to this developer teacher, so the existing Topics fixture was used instead of changing enrolments.
- Format branch `codex/authoring-entry-recovery-20261012`, HEAD `ec81e72`, plus the uncommitted Add a page label/help changes already deployed to the evaluation mount. This is not the published plugin version.
- Reviewed `format.php`, `author.php`, `unit.php`, `classes/local/unit_actions.php`, `classes/local/unit_starter.php`, `classes/form/unit_starter.php`, and the existing authoring specification.
- On Topics: Edit mode → Insert content in the target section → Activity or resource → chooser → Resources → File → Add. Confirmed New File, file selection and standard save buttons. Cancelled without uploading or saving. The chooser displayed search, categories, information and an existing starred item; favourites were not changed.
- On Dual Learning's course page: the same insertion route opened the standard chooser, preserving the target section. Closed it and restored the developer session's edit mode to off. No new activity or unit was created.
- LessonMark is absent from this lab. Its absence cannot validate installed-plugin discovery. File-form arrival does not establish successful PDF upload or student access.

## Comparison

| Stage | Standard route | Assisted route and finding |
| --- | --- | --- |
| Choose a place | Work in an existing course section; create a section only if needed | A separate unit list and creation form are offered. A unit maps to a standard section; neither a new unit nor an introduction should become a prerequisite for every material |
| Choose material/tool | Activity chooser offers available types, descriptions, search, categories and starred items | `unit_actions::build()` enumerates Page, LessonMark, Quiz and Assignment, filtered by course availability. File, URL, Book, Folder, Forum, H5P and external tools have no entry here |
| Enter content | A type-specific standard form follows the selection | Standard forms are reused. Direct Page creation is a valid shortcut once Page is chosen, but is not a general material-selection route |
| Store introduction | Section summary and activity content have different purposes | Starter text is saved as section summary. The custom summary editor has `maxfiles=0`; it is not an equivalent substitute for a standard resource/file workflow |
| Save and return | Standard save/return, save/display and cancel controls | Retained; the participant could use them with assistance. Contextual return links may help but are not proof of independent usability |
| Visibility | Moodle section/activity visibility and access rules apply | Hidden sections are labelled drafts. This is a presentation convention, not a separate personal draft store; no automatic publication is warranted |

The former Add an explanation label compounded the issue by naming a purpose rather than the Page type. Add a page improves correspondence, but renaming alone does not expose the other choices.

## Design decision to take into the next change

1. Make the standard chooser reachable from the current unit without requiring the teacher to discover another workflow. Preserve the target section and permissions. Evaluate a clear general add entry before retaining any direct shortcuts.
2. Reuse Moodle's chooser rather than rebuilding a partial list. Respect installed/allowed activities, search, descriptions, categories, favourites and administrator configuration. A raw link to `modedit.php?add=page` does not satisfy this requirement.
3. Keep standard course editing available. Do not force unit creation, an introduction, an authoring wizard or a novice/expert mode before adding a file or other resource.
4. Keep standard resource names visible. Explanations may describe purpose, but must not silently decide the resource type on the teacher's behalf.
5. Retain standard forms and save behaviour. Avoid broad form simplification based on this single participant's difficulty.

`SIMPLE_AUTHORING_SPEC.ja.md` SA-04 and its second-stage implementation currently encourage direct purpose-based links. That intent must be reconciled with principle revision 5: assistance should clarify choices without making its shortlist appear to be the complete set of Moodle capabilities. This audit records the conflict; the replacement flow is not yet implemented or user-validated.

## Acceptance checks for the next implementation

Compare equivalent tasks in Topics and Dual Learning: create a Page, upload an existing PDF as File, add a URL, and discover an installed optional activity. Check that every path preserves the selected section, that cancel leaves no new activity, and that standard settings and save/return controls remain available. Check hidden/visible sections, restricted activities and student access. Test without LessonMark and, separately, with it installed and permitted. Do not install it merely to make the independent-format test pass.

Observe a first-time teacher and an experienced teacher separately. Record assistance, wrong turns and relearning costs, not only clicks. Include iPhone touch and keyboard/accessibility checks. PDF upload, mobile tap behaviour, optional-plugin discovery and experienced-teacher evaluation are still open; the earlier automated CI does not settle these questions. Avoid speculative CSS fixes for the earlier wrong-page tap report, whose cause remains unconfirmed.

## Sources

- [Moodle 5.2: Activity chooser](https://docs.moodle.org/502/en/Activity_chooser): standard selection and discovery features.
- [Moodle 5.2: Course homepage](https://docs.moodle.org/502/en/Course_homepage): editing mode, sections and course navigation.
- [Moodle 5.2: File resource](https://docs.moodle.org/502/en/File_resource): existing files as resources.
- [Earlier comparison](AUTHORING_COMPARISON_2026-10-11.md) and [participant recovery record](AUTHORING_RECOVERY_2026-10-12.md). Earlier versions and assisted trials are not interchangeable with this audit.

## Implementation follow-up: standard chooser

The unit entry now opens Moodle's standard activity/resource chooser. The former fixed Page/LessonMark/Quiz/Assignment shortlist has been removed. A short explanation maps Page to text/images, File to an existing PDF and URL to a web link; it does not choose a type for the teacher.

The implementation reuses the core chooser's section data, button template, JavaScript and standard creation forms. Course-editor-only insertion actions (such as subsection insertion) are not rendered on the separate unit screen. The full standard course editing route remains available. No edit-mode preference is changed merely by opening the unit screen, and no resource is created until its form is saved.

Developer inspection on the local lab confirmed the chooser opens with editing off, retains the selected section ID in Page/File/URL targets, and shows the standard Resources category and existing starred items. At 390px width the chooser opens and fits the viewport. This is a desktop narrow-viewport check, not an iPhone or assistive-technology evaluation. The participant's saved introduction and Page remain present; no participant activity was changed.

A comparative browser regression creates Page, PDF-as-File and URL in Topics and Dual Learning, checks the saved section and PDF, cancels an additional Page, and checks hidden materials remain absent for a student. Results will be recorded after CI completes. Independent first-time and experienced-teacher evaluation, actual iPhone upload, screen-reader use, and discovery of an installed optional plugin remain separate acceptance work.
