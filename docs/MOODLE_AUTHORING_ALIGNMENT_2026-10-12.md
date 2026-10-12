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

`SIMPLE_AUTHORING_SPEC.ja.md` SA-04 and its second-stage implementation currently encourage direct purpose-based links. That intent must be reconciled with principle revision 5: assistance should clarify choices without making its shortlist appear to be the complete set of Moodle capabilities. This was the pre-change audit. The follow-up below records the replacement implementation; participant validation remains separate.

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

A comparative browser regression creates Page, PDF-as-File and URL in Topics and Dual Learning, checks the saved section and PDF, cancels an additional Page, and checks hidden materials remain absent for a student. The completed CI results are recorded below. Independent first-time and experienced-teacher evaluation, actual iPhone upload, screen-reader use, and discovery of an installed optional plugin remain separate acceptance work.

## Verified comparison results

Code commit `55c1336` passed [CI 38193401052](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38193401052) on Moodle 5.2.4 (Build 20261005). PHP 8.3 and 8.4 each passed 36 PHPUnit tests / 344 assertions, PHP lint, code and documentation checks, plugin validation and JavaScript build/lint. Browser regression passed 12 scenarios / 257 steps, followed by the separate student-role cache regression (2 scenarios / 54 steps). The reproducible ZIP check also passed. The initial run exposed coding-style issues and a PHPUnit renderer setup error; both were corrected before this successful run.

| Check | Standard Topics | Dual Learning unit entry |
| --- | --- | --- |
| Page through the standard chooser; save and display its text | Passed | Passed |
| File through the chooser; upload a real PDF and save/return | Passed | Passed |
| URL through the chooser; save/return | Passed | Passed |
| All three resources saved in the selected section | Passed | Passed |
| Cancel a fourth resource; exactly three remain | Passed | Passed |
| Hidden section retained; resources absent from the student course page | Passed | Passed |

The comparison used isolated Behat fixtures. PDF upload was exercised through the standard file manager in Selenium, not seeded into storage. These results establish functional parity for the tested tasks; they do not establish a reduction in novice confusion or learning gains. No production release, tag, or main-branch integration was performed.

## Purpose review after adopting the standard chooser

**Retain the change conditionally.** There is no evidence that this change removed the core ability to structure units, resume authoring or guide learners. Resource coverage and consistency with standard editing improved. Purpose-based guidance and the shortest route to a known activity were reduced. Overall usability and learning improvement are not established.

### Evidence and scope

On 12 October 2026, compared `ec81e72` (before the chooser change) with verified code `55c1336`. Reviewed principle revision 5, the authoring milestone, unit UI, creation service, learning modes, learner overview/navigation and regression coverage. This review changes documentation only; the successful CI for unchanged runtime code was not rerun.

The participant subsequently reported entering a URL on iPhone, saving/displaying it, and finding re-editing satisfactory. This is participant self-report, not directly observed interaction. The conversation suggested a URL task and prompted saving/re-editing, and the participant had already used earlier screens. Do not count this as an unassisted first encounter or a timed before/after comparison. The participant reported no prior iPhone PDF-upload experience; actual iPhone PDF upload remains untested.

### Five evaluation axes

| Axis | Retained or improved | Cost, limit and judgement |
| --- | --- | --- |
| Author decisions | Unit title, visibility, saved materials, editing and return routes remain together; File/URL are now reachable from the unit entry | Opening the chooser and selecting a type adds decisions. Reachability improved; lower overall novice cognitive burden is not established |
| Learner clarity | Next incomplete material, ordered navigation, personal deadlines and submission/feedback code are unchanged; existing regressions passed | No new learner mobile journey or learning-outcome evaluation; authoring success does not establish learner improvement |
| Guided and self-paced learning | Mode-specific starter text, headings and guided question-forum shortcuts remain; completion and role-preview regressions cover both modes | This does not validate the educational adequacy of either mode; their presentation policies do not automate whole-course learning design |
| Moodle consistency | Standard chooser/forms, saves/cancel, permissions and visibility preserved; equivalent Page/File/URL tasks compared with Topics | Core terminology and the number of options remain; standardisation alone is not evidence of ease of use |
| Interaction consistency | Removes a partial custom list competing with the standard list; unit list, creation, re-editing and role checks remain | Users familiar with the old direct buttons must select again; expert speed and relearning costs are unmeasured |

### Explicit loss of assistance

The old unit page offered direct Page, optional LessonMark, Quiz and Assignment links with individual hints. For a known type, a single link opened the form. The current route requires at least three actions: open chooser, select type, Add; search or category changes add more. This is a structural comparison, not a measured time or error-rate result.

The old Quiz hint explained adding questions and checking feedback; the Assignment hint explained submission types. These strings still exist but are no longer rendered on the unit page. Current examples cover only Page/File/URL. Support for translating “check understanding” or “collect submitted work” into Quiz/Assignment has therefore weakened. The dedicated LessonMark entry also disappeared; installed optional-plugin discoverability remains unverified. Record these costs rather than claiming all assistance was retained.

### Remaining Dual Learning value

Its role remains contextual. Around standard material selection it gathers the unit being edited, saved materials, visibility, re-editing, student-role checks and return paths. On the learning side it derives guidance from visibility, completion, deadlines and submission state. Many underlying capabilities also exist in Moodle; distinct value lies in how they are arranged around teaching and learning work. The size of this added value over Topics has not yet been measured.

Code evidence: `unit.php`, `classes/local/unit_starter.php`, `classes/local/authoring_navigation.php`, `classes/local/student_view.php`, `classes/local/overview.php`, `classes/local/mode/`, and `classes/output/ordered_activity_navigation.php`. The change mainly replaced the limited `unit_actions::build()` list and the unit's addition controls. The other listed core classes, navigation and templates have no diff across the comparison. Unchanged code is not proof of absence of bugs; existing regression results provide bounded additional evidence.

Completion configuration matters. `overview::collect_completion()` includes completion-enabled materials; `first_incomplete()` selects the next among those targets. Saving a resource does not automatically establish a learning pathway. This is an existing condition, not a new chooser regression. The Page/PDF/URL comparison proves storage and hidden visibility, not a complete learner-resumption journey through those newly created resources.

### Decision and next acceptance work

Keep the standard chooser while evaluating the remaining gaps. Do not immediately restore a parallel button list, wizard or novice mode.

1. Give a teacher an outcome: add a short explanation, prepare a check of understanding and collect submitted work. Do not supply resource names or click instructions first. Observe type choice, return to the unit and understanding of publication state; record confusion between introduction and Page, difficulty finding Quiz/Assignment, and unnecessary creation.
2. Observe a new first-time participant and an experienced Moodle teacher separately. Compare with Topics using equivalent content, permissions and devices; record order effects, stops, assistance, wrong turns, actions and time. Do not reclassify this continuing participant as wholly unfamiliar.
3. In both guided and self-paced courses, use dedicated test materials with deliberately configured visibility, availability and completion. As a learner, read, follow a link, return, complete/undo and resume; prioritise iPhone. Do not publish participant drafts merely to test this.
4. If repeated confusion occurs at the purpose-to-type choice, trial a minimal explanatory aid around standard selection. A partial fixed list is not the default remedy. Avoid adding mandatory steps for experienced users.

Bound the conclusion: **core capability retention and better resource reachability have evidence. Purpose-based assistance has a real reduction; total author burden, learner benefits and expert effects still need evaluation.**

## Agreed acceptance baseline and next participant tasks

Use standard Moodle as the baseline. Improvement over an earlier Dual Learning version alone is insufficient. For equivalent users, goals and conditions, check for added unnecessary decisions, errors, dead ends, relearning and workload. Do not assume every metric can improve simultaneously. Any disadvantage must identify the affected users, cost, concrete benefit, evidence, simpler alternative and reason for acceptance. An unverified benefit does not justify a tradeoff; retain it as an open decision. Permissions, data preservation and access to standard capabilities must not be sacrificed for convenience.

Run the next participant tasks separately. First give only the goal: prepare one question to check whether students who read about Monday at 18 degrees and Tuesday at 21 degrees understand the temperature difference. From the current unit, ask which material they would choose and why, using the screen alone. Initially stop at selection; do not require detailed question configuration or publication. Then give a separate goal: collect students' written explanations of the temperature difference in their own words. Do not provide resource names or the expected button first.

Record the selected item, participant reasoning, uncertainty and assistance. Mark any task requiring instructions as assisted. This is a continuing participant, not a wholly new user. Do not claim superiority or no disadvantage relative to Topics until an equivalent baseline comparison is made. Neither task result is available at the time of this entry.


## Purpose-to-type selection: participant reports and minimal guidance

For checking understanding, the participant reported choosing Quiz. This does not establish question creation or saving. For collecting written work, the participant reported uncertainty and tentatively chose Feedback. Assignment and Online text were then explained, and the participant acknowledged understanding. The second selection required assistance. Saved activities and unsaved form contents were not inspected. Both findings are conversational self-reports, not an independent first-time-user comparison.

The selection UI is standard Moodle's, so this uncertainty alone does not establish a regression relative to Topics. Distinguish the reduction in the old Dual Learning purpose-based guidance from difficulty in the standard type choice itself. An equivalent participant comparison with Topics has not been performed.

The next minimal trial adds one sentence to the existing EN/JA examples: “To collect written work or files from students, choose Assignment.” It adds no custom button, mandatory action or standard-form change. The cost is one more sentence to read; the hypothesised benefit is clearer mapping from submission intent to activity name. A successful retry by the participant who already received the explanation cannot establish the sentence's effect. Evaluation with a new participant and of narrow-screen reading burden remains open.

The next task is an assisted evaluation of the standard Assignment form: prepare an activity that accepts typed student text, observing whether submission settings are found, other settings cause uncertainty, and saving returns to the same unit. Do not save or discard any existing Feedback form input on the participant's behalf. Publishing the activity or notifying students is outside this task.


## Release evaluation checkpoint

The participant completed the submission / teacher grading / student feedback-view cycle and reported checking it on iPhone. Desktop screenshots show the course overview changing from submitted-with-no-published-grade to current-attempt-grade-published, with a working link back to the graded assignment. The participant understood the requested improvement to their answer. Actual revision and resubmission were not performed in this session.

The participant described the pending-grade display as understandable with familiarity in smartphone operation, rather than immediately clear to everyone. Confusion between the authoring and student-practice courses was introduced by the evaluation setup and facilitator instructions; it is not attributed to the plugin. The Grade versus Submissions distinction belongs to standard Moodle, and the participant understood it after explanation.

The participant explicitly accepted the grade link's destination and asked that minor scrolling details not displace the main product goals. No anchor change is included on that basis. VoiceOver testing is postponed until after this corrective release. These are assisted continuing-participant findings, not a fresh novice or comprehensive mobile accessibility evaluation.

## Release wording follow-up

Following the request to include outstanding corrective candidates, existing English and Japanese guidance now names Quiz and Assignment by purpose, distinguishes Submissions from individual Grade on the teacher overview, and labels the current-attempt grade action as grade and feedback. No new required interaction, button or custom grading screen is introduced. The label is also used when a grade has no comments; visibility and publication checks are unchanged. The usability benefit of these wording changes remains to be evaluated.
