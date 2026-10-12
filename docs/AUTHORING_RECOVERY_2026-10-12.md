# Authoring recovery changes — 12 October 2026

The first evaluation exposed obstacles before a teacher could complete the authoring task. Reported difficulties concerned finding the creation entry, replacing a long outline, and reopening a saved draft. They are not restricted to mobile layouts.

## Changes

- Show the authoring navigation to users with course update and section visibility capabilities, including when editing is off. Keep student and switched-student views free of these controls.
- Use the same Create a unit / Open units and drafts links on the course overview, new-unit form and unit authoring pages. The list link also exists on an empty course, so returning to an older page does not remove the recovery route.
- Keep the editable outline. Add explicit Clear introduction and Undo clear controls. Clearing preserves the title and does not save. Undo will not replace text entered after clearing. With JavaScript unavailable, the standard editor remains usable and non-functional controls remain hidden.
- Name the field Unit introduction and explain its place before separate activities. Show saved introduction and draft visibility persistently on the unit page, label hidden entries in the unit list, and identify when no separate learning materials have been added.
- Put adding materials before student-role checking in the unit workflow.

No grade, submission or completion logic changes. Existing sections and participant content are retained. No schema or published-version change is included. Branch: codex/authoring-entry-recovery-20261012.

## Verification status

PHP syntax checks passed. Manual Chrome checks covered creation with editing off, clearing and restoring the outline, preserving the title and newly typed text, saving a draft, and reopening the unit list from the creation form. Added browser regressions for entry with editing off, clear/undo, preserving newly typed text, recovery from the creation form, and withholding teacher navigation/hidden drafts from a real student. CI passed for implementation/test commit `08fdb83feff22f30f8ebca97051decdf7ade2cca`: [run 38184759560](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38184759560). Moodle 5.2.4 / PHP 8.3 and 8.4 checks passed, including JavaScript build/lint, PHP checks and PHPUnit (35 tests, 340 assertions). Chrome Behat passed 10 scenarios / 196 steps; the separate same-second role-switch regression passed 2 scenarios / 52 steps. The reproducible ZIP check passed. This does not establish novice usability or real-device accessibility.

Developer regression work uses a separate course. A read-only comparison confirmed the participant’s original title, introduction, hidden state and absence of separate activities were retained.

The participant report is mediated through chat, not direct screen observation. Dashboard versus course location was initially uncertain. This change cannot claim that all Moodle dashboard navigation or actual first-time usability is resolved. Repeat the task with assistance recorded. Real VoiceOver, iPhone touch and additional themes remain separate checks.

After reconnecting Chrome, developer checks at 320 × 740 and 390 × 844 confirmed wrapped authoring links and no document-wide horizontal overflow (client/scroll widths 305/305 and 375/375). At 320px, the introduction controls and save/cancel buttons were visible; navigation from the creation form through the draft list reopened the saved unit. Expanding Saved unit introduction displayed the retained test text. The course index drawer was closed to inspect the content. Some automation commands needed state reinspection or an accessibility click before navigation completed; these are not evidence of a successful real-device touch test. The viewport override was reset. Actual iPhone and VoiceOver verification remains pending.

## Follow-up: prioritise resuming saved work

The participant could infer the purpose of Open units and drafts, but did not find the priority clear. The primary Create a unit action suggested starting again, and the link did not explain what several drafts would look like. The user chose to make resuming saved work the primary path. This is a design decision informed by one report, not a general usability finding.

When hidden top-level units exist, the shared authoring panel now lists their count, titles and primary Continue editing actions in course order. Create a unit and the complete unit list remain secondary. With no hidden units, creation remains primary. The panel lists course-level hidden units, not personal drafts; a hidden unit may also be intentionally withheld material. General and delegated subsections are excluded. No content is deleted, published, or selected automatically. Navigation also checks authoring capabilities itself.

A developer DOM inspection confirmed a named draft with the new priority. A new unit test covers no drafts, two hidden units alongside a visible unit, and student access. The browser recovery scenario targets the named resume action. The browser connection was interrupted before this revision’s narrow-width recheck; previous viewport results apply to the earlier revision. CI for this follow-up is pending.

### Separate choosing a draft from editing it

The participant’s next report showed that adding a resume button was insufficient: once inside the unit, the same resume/create controls remained above several more actions. The controls could look like a list of things that all had to be done. The unit detail page now starts with the selected unit and its saved visibility state, without the draft-selection/create panel. Plain links back to the unit list and course remain at the bottom. Material choices include a short instruction to start with one and add others later. Student-role checks and preview are inside a collapsed disclosure, retained for deliberate use. The recovery test checks the absence of entry controls and the disclosure’s behaviour; the student-role regression opens it explicitly. These changes are available in the evaluation environment; new CI results are pending.


### Final automated verification

The initial follow-up run caught the entry panel still being rendered on the detail page. The rendering scope was corrected, retaining the regression assertion. CI for `c029141` passed: [run 38187505480](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38187505480). PHP 8.3 and 8.4 checks and PHPUnit passed (36 tests, 351 assertions); browser regressions passed 10 scenarios / 205 steps, and same-second role switching passed 2 scenarios / 54 steps. Reproducible ZIP verification passed. The corrected file is deployed to the isolated evaluation environment. A read-only check confirmed the participant draft remains unchanged. Real-device usability and VoiceOver remain unverified for this revision. No published release was changed.


### Balance familiar operation and first-time guidance

The participant questioned the distinction between Continue editing and Open units and drafts. They could infer the list destination, but the extra direct-resume route introduced another choice. Following the agreed balance principle (revision 5), the course entry now offers only Open existing units and Create a new unit. Existing units take priority when any ordinary unit exists; the list includes both hidden and visible units, with visibility labels and unit-name links to editing. The list no longer repeats its own entry panel, and the creation page offers a return link to the list without repeating a create button. Standard Moodle editing routes, forms, permissions and advanced settings are retained. This trades a direct resume shortcut for one consistent list route, rather than adding a second authoring mode. No unit data is changed.

Regression tests have been updated for the two-entry structure and recovery through the named list link. CI for implementation `178ad01` passed: [run 38188662469](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38188662469). PHP 8.3/8.4 checks and PHPUnit (36 tests, 348 assertions), browser regressions (10 scenarios, 205 steps), same-second role switching (2 scenarios, 54 steps), and reproducible ZIP verification passed. A read-only lab check confirmed the original participant draft is preserved. Usability with first-time and experienced teachers remains to be evaluated; this change is not evidence that both groups now find the workflow clear.
