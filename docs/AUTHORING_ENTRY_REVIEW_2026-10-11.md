# First authoring entry review

English | [日本語](AUTHORING_ENTRY_REVIEW_2026-10-11.ja.md)

2026-10-11. Developer-operated cognitive walkthrough, not a first-time participant test. Follow-up to the [authoring comparison](AUTHORING_COMPARISON_2026-10-11.md).

## Decision

**The route works; discoverability is not established.** Both formats reached the standard New Page form. Dual Learning has an observable mismatch in the empty state: it gives primary placement to “Continue building a unit” even when there is no unit to continue. This should be the first entry-point improvement to consider.

## Conditions

The isolated port-8098 site used the previous comparison's release versions plus the empty-overview candidate. Both new courses used Boost, the same editing-teacher account, English, Chrome and the same desktop window. Each started with General only, no activities and no numbered sections. Course 6 used Topics; course 7 used Dual Learning in its default Self paced mode. No LessonMark was installed.

The observer already knew Moodle and the implementation. The account had been used before; this is a fresh-course state, not a pristine first-login account. Moodle tours were encountered during the preceding student session and dismissed before teacher testing. No participant timing, success rate, keyboard-only or mobile result is reported.

## Observations

| Decision | Dual Learning | Topics |
| --- | --- | --- |
| Where to start with editing off | Course tabs and empty General; no authoring entry in the content area. Standard Edit mode switch is available. | Same basic state and Edit mode switch. |
| What appears with editing on | Continue building a unit, then Start a unit from an outline; standard General insertion and Add section also remain. | General's plus icon opens Insert content; Add section is also available. |
| What the prominent route does | Continue opens a chooser with no units and a link to start the first one. Recoverable detour, not a dead end. | Insert content offers Activity or resource and Subsection. |
| What must be understood before a Page | Unit/outline, title, editable learner-facing starter text, hidden draft; then Add an explanation. | Activity/resource categories and which type is appropriate; Page appears among 19 available types, with search/categories/help. |
| Arrival | Add an explanation opens standard New Page, with a contextual return link. | Selecting Page and Add opens standard New Page. |

**The creation action gives clear feedback.** A title was entered for the Dual Learning unit while the supplied starter text was retained. “Create hidden draft” led directly to the unit authoring screen, confirmed that the draft was hidden, and exposed explanation, practice and submission actions. No Page was saved in either course; reaching the form was the stopping point.

**Cancellation changes the screen context.** Cancelling the Dual Learning Page form returned to the standard section page. A “Back to unit authoring” link remained available there. This recovery exists, but the extra context change should be included in participant observation.

The standard activity chooser did not open on the first accessibility-driven attempt. A semantic browser click subsequently opened it in the same course. No relevant console error established a product cause. Exclude this automation interruption from usability comparisons.

## Interpretation and priorities

1. **Match the primary action to the actual course state.** With no eligible units, show the existing Start action prominently and omit Continue. Once units exist, retain Continue. Keep standard Moodle controls. This removes an irrelevant choice without introducing a new concept.
2. **Test the editing-off entry.** Both formats require discovery of Edit mode. Dual Learning currently adds no task-oriented cue before that switch. Observe whether teachers find it; consider a short contextual cue or direct authorised route only if needed. Do not add a permanent tutorial by default.
3. **Test the labels and starter text.** “Unit”, “outline” and “What learners will read” may require interpretation before someone can simply add an explanation. Ask whether the teacher thinks an outline is a prerequisite and whether unit text and Page content are distinct. These are hypotheses, not observed confusion.
4. **Preserve purpose-based choices and recovery.** Add an explanation maps a teaching intention to Page; return links preserve an editing route. Their value still needs confirmation with participants.

The routes are not equal workloads: the Dual Learning path creates a hidden unit, while the Topics demonstration adds to General. Neither fewer clicks nor faster completion follows from this exercise. A fair hidden-draft comparison must include section creation, naming and visibility in Topics.

## Next participant check

Use the existing [evaluation kit](AUTHORING_UX_EVALUATION.md). Start on an empty course with editing off. Ask: “Prepare one short explanation for a lesson. Keep unfinished work hidden.” Do not name Edit mode, Unit, Page or a button. Record the first action, hesitation, assistance, interpretation of the starter text, and whether the teacher reaches a suitable editing form. Let the participant explain what students can see. Counterbalance format order and use equivalent content. Classify the result as unaided, assisted, incomplete or not observed; do not label this developer walkthrough as an unaided participant success.

Product code was not changed during this review. Two local fixture courses were added; course 7 contains one hidden unit with example starter text for inspection. The four earlier comparison courses were not modified. Fixture setup and final state are in the ignored local laboratory as `entry-fixture.php` and `entry-state.json`.

## Implemented follow-up: state-aware entry

The candidate now shows Start as the primary authoring action when no selectable units exist. Continue is omitted in that state. After a unit exists, Continue is primary and Start is secondary. Eligibility matches the unit chooser: General and delegated sections are excluded; hidden drafts remain eligible. Editing-mode and capability checks remain in place.

Chrome verification on the isolated Boost course 8 confirmed: empty course → primary Start with no Continue → direct creation form → one hidden draft → primary Continue and secondary Start. Standard insertion controls remained available. Switching editing off hid both authoring links. PHP syntax and diff whitespace checks passed. No automated UI suite or remote CI was run for this change. No release version or production deployment was changed.

This resolves the observed empty-course detour. Discovering Edit mode and interpreting the unit/outline terminology still require participant evaluation. The local candidate mount includes this change; the retained release extraction is unchanged.

## Follow-up: text ownership and recovery

A developer walkthrough on the same local candidate checked the distinction between a unit introduction and a separate Page explanation. This did not add product changes.

| Scenario | Observed result |
| --- | --- |
| Enter a whitespace-only unit title after changing the starter text | Validation rejected the title; edited text remained in the form. |
| Correct the title and create the draft | One new hidden unit was created, with the edited introduction. |
| Enter a Page title/body and Cancel | Returned to the standard section view; unit introduction remained, no Page was saved. The return-to-authoring link was available. |
| Add an explanation again and Save and return to course | One Page was saved; the destination was the section view, with both the introduction and activity link. |
| Return to authoring and Edit the unit text | The standard section editor labelled the field Description. Saving a revised introduction left the Page body unchanged. |
| Verify final state with Moodle APIs | One Page and one newly created unit, no cancelled Page, hidden section and activity, independent student access false. |

Evidence is in the local laboratory's `check-recovery.php` and `recovery-results.json`: course 8, section 14, activity 5. Existing comparison courses were untouched. Browser command timeouts were inspected before retrying; they are automation interruptions, not participant hesitation or evidence of application failure.

**Terminology is the next evaluation question.** The unit introduction is called “What learners will read” during creation, “unit text” in the action link, and “Description” in the standard editor. “Add an explanation” creates a separate Page. The authoring screen shows the unit title but does not reproduce the saved introduction; the teacher must view or edit the unit to see it. These are observable differences. Whether they confuse a novice remains untested.

Do not collapse the two content types merely because labels differ. The introduction can orient learners while a Page holds the explanation. First ask participants to identify where their original text was saved, whether Add an explanation replaces or supplements it, and how to change only the introduction. Consider consistent naming or a short location cue if they cannot answer. Also observe the interpretation of Save and return to course, which returns to a section view in this route. Existing return links make recovery possible.

No participant study, mobile coverage, full CI or new release was completed by this walkthrough. Functional recovery passed; clarity of terminology remains an open design hypothesis.

See the subsequent [learner guidance review](LEARNER_GUIDANCE_REVIEW_2026-10-11.md) for 14 completion/visibility checks and the consolidated improvement decisions.

