# Authoring guide and standard editing

2026-10-15. Implemented, unreleased. A full-course simulation will evaluate usability next.

Prioritise relevant choices. Preserve access to standard Moodle features while reducing simultaneous decisions. Both views use the same section and activities; switching does not change the course format or duplicate material.

## State and actions

Use observable state. The guide shows the saved introduction, the number of materials visible to the editor, and visibility/restriction settings. Counts do not certify readiness or individual learner access.

- With no materials, prioritise adding a resource or activity.
- With materials, prioritise continuing editing; adding and checking access remain available.
- Expand a material name to reveal its actions. Keep its visibility information visible when collapsed.
- Teacher-guided prompts cover preparation, class work and review. Self-paced prompts cover explanation, practice, checking answers and getting unstuck. Neither forces activity types.
- Teachers decide whether materials are sufficient and ready to show. No automatic publication or new completion workflow is introduced.

## Switching views

The guide is an optional workspace. Use a material's Settings to edit it directly. Use the guide to review the unit as a whole, or keep working with Moodle's standard editing controls. Returning to the guide is not a prerequisite for editing or saving.

The 2026-10-15 copy review found that return instructions did not explain direct editing or continuing without returning. The existing standard-editing hint, saving/returning help and form reminder were revised in English and Japanese. Buttons, routes and permissions are unchanged. This review does not demonstrate that first-time users discover the appropriate route without help.

Open standard course editing opens the same section using core Moodle editing. It uses a POST, session key and core permission checks to enable editing. Returning to the guide does not silently disable editing.

The existing Back to unit authoring link returns from the standard section and activity forms to the same unit. It does not save a form. The existing save-first reminder remains.

## Verification and limits

Browser checks covered a populated teacher-guided unit and an empty hidden self-paced unit. A standard Page was saved from the chooser; the guide count changed from zero to one and reopening the form preserved its content. Enter expanded material actions and role checks. Switching to the student role kept the draft hidden, and returning restored the teacher role and unit.

Local integration checks covered both approaches, same-section return targets, rejection of foreign sections and student authoring access, and preservation of existing section/module records. Behat regression scenarios were added or updated. The local environment has no PHPUnit/Behat runner, so the full CI suite has not run. PHP syntax was checked.

Usability gains remain a hypothesis. The next full-course simulation should compare extra steps, discoverability of collapsed actions and long titles on narrow screens against standard editing. Physical iPhone and assistive-technology evaluation remain outstanding.
