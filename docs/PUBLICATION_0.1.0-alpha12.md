# Alpha12 publication / alpha12公開記録

Release commit: `a120a9be883875322806ae61050aab9ea199a265`.

- Component: `format_duallearning`; release: `0.1.0-alpha12`; build: `2026101200`; maturity: Alpha; supported Moodle: 5.2.
- Main was fast-forwarded to the tested candidate and annotated tag `v0.1.0-alpha12` was pushed atomically with main.
- [GitHub prerelease](https://github.com/ozekihiroshi/moodle-format_duallearning/releases/tag/v0.1.0-alpha12).
- [Successful candidate CI](https://github.com/ozekihiroshi/moodle-format_duallearning/actions/runs/38201628068): PHP 8.3 and 8.4 each passed 36 tests / 344 assertions, static checks and JavaScript build/lint. Browser regressions passed 12 scenarios / 257 steps plus 2 same-second role-switch scenarios / 54 steps. ZIP reproducibility passed.
- ZIP: `moodle-format_duallearning-0.1.0-alpha12.zip`, 1,541,416 bytes.
- SHA-256: `7978d0174cf16fa547419157dc76c9b213aa66ef5ad923822f2b229717ce5e81`.
- The distributed ZIP is the CI artifact; GitHub reports the same digest. Compiled JavaScript matches the CI rebuild. The initial local archive differed only in line endings in the preceding release's publication record; the CI artifact was selected for both destinations.
- The isolated Moodle 5.2.4 upgrade succeeded. Counts and hashes of saved course sections, modules, Page/URL materials, assignments, submissions, online text, grades, feedback comments and completion records matched before and after.
- The release workflow's hardcoded alpha11 ZIP path was corrected to use the builder's current-version output after CI detected the mismatch.

今回の修正は、保存済み単元・下書きへの復帰、紹介文の消去と取り消し、標準教材選択の復元、用途と提出・評点確認の案内を対象とする。標準Moodleの採点画面や評点リンクのスクロール位置は変更していない。学生による提出から教師の採点、学生のフィードバック確認と改善点の理解までを、支援ありの継続参加者で確認した。iPhoneでの確認は本人の報告、保存状態の画像証拠はPC画面として区別する。初見・熟練者の比較やVoiceOver検証、学習効果の実証は未完了である。

This is a corrective alpha release, not a claim of comprehensive accessibility or learning-effectiveness validation. No theme release or production-server deployment is part of this publication.

## Marketplace confirmation

Published on 12 October 2026. [Moodle Marketplace](https://marketplace.moodle.com/plugins/format_duallearning) version ID `28596` shows Automated test passed. The public Download link points to `/plugins/4223/versions/28596/download`, and the latest release date is 12 October 2026. Supported version is Moodle 5.2 only. English release notes and the product description were updated to match the implemented authoring flow and evaluation limits.

These publication records were committed after tagging; the release ZIP and tag remain unchanged.
