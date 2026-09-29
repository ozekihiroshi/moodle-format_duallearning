# First-time teacher evaluation kit

English | [日本語](AUTHORING_UX_EVALUATION.ja.md)

2026-09-29. Status: ready for facilitated evaluation; no participants have been observed. This kit tests authoring usability, not learning outcomes. Related: [milestone](AUTHORING_UX_MILESTONE.md), [H1](RESEARCH_DESIGN_HYPOTHESES.md).

## Setup for the facilitator

Use an isolated test site with fictional content, an editing-teacher account and a separate enrolled student account. Do not use production student records. Prepare two otherwise equivalent visible courses: Dual Learning with Boost, and standard Topics with Boost. Give both the same permissions, section count and available standard Page activity. Do not install LessonMark or the Dual Learning theme as requirements. Confirm the teacher can switch to the standard student role before the session; record any different site policy.

Use parallel subject examples of similar length. Alternate which format participants try first; record order and previous Moodle experience because the second task benefits from practice. Include phone and desktop use when participants are available; do not claim device coverage from one desktop browser emulation. Record a short anonymous participant label, not student data. Explain the purpose and ask whether note-taking is acceptable before starting; recording video is not required.

## Participant task (read without click instructions)

“You are preparing a short lesson about checking a data table. Make a draft unit and add one explanation. Keep unfinished work hidden from students. Check what a student would currently see, then return and change the title of that same explanation. Stop when you believe the changes are saved. Tell us what you think is happening as you work.”

Provide the explanation text in a document for copying. For the second format use a comparable subject, such as checking units in a measurement table. Do not teach the controls first or name the buttons to use. If the participant stalls, ask “What would you try next?” before offering a hint; record all assistance.

## Observation sheet (one per attempt)

| Field | Record |
| --- | --- |
| Participant / date / facilitator | |
| Moodle experience / first use of Dual Learning | |
| Device, browser, language, viewport or screen size | |
| Format, learning mode, trial order, example | |
| Site version and plugin commit / role-switch availability | |
| Start / finish / interruptions | |
| First action and places where the participant stops | |
| Hints, documentation consulted, spoken assistance | |
| Material saved and still hidden? Evidence | |
| What the participant thinks student-role checking shows | |
| Can they distinguish hidden content from missing or unsaved content? | |
| Return to the same unit and same activity? Evidence | |
| Revision saved, accidental extra activity or publication? | |
| Keyboard/focus, touch, editor and save-button problems | |
| Participant quotation / facilitator interpretation (separate) | |

Use “not observed” when a task was not attempted. If an action fails, record the visible message and what happened next. Do not substitute a developer replay for the participant result.

## Questions after the task

- What did you create, and where would you find it again?
- Can students currently see it? What on the screen led you to that conclusion?
- Does the role check establish what a particular enrolled student can access? Why?
- What would you do to edit the text again? What would cancel or going back do?
- Where did you hesitate? Which information was useful or distracting?

After the participant finishes, the facilitator verifies the saved title, activity ID and hidden state through standard Moodle controls. Use the separate test-student account to verify actual access in the fixture. Do not open a hidden draft to learners just to inspect it. Record separately what the participant inferred and what the facilitator verified.

## Review and next iteration

Classify each attempt as completed without assistance, completed with assistance, incomplete, or not observed. List publication misunderstandings and lost or duplicated work before counting clicks or comparing time. Report individual observations, including contrary findings; do not generalise percentages from a small convenience sample. Set numerical targets only after the first observations establish a baseline.

For each issue record: evidence → affected decision → proposed change → possible adverse effect → next check. Compare with Topics while noting order effects and differences in the standard editor. A successful task supports only a narrow usability finding. It does not validate cognitive-load reduction, better teaching or learner retention. Participant recruitment and observation remain outstanding; the developer walkthrough and automated tests are separate evidence.
