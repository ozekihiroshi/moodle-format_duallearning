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

PHP syntax checks passed. Manual Chrome checks covered creation with editing off, clearing and restoring the outline, preserving the title and newly typed text, saving a draft, and reopening the unit list from the creation form. Added browser regressions for entry with editing off, clear/undo, preserving newly typed text, recovery from the creation form, and withholding teacher navigation/hidden drafts from a real student. CI and browser verification are in progress; do not interpret this preparation note as a pass.

Developer regression work uses a separate course. A read-only comparison confirmed the participant’s original title, introduction, hidden state and absence of separate activities were retained.

The participant report is mediated through chat, not direct screen observation. Dashboard versus course location was initially uncertain. This change cannot claim that all Moodle dashboard navigation or actual first-time usability is resolved. Repeat the task with assistance recorded. Real VoiceOver, iPhone touch and additional themes remain separate checks.

The developer browser connection became unavailable during the narrow-viewport recheck. Narrow-layout verification of this change remains incomplete.
