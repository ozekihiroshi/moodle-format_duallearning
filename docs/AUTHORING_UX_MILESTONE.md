# Next milestone: understanding through authoring

English | [日本語](AUTHORING_UX_MILESTONE.ja.md)

2026-09-29. Document revision: 3. Status: development direction and evaluation plan. The proposed interface and acceptance criteria describe future work, not implemented features or demonstrated benefits.

## 1. Intended experience

**Touch first, understand as you go. — 触れば分かる。作りながら覚える。**

Help teachers concentrate on creating lessons rather than learning Moodle. Let the relationship between an action and its result explain what matters at that moment, without requiring a manual or a long introduction before work can begin.

This is not a plan to add game decoration, points, or badges. It is a direction for reducing cognitive load through fewer initial choices, immediate feedback, recoverable actions, and progressive disclosure. It includes keyboard users, who must have access to the same meanings and actions as touch users.

## 2. Relationship to existing specifications

Keep learning as the ultimate purpose: reduce operational effort while preserving the thinking required for learning. Apply "Keep learning as the purpose" and the change-review record in the [shared development principles](DEVELOPMENT_PRINCIPLES.md) to this milestone. Making the authoring loop easier does not prove improved learning outcomes.

Retain the independence and responsibility boundaries in the [shared development principles](DEVELOPMENT_PRINCIPLES.md), and SA-01–08 in the [simple authoring specification](SIMPLE_AUTHORING_SPEC.ja.md). This document defines the next evaluation and improvement priorities. It does not reinstate long design forms or mandatory wizards.

Existing specifications and records describe hidden unit drafts, entry points for materials, practice and submissions, return paths, and optional hints. However, the [19 September walkthrough](DEVELOPER_AUTHORING_CHECK_2026-09-19.md) was operated by a developer and is not evidence of usability for a teacher encountering the interface for the first time. Recheck the current code and interface before implementation.

Existing entry points do not establish that course-mode selection, standard activity-form complexity, or student-view checking are solved. Retain existing learning-design requirements, preservation of standard data, and preparation and revision in both learning modes.

## 3. Target flow and proposed actions

Create a course → choose Teacher guided / Self paced → choose what to prepare first → see the result → add what is needed → check the student view → return to editing or publish when ready.

This describes an experience, not a sequence every user must complete on every visit. Returning teachers should reach their current unit and existing materials directly. Keep the mode at course level and normally preserve it; do not ask teachers to choose it again whenever they open authoring support.

| Situation | Actions and information to expose | What the result explains |
| --- | --- | --- |
| Choose the learning mode | Briefly contrast teacher-supported classroom progress with progression through materials at the learner's own pace | Both support questions, preparation, revision, and teacher grading. Changing mode does not rewrite materials |
| Begin creating | Ask what to prepare first, with four candidates: materials, practice, submissions, and a place for questions | Not everything is required; existing materials can also be reused |
| Create content | Inputs relevant to the chosen purpose, with a short contextual example | What is being created, without first learning Moodle's internal terminology |
| Save | Show the saved item, its location and visibility, with Edit and Check student view actions | Whether saving succeeded, what was added, and whether students can see it |
| Check and continue | Return to the original unit from preview, with optional further actions | How to revise or continue without being forced to add more |

Four candidates are a starting proposal, not a proven optimum. Materials must have a route through standard Page, Book, or File resources. LessonMark can be an additional option when available, without removing the basic path when absent. Consider access to a place for questions in both learning modes.

## 4. Interface requirements

- Make primary actions discoverable without expanding instructions. Use short action labels in English and Japanese rather than relying on icons alone.
- After saving, show the actual change in the same working context. Distinguish processing, failure, and unsaved work from success; make feedback available to keyboard and assistive-technology users.
- Reveal details when needed, but keep information essential to the current decision visible: visibility, deadlines, submission method, and grading conditions. Do not silently apply consequential defaults.
- Offer brief guidance and direct access to existing materials and standard settings in the same interface. Do not require users to declare their expertise or complete a tutorial repeatedly.
- Distinguish Back, Cancel, and Undo. Cancelling before saving must not create content. Re-editing saved content and deleting it are separate actions. Do not promise unconditional Undo for operations involving submissions or grades.
- Before deletion, publication, or changes affecting existing records, show the target and consequence. More confirmation dialogs alone do not establish safety.
- Distinguish student preview from verification of an actual student's permissions. Do not imply that role switching reproduces all enrolment, group, availability, and completion conditions. Do not publish production materials merely to preview them.

## 5. Narrow the first implementation candidate

Required first step: connect existing research to design hypotheses using the [bilingual evidence register](RESEARCH_DESIGN_HYPOTHESES.md). Record the finding, transfer limits, hypothesis, possible harms and observation plan before implementation. H1 (saved content, settings and actions together) is the first prototype; H2 (retrieval) and H3 (revisiting) are later candidates, not bundled requirements.

The first target is a complete loop: **a teacher encountering the interface for the first time creates one material in a hidden unit, checks its student presentation, and returns to edit that same material without consulting a manual.**

Walk through the existing interface first and record where uncertainty occurs. Select the most significant obstacle and create an operable prototype addressing it. Do not redesign all four activity forms and course creation simultaneously. Start with mode selection if that blocks the first step, or the destination after saving if that is the obstacle.

Keep Moodle authoritative for sections, activities, and permissions. A link to a standard form does not prove that cognitive load has been reduced. If a simplified form is needed, first establish how it will preserve required settings, validation, and permissions. The theme owns appearance, LessonMark owns functionality within lessons, and the course format owns the authoring flow.

## 6. Observation and adoption

Give teachers a task, not click instructions: prepare a short material, check how it appears to students, and make one revision. Distinguish observation of actual first-time teachers from developer walkthroughs. If participants have not been recruited, record human evaluation as outstanding.

| Observation | Evidence to record |
| --- | --- |
| Can the teacher find the first action? | First choice, stopping points, and assistance required |
| Can the teacher understand the result? | Their explanation of what was created, its location, and its saved and published state |
| Can the teacher revise and return? | Return to the same material; confusion between cancellation, re-editing, and deletion |
| Is dependence on instructions reduced? | Research, hint use, spoken assistance, rework, and elapsed time |
| Is repeat use straightforward? | Whether the same participant can directly re-edit and add content |

Compare against standard Topics with equivalent content and conditions. Record experience, device, trial order, and practice effects. Do not use click count as the sole success measure; prioritise misunderstandings about publication and actions that lose data.

Before adoption, observe completion of the loop, understanding of the result and visibility, and return to the original work. Do not generalise small-sample findings into universal improvement rates. Set measurable targets after initial observation and do not advertise unmeasured time savings or learning outcomes.

For the affected scope, verify independent operation with Boost and standard activities, optional combinations with LessonMark and the Dual Learning theme, English and Japanese, phones and desktop screens, keyboard access, and teacher and student permissions.

For each prototype, record the operational burden reduced, the intended learning behaviour, the hypothesised connection, potential adverse effects, and what can currently be checked. For example, making it easier to return to a material after saving reduces revision effort; it does not guarantee more updates or better content.

While observation of actual teachers and learners is unavailable, use developer walkthroughs and design reviews as preliminary evaluation, and record human usability evaluation and learning-outcome validation as outstanding. Prototyping can proceed, but these checks cannot substitute for an adoption decision claiming demonstrated effectiveness in actual use.

The next deliverables are a record of the current loop (identified as actual use or preliminary review), one prioritised point of confusion, the smallest prototype addressing it with a learning hypothesis, and comparative findings, unverified questions, and an adoption decision. The first local H1 prototype now groups saved materials, visibility settings and review/edit actions. Student-view verification and novice evaluation remain outstanding. Published releases and AWS are unchanged.

Local prototype record: [H1 review and remaining work](AUTHORING_UX_H1_REVIEW.md).

First-time teacher evaluation: [task and observation kit](AUTHORING_UX_EVALUATION.md). Participant observation has not taken place.
