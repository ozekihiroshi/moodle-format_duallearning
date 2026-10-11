# Authoring and student-role regression — 11 October 2026

The candidate retains the authoring round trip. These are developer-run regression checks, not observations of first-time teachers or evidence of learning effectiveness.

## Browser verification

The isolated lab used Moodle 5.2.2, PHP 8.3, Boost, the candidate course format, and separate comparisonteacher/comparisonstudent accounts. Original comparison courses 2–5 were not edited.

| Check | Result |
| --- | --- |
| Existing hidden unit: Continue → select unit | Editing resumed in course 8, section 14 |
| Student-role check | Hidden unit and Page absent; role-check limitations and return button shown |
| Return to teacher | Returned to the same unit and its edit link |
| Edit and save | Page name changed, saved, viewed, then restored; body unchanged |
| Empty course: start authoring | New isolated course 11; hidden unit created as section 20 |
| Add an explanation | Page 12 saved with the entered body; return link led to section 20 |
| Student-role check → return → revise | Draft remained hidden; revised Page name saved; same unit reopened |
| Independent enrolled student | No hidden draft in the course outline and no teacher return control |
| Direct Page URL as student | Moodle rejected access with “Sorry, this activity is currently hidden” |

Course 11 remains a hidden regression fixture. Its Page is named “Regression explanation — revised”. The body is “Read the table headings and identify the temperature unit.” Source-code mode in the standard editor was used for automation; this does not establish that a novice can author content without help.

## Automated checks

The workflow includes PHP 8.3/8.4 lint, Moodle coding/PHPDoc checks, plugin validation, savepoints and PHPUnit; PHP 8.3 runs all plugin Behat scenarios plus the frozen-clock student-role cache regression. JavaScript build/lint and a required compiled overview asset in the deterministic ZIP were added.

Fragment tests additionally cover context mismatch, an unenrolled user, and rejection of a client-supplied user ID as a way to reveal hidden material. The live-completion Behat outline exercises completion and undo in both learning modes without reloading.

Initial CI caught a lower-case PHP comment and the repository's broad build/ ignore rule excluding amd/build. The comment was corrected and the ignore rule was narrowed to /build/. Generated assets must be committed and match the Moodle build. No version bump, production deployment or Marketplace publication is part of this verification.
