# Learner overview focus: issues #8 and #9

## Behavior

Progress and the next incomplete activity precede work links and assignment lists. Each list uses native HTML details/summary: up to five accessible entries are initially expanded; longer lists start collapsed. All links remain in their existing course order. This adds no author setting or JavaScript dependency.

The next-item explanation is neutral in English and Japanese. Removed the generic Lab launch hint: an LTI activity is not evidence of a Python Lab. Course authors can still provide service-specific instructions within their activity content.

## Browser inspection, 19 September 2026

Local Moodle 5.2, optional Dual Learning theme, viewport 753 x 849. A test teacher used Moodle's Student role preview; this was not a new student submission or completion test.

- Completed English self-paced Python course 44: next action visible at y=404 before 32 work links and eight assignments; both lists initially closed.
- Completed English teacher-guided Python course 45: same next-action position before 40 work links and eight assignments.
- Work links opened by click and closed with Enter. Native disclosure retains the links in the DOM.
- Lab-free teacher-guided test course 32: neutral next-item explanation, one forum shortcut and one assignment initially expanded; next action opened the intended LessonMark material.
- Teacher role restored. Empty evaluation units prepared in hidden test courses 32 and 33 for #5; no actual teacher usability evaluation performed.

Regression tests cover both modes, empty shortcut lists, one through six entries, hidden LTI/assignments, non-Python LTI tools, disclosure defaults, link order and next-action placement. Existing completion and subsection-order tests are retained. CI results are recorded in the pull request.

The earlier Marketplace screenshots show the released alpha6 layout. This branch does not change a release tag, release ZIP or Marketplace listing. Recapture overview images when publishing this behavior.
