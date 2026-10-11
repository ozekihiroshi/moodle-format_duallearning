# Student-specific access review — 11 October 2026

[日本語](ACCESS_CONDITIONS_REVIEW_2026-10-11.ja.md)

The tested routes respected each student's availability. No incorrect next-item link or cross-student completion state was observed. This is a developer-run comparison with fictional accounts, not novice-user research or evidence of learning effectiveness.

## Environment and method

The isolated localhost:8098 lab runs Moodle 5.2.4 (Build 20261005), PHP 8.3.33 and Boost. Course 19 uses Dual Learning / Self paced; course 20 uses Topics. Product code is c0a8b91, with documentation HEAD 74632cc before this review. The Moodle release/build matches the preceding CI; this does not assert identical core Git revisions. Production and the older comparison courses were untouched.

Two actual student logins were used. Student A (ID 4) belongs only to Group A, and Student B (ID 5) only to Group B. Each course has a group-A-only Page, group-B-only Page, unrestricted preparation Page, completion-dependent Page, date-restricted Page and a no-grade assignment. Group/date restrictions hide the activity completely. The dependent Page deliberately shows its name and prerequisite explanation while locked; that visible title is not an unauthorized content disclosure.

Assignment due dates are identical. Only student A receives a two-day extension. All data and accounts are fictional. Browser interaction was supplemented with read-only per-user modinfo and overview checks. The date boundary was simulated by changing only the two fixture Pages' availability timestamps to the past and rebuilding their course caches; the clock itself was not changed.

## Observations

| Check | Dual Learning / Boost | Topics / Boost |
| --- | --- | --- |
| Initial student A | Next = Group A; 0/2 manual materials; Group B and future Page absent | Same available/hidden activities |
| A completes Group A | Next changes to preparation; 1/2 | Not exercised: Topics has no next-item summary |
| A completes preparation | Next changes to dependent Page; 2/3, without manual reload; outline unlocks too | Dependent Page unlocks after the same completion action |
| A undoes preparation | Next returns to preparation; 1/2; dependent Page re-locks, without manual reload | Undo was not exercised in Topics |
| Switch to student B | Next = Group B; 0/2; A's completion and group material are not inherited | A's completed preparation does not unlock B's dependent Page |
| Direct restricted URLs | A's Group-B URL and B's future-Page URL return to course without content; B's dependent-Page URL shows prerequisite only | Direct URL cases were not repeated in Topics |
| B completes own group/preparation/dependent | Next follows own preparation and dependent Page, then available tracked work is complete (3/3) | B completion sequence was not repeated in Topics |
| Individual deadline | Overview explicitly shows base deadline, 18 Oct 09:31 Europe/London. Assignment shows A's extension to 20 Oct; B has no extension and roughly two fewer days remaining | Same individual assignment results; standard outline also shows base due date |
| Date condition becomes satisfied | After reload B gets Scheduled material as next, 3/4; link opens correct content | Scheduled material appears on B's outline |
| Return to A after B work | Next remains preparation, 1/3; prerequisite remains locked; scheduled content opens | Per-user API confirms A/B availability independently |

The API cross-check agreed with the browser. It explicitly set each user and passed that user ID to modinfo/overview. Future Pages were unavailable before the fixture date change, and available afterwards for both users in both courses. Opposite-group Pages remained inaccessible. A's Topics preparation remained complete while B's stayed incomplete.

## Design implications

**A1 — Individual deadlines take another navigation step.** The overview's base-date label is accurate, and it directs the student to the assignment for extensions. Nonetheless, the prominent date is not A's personal deadline. Consider displaying Moodle's effective individual deadline, with a clearly differentiated base-date fallback, after checking user overrides, group overrides, extension precedence and timezone behavior. The tested extension alone does not establish those cases. This is a usability improvement candidate, not a demonstrated access-control defect.

**A2 — Progress describes available work.** The denominator grows when a prerequisite or date condition unlocks more material: 1/2 to 2/3, or 3/3 to 3/4. The finished wording correctly limits itself to currently available tracked activities. Observe whether learners understand the changing denominator before adding another indicator or exposing deliberately hidden future activities.

No product fix was necessary for the tested access routes. This turn changed only isolated fixtures and documentation, so CI was not rerun. The latest product-code CI remains [successful](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38123627322). No version bump, main merge, deployment or publication was performed.

## Reproduction and remaining scope

Local ignored fixtures are in moodle-rescue/build/dual-learning-audit-20261011/comparison: access-fixture.php creates the courses and refuses duplicates; access-state.php reads both users' states, with optional open-date limited to the two fixture Pages. Do not rerun creation against retained courses. Final IDs: Dual course19 Pages37–41/assignment42; Topics course20 Pages43–47/assignment48. Date Pages remain open. A completed only Group A in Dual and preparation in Topics. B completed Group B, preparation and dependent in Dual, nothing in Topics. Neither submitted the assignments.

Untested here: actual wall-clock transition while a page stays idle, availability OR/nested trees, restricted sections, grade/profile restrictions, changing group membership mid-session, groupings, assignment group/user overrides, Teacher guided mode, mobile/keyboard/screen reader use and participant observation. The next bounded deadline review should distinguish an extension from Moodle's override mechanisms before implementing A1. Broader verification is still incomplete.
