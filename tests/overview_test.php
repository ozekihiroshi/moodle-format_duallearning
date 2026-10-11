<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace format_duallearning;

use format_duallearning\local\overview;

/**
 * Regression coverage for learner overview visibility.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(overview::class)]
final class overview_test extends \advanced_testcase {
    /**
     * Assignment headings and hints appear only for accessible assignments.
     */
    public function test_assignment_panel_tracks_visible_assignments(): void {
        global $OUTPUT;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $data = overview::build($course, $student->id);
        $this->assertFalse($data['hasassignments']);
        $this->assertFalse($data['hascontent']);
        $html = $OUTPUT->render_from_template('format_duallearning/overview', $data);
        $this->assertSame('', trim($html));
        $this->assertStringNotContainsString($data['assignmentheading'], $html);
        $this->assertStringNotContainsString($data['statehint'], $html);
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'visible' => 0]);
        $this->assertFalse(overview::build($course, $student->id)['hasassignments']);
        $visible = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Visible assignment']);
        $data = overview::build($course, $student->id);
        $this->assertTrue($data['hasassignments']);
        $this->assertTrue($data['hascontent']);
        $this->assertCount(1, $data['assignments']);
        $this->assertSame($visible->name, $data['assignments'][0]['name']);
        $html = $OUTPUT->render_from_template('format_duallearning/overview', $data);
        $this->assertStringContainsString($data['assignmentheading'], $html);
    }
    /**
     * Recorded submissions get status guidance without skipping or changing completion.
     */
    public function test_pending_submission_guidance(): void {
        global $CFG, $DB, $OUTPUT;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->libdir . '/completionlib.php');
        $this->resetAfterTest();
        $CFG->enablecompletion = 1;
        foreach (['path', 'guided'] as $mode) {
            $this->setAdminUser();
            $generator = $this->getDataGenerator();
            $course = $generator->create_course([
                'format' => 'duallearning', 'learningmode' => $mode, 'enablecompletion' => 1,
            ]);
            $student = $generator->create_user();
            $generator->enrol_user($student->id, $course->id, 'student');
            $instance = $generator->create_module('assign', [
                'course' => $course->id, 'grade' => 100, 'completion' => COMPLETION_TRACKING_MANUAL,
            ]);
            $page = $generator->create_module('page', [
                'course' => $course->id, 'completion' => COMPLETION_TRACKING_MANUAL,
            ]);
            $cm = get_coursemodule_from_instance('assign', $instance->id);
            $assignment = new \assign(\context_module::instance($cm->id), $cm, $course);
            $submission = $assignment->get_user_submission($student->id, true);
            $submission->status = 'submitted';
            $DB->update_record('assign_submission', $submission);
            $this->setUser($student);
            $data = overview::build($course, $student->id);
            $this->assertSame(get_string('checksubmission', 'format_duallearning'), $data['nextlabel']);
            $this->assertStringContainsString('id=' . $cm->id, $data['next']['url']);
            $hint = get_string('submissionrecordedhint', 'format_duallearning');
            $html = $OUTPUT->render_from_template('format_duallearning/overview', $data);
            $this->assertSame(1, substr_count($html, $hint));
            $this->assertSame('', $data['assignments'][0]['reviewlabel']);
            $completion = new \completion_info($course);
            $this->assertEquals(COMPLETION_INCOMPLETE, $completion->get_data($cm, false, $student->id)->completionstate);
            $completion->update_state($cm, COMPLETION_COMPLETE, $student->id);
            $data = overview::build($course, $student->id);
            $this->assertStringContainsString('id=' . $page->cmid, $data['next']['url']);
            $this->assertSame($hint, $data['assignments'][0]['pendinghint']);
            foreach (['draft', 'reopened'] as $status) {
                $submission->status = $status;
                $DB->update_record('assign_submission', $submission);
                $this->assertSame('', overview::build($course, $student->id)['assignments'][0]['pendinghint']);
            }
            $submission->status = 'submitted';
            $DB->update_record('assign_submission', $submission);
            $DB->set_field('assign', 'grade', 0, ['id' => $instance->id]);
            $this->assertSame('', overview::build($course, $student->id)['assignments'][0]['pendinghint']);
            $this->setAdminUser();
            $this->assertSame('', overview::build($course, get_admin()->id)['assignments'][0]['pendinghint']);
        }
    }

    /**
     * Due dates follow core overrides, extensions, timezone and explicit user isolation.
     */
    public function test_personal_deadlines(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['format' => 'duallearning']);
        $a = $generator->create_user(['timezone' => 'Asia/Tokyo']);
        $b = $generator->create_user(['timezone' => 'Europe/London']);
        foreach ([$a, $b] as $user) {
            $generator->enrol_user($user->id, $course->id, 'student');
        }
        $base = 1800000000;
        $instance = $generator->create_module('assign', ['course' => $course->id, 'duedate' => $base]);
        foreach ([1 => 2, 2 => 6] as $priority => $days) {
            $group = $generator->create_group(['courseid' => $course->id]);
            foreach ([$a, $b] as $user) {
                $generator->create_group_member(['groupid' => $group->id, 'userid' => $user->id]);
            }
            $DB->insert_record('assign_overrides', (object) [
                'assignid' => $instance->id, 'groupid' => $group->id,
                'sortorder' => $priority, 'duedate' => $base + $days * DAYSECS,
            ]);
        }
        $read = fn($user) => overview::build($course, $user->id);
        $this->setUser($a);
        $this->assertSame(userdate($base + 2 * DAYSECS), $read($a)['assignments'][0]['deadline']);
        $override = (object) ['assignid' => $instance->id, 'userid' => $a->id, 'duedate' => $base + DAYSECS];
        $override->id = $DB->insert_record('assign_overrides', $override);
        $this->assertSame(userdate($base + DAYSECS), $read($a)['assignments'][0]['deadline']);
        $flags = (object) ['assignment' => $instance->id, 'userid' => $a->id,
            'extensionduedate' => $base + 5 * DAYSECS];
        $flags->id = $DB->insert_record('assign_user_flags', $flags);
        $this->assertSame(userdate($base + 5 * DAYSECS), $read($a)['assignments'][0]['deadline']);
        $DB->set_field('assign_overrides', 'duedate', $base + 8 * DAYSECS, ['id' => $override->id]);
        $this->assertSame(userdate($base + 8 * DAYSECS), $read($a)['assignments'][0]['deadline']);
        $this->assertSame(get_string('personaldeadlinehint', 'format_duallearning'), $read($a)['deadlinehint']);
        // Explicit B must not inherit A's dates even before changing the session user.
        $this->assertSame(userdate($base + 2 * DAYSECS), $read($b)['assignments'][0]['deadline']);
        $this->setUser($b);
        $this->assertSame(userdate($base + 2 * DAYSECS), $read($b)['assignments'][0]['deadline']);
        $this->assertSame(1, $DB->count_records('assign_user_flags'));
        $this->setAdminUser();
        $this->assertSame(userdate($base), overview::build($course, get_admin()->id)['assignments'][0]['deadline']);
        $this->assertSame(
            get_string('deadlinehint', 'format_duallearning'),
            overview::build($course, get_admin()->id)['deadlinehint']
        );
        $this->setUser($a);
        // A null user field follows core fallback; do not independently merge group values.
        $DB->set_field('assign_overrides', 'duedate', null, ['id' => $override->id]);
        $DB->set_field('assign_user_flags', 'extensionduedate', 0, ['id' => $flags->id]);
        $this->assertSame(userdate($base), $read($a)['assignments'][0]['deadline']);
        $DB->set_field('assign_overrides', 'duedate', 0, ['id' => $override->id]);
        $this->assertSame(get_string('nodeadline', 'format_duallearning'), $read($a)['assignments'][0]['deadline']);
        $DB->set_field('assign_user_flags', 'extensionduedate', $base + DAYSECS, ['id' => $flags->id]);
        $this->assertSame(userdate($base + DAYSECS), $read($a)['assignments'][0]['deadline']);
        $DB->delete_records('assign_overrides', ['assignid' => $instance->id]);
        $DB->set_field('assign_user_flags', 'extensionduedate', 0, ['id' => $flags->id]);
        $DB->set_field('assign', 'duedate', 0, ['id' => $instance->id]);
        $this->assertSame(get_string('nodeadline', 'format_duallearning'), $read($a)['assignments'][0]['deadline']);
    }

    /**
     * File-only returns respect the current attempt and workflow in both modes.
     */
    public function test_ungraded_file_feedback_returned(): void {
        global $DB, $CFG, $OUTPUT;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();
        foreach (['path', 'guided'] as $mode) {
            $this->setAdminUser();
            $generator = $this->getDataGenerator();
            $course = $generator->create_course(['format' => 'duallearning', 'learningmode' => $mode]);
            $student = $generator->create_user();
            $generator->enrol_user($student->id, $course->id, 'student');
            $instance = $generator->create_module('assign', [
                'course' => $course->id, 'grade' => 0, 'markingworkflow' => 1,
                'assignfeedback_file_enabled' => 1, 'assignfeedback_comments_enabled' => 1,
            ]);
            $cm = get_coursemodule_from_instance('assign', $instance->id);
            $context = \context_module::instance($cm->id);
            $assignment = new \assign($context, $cm, $course);
            $read = fn() => overview::build($course, $student->id)['assignments'][0];
            $this->setUser($student);
            $before = $DB->count_records('assign_submission');
            $this->assertSame('', $read()['reviewlabel']);
            $this->assertSame($before, $DB->count_records('assign_submission'));
            $submission = $assignment->get_user_submission($student->id, true);
            $submission->status = 'submitted';
            $DB->update_record('assign_submission', $submission);
            $flags = $assignment->get_user_flags($student->id, true);
            $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
            $DB->update_record('assign_user_flags', $flags);
            $before = $DB->count_records('assign_grades');
            $this->assertSame('', $read()['reviewlabel']);
            $this->assertSame($before, $DB->count_records('assign_grades'));
            $grade = $assignment->get_user_grade($student->id, true, 0);
            $this->assertSame('', $read()['reviewlabel']);
            $file = [
                'contextid' => $context->id, 'component' => 'assignfeedback_file', 'filearea' => 'feedback_files',
                'itemid' => $grade->id, 'filepath' => '/', 'filename' => 'private-response.txt',
            ];
            get_file_storage()->create_file_from_string($file, 'Private response content');
            $label = get_string('reviewattemptfeedback', 'format_duallearning');
            $this->assertSame($label, $read()['reviewlabel']);
            $this->assertSame(get_string('feedbackreturned', 'format_duallearning'), $read()['status']);
            $this->assertSame('', $read()['pendinghint']);
            $this->assertStringContainsString('/mod/assign/view.php?id=' . $cm->id, $read()['url']);
            $html = $OUTPUT->render_from_template('format_duallearning/overview', overview::build($course, $student->id));
            $this->assertStringContainsString($label, $html);
            $this->assertStringNotContainsString('private-response.txt', $html);
            $this->assertStringNotContainsString('Private response content', $html);
            $this->assertStringNotContainsString('pluginfile.php', $html);
            foreach ([ASSIGN_MARKING_WORKFLOW_STATE_INMARKING, ASSIGN_MARKING_WORKFLOW_STATE_READYFORRELEASE] as $state) {
                $flags->workflowstate = $state;
                $DB->update_record('assign_user_flags', $flags);
                $this->assertSame('', $read()['reviewlabel']);
                $this->assertSame(get_string('submittedungraded', 'format_duallearning'), $read()['status']);
            }
            $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
            $DB->update_record('assign_user_flags', $flags);
            $this->assertSame($label, $read()['reviewlabel']);
            $plugin = $assignment->get_feedback_plugin_by_type('file');
            $plugin->set_config('enabled', 0);
            $this->assertSame('', $read()['reviewlabel']);
            $DB->insert_record('assignfeedback_comments', (object) [
                'assignment' => $instance->id, 'grade' => $grade->id,
                'commenttext' => 'Returned comments', 'commentformat' => FORMAT_PLAIN,
            ]);
            $this->assertSame(get_string('reviewattemptcomments', 'format_duallearning'), $read()['reviewlabel']);
            $plugin->set_config('enabled', 1);
            $this->assertSame($label, $read()['reviewlabel']);
            $assignment->get_feedback_plugin_by_type('comments')->set_config('enabled', 0);
            $this->assertSame($label, $read()['reviewlabel']);
            foreach (['draft', 'reopened'] as $status) {
                $submission->status = $status;
                $DB->update_record('assign_submission', $submission);
                $this->assertSame('', $read()['reviewlabel']);
            }
            $submission->latest = 0;
            $DB->update_record('assign_submission', $submission);
            $second = $assignment->get_user_submission($student->id, true, 1);
            $second->status = 'submitted';
            $DB->update_record('assign_submission', $second);
            $this->assertSame('', $read()['reviewlabel']);
            $secondgrade = $assignment->get_user_grade($student->id, true, 1);
            $this->assertSame('', $read()['reviewlabel']);
            $file['itemid'] = $secondgrade->id;
            get_file_storage()->create_file_from_string($file, 'Current response');
            $this->assertSame($label, $read()['reviewlabel']);
            $DB->set_field('assign', 'markingworkflow', 0, ['id' => $instance->id]);
            $this->assertSame($label, $read()['reviewlabel']);
            // Files must not bypass the existing grade visibility rules for scored assignments.
            $DB->set_field('assign', 'grade', 100, ['id' => $instance->id]);
            $this->assertSame('', $read()['reviewlabel']);
            $this->setAdminUser();
            $this->assertSame('', overview::build($course, get_admin()->id)['assignments'][0]['reviewlabel']);
        }
    }


    /**
     * Ungraded comments respect release, attempt, and plugin visibility.
     */
    public function test_ungraded_comments_returned(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $instance = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id, 'grade' => 0, 'assignfeedback_comments_enabled' => 1,
        ]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $assignment = new \assign(\context_module::instance($cm->id), $cm, $course);
        $this->setUser($student);
        $before = $DB->count_records('assign_submission');
        overview::build($course, $student->id);
        $this->assertEquals($before, $DB->count_records('assign_submission'));
        $submission = $assignment->get_user_submission($student->id, true);
        $submission->status = 'submitted';
        $DB->update_record('assign_submission', $submission);
        $grade = $assignment->get_user_grade($student->id, true, 0);
        $readstatus = fn() => overview::build($course, $student->id)['assignments'][0]['status'];
        $review = fn() => overview::build($course, $student->id)['assignments'][0]['reviewlabel'];
        $submitted = get_string('submittedungraded', 'format_duallearning');
        $returned = get_string('commentsreturned', 'format_duallearning');
        $this->assertSame($submitted, $readstatus());
        $this->assertSame('', $review());
        $submission->status = 'draft';
        $DB->update_record('assign_submission', $submission);
        $this->assertSame(get_string('draft', 'format_duallearning'), $readstatus());
        $submission->status = 'submitted';
        $DB->update_record('assign_submission', $submission);
        $DB->insert_record('assignfeedback_comments', (object) [
            'assignment' => $instance->id, 'grade' => $grade->id,
            'commenttext' => 'Please compare the two calculations.', 'commentformat' => FORMAT_PLAIN,
        ]);
        $this->assertSame($returned, $readstatus());
        $html = $GLOBALS['OUTPUT']->render_from_template('format_duallearning/overview', overview::build($course, $student->id));
        $this->assertStringContainsString('btn btn-outline-secondary', $html);
        $this->assertStringContainsString('visually-hidden', $html);
        $this->assertSame(get_string('reviewattemptcomments', 'format_duallearning'), $review());
        $comments = $assignment->get_feedback_plugin_by_type('comments');
        $comments->set_config('enabled', 0);
        $this->assertSame($submitted, $readstatus());
        $this->assertSame('', $review());
        $comments->set_config('enabled', 1);
        $DB->set_field('assign', 'markingworkflow', 1, ['id' => $instance->id]);
        $flags = $assignment->get_user_flags($student->id, true);
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INMARKING;
        $DB->update_record('assign_user_flags', $flags);
        $this->assertSame($submitted, $readstatus());
        $this->assertSame('', $review());
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
        $DB->update_record('assign_user_flags', $flags);
        $this->assertSame($returned, $readstatus());
        $this->assertSame(get_string('reviewattemptcomments', 'format_duallearning'), $review());
        $submission->status = 'reopened';
        $DB->update_record('assign_submission', $submission);
        $this->assertSame(get_string('reopened', 'format_duallearning'), $readstatus());
        $submission->status = 'submitted';
        $submission->attemptnumber = 1;
        $DB->update_record('assign_submission', $submission);
        $this->assertSame($submitted, $readstatus());
        $this->assertSame('', $review());
    }
    /**
     * Retained grades must not imply that a new attempt has been graded.
     */
    public function test_grade_publication_is_specific_to_current_attempt(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        require_once($CFG->libdir . '/gradelib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $instance = $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id, 'grade' => 100, 'markingworkflow' => 1,
        ]);
        $cm = get_coursemodule_from_instance('assign', $instance->id);
        $assignment = new \assign(\context_module::instance($cm->id), $cm, $course);
        $submission = $assignment->get_user_submission($student->id, true);
        $submission->status = 'submitted';
        $DB->update_record('assign_submission', $submission);
        $firstgrade = $assignment->get_user_grade($student->id, true, 0);
        $DB->set_field('assign_grades', 'grade', 75, ['id' => $firstgrade->id]);
        $flags = $assignment->get_user_flags($student->id, true);
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
        $DB->update_record('assign_user_flags', $flags);
        grade_update(
            'mod/assign',
            $course->id,
            'mod',
            'assign',
            $instance->id,
            0,
            (object) ['userid' => $student->id, 'rawgrade' => 75]
        );
        $this->setUser($student);
        $status = fn() => overview::build($course, $student->id)['assignments'][0]['status'];
        $review = fn() => overview::build($course, $student->id)['assignments'][0]['reviewlabel'];
        $published = get_string('graded', 'format_duallearning');
        $pending = get_string('submitted', 'format_duallearning');
        $this->assertSame($published, $status());
        $this->assertSame(get_string('reviewattemptgrade', 'format_duallearning'), $review());
        $submission->attemptnumber = 1;
        $DB->update_record('assign_submission', $submission);
        $before = $DB->count_records('assign_grades');
        $this->assertSame($pending, $status());
        $this->assertSame('', $review());
        $this->assertSame($before, $DB->count_records('assign_grades'));
        $secondgrade = $assignment->get_user_grade($student->id, true, 1);
        $this->assertSame($pending, $status());
        $this->assertSame('', $review());
        $DB->set_field('assign', 'markingworkflow', 0, ['id' => $instance->id]);
        $this->assertSame($pending, $status());
        $this->assertSame('', $review());
        // A gradebook override does not establish grading of the current attempt.
        $item = \grade_item::fetch(['itemtype' => 'mod', 'itemmodule' => 'assign', 'iteminstance' => $instance->id]);
        $item->update_final_grade($student->id, 80, 'test');
        $this->assertSame($pending, $status());
        $this->assertSame('', $review());
        $DB->set_field('assign_grades', 'grade', 0, ['id' => $secondgrade->id]);
        $this->assertSame($published, $status());
        $this->assertSame(get_string('reviewattemptgrade', 'format_duallearning'), $review());
        $DB->set_field('assign', 'markingworkflow', 1, ['id' => $instance->id]);
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INMARKING;
        $DB->update_record('assign_user_flags', $flags);
        $this->assertSame($pending, $status());
        $this->assertSame('', $review());
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
        $DB->update_record('assign_user_flags', $flags);
        $this->assertSame($published, $status());
        $this->assertSame(get_string('reviewattemptgrade', 'format_duallearning'), $review());
        $item->set_hidden(1);
        $this->assertSame($pending, $status());
        $this->assertSame('', $review());
        $item->set_hidden(0);
        $this->assertSame($published, $status());
        $this->assertSame(get_string('reviewattemptgrade', 'format_duallearning'), $review());
    }
    /**
     * Next activity follows moved subsections and keeps visibility restrictions.
     */
    public function test_next_activity_follows_subsections(): void {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/completionlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablecompletion = 1;
        foreach (['path', 'guided'] as $mode) {
            $course = $this->getDataGenerator()->create_course([
                'format' => 'duallearning', 'learningmode' => $mode, 'enablecompletion' => 1,
            ]);
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
            $page = fn($name, $section) => $this->getDataGenerator()->create_module('page', [
                'course' => $course->id, 'name' => $name, 'section' => $section,
                'completion' => COMPLETION_TRACKING_MANUAL,
            ]);
            $first = $page('First', 1);
            $last = $page('Last', 1);
            $suba = $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);
            $subb = $this->getDataGenerator()->create_module('subsection', ['course' => $course->id, 'section' => 1]);
            $modinfo = get_fast_modinfo($course);
            $a = $page('Inside A', $modinfo->get_cm($suba->cmid)->get_delegated_section_info()->sectionnum);
            $b = $page('Inside B', $modinfo->get_cm($subb->cmid)->get_delegated_section_info()->sectionnum);
            $actions = \core_courseformat\formatactions::cm($course);
            $actions->move_before($suba->cmid, $last->cmid);
            $actions->move_before($subb->cmid, $last->cmid);
            $this->setUser($student);
            $next = fn() => overview::build($course, $student->id)['next']['name'];
            $this->assertSame('First', $next());
            $completion = new \completion_info($course);
            $completion->update_state(get_fast_modinfo($course)->get_cm($first->cmid), COMPLETION_COMPLETE, $student->id);
            $this->assertSame('Inside A', $next());
            $this->setAdminUser();
            $actions->move_before($subb->cmid, $suba->cmid);
            $this->setUser($student);
            $this->assertSame('Inside B', $next());
            $completion->update_state(get_fast_modinfo($course)->get_cm($b->cmid), COMPLETION_COMPLETE, $student->id);
            $this->assertSame('Inside A', $next());
            $this->setAdminUser();
            set_coursemodule_visible($suba->cmid, 0);
            $this->setUser($student);
            $this->assertSame('Last', $next());
            $this->setAdminUser();
            set_coursemodule_visible($suba->cmid, 1);
            $DB->set_field('course_modules', 'availability', json_encode([
                'op' => '&', 'c' => [['type' => 'date', 'd' => '>=', 't' => time() + 86400]], 'showc' => [false],
            ]), ['id' => $suba->cmid]);
            rebuild_course_cache($course->id, true);
            $this->setUser($student);
            $this->assertSame('Last', $next());
            $DB->set_field('course_modules', 'availability', null, ['id' => $suba->cmid]);
            $DB->set_field('course_modules', 'deletioninprogress', 1, ['id' => $a->cmid]);
            rebuild_course_cache($course->id, true);
            $this->assertSame('Last', $next());
            $this->setAdminUser();
        }
    }

    /**
     * Flat courses keep completion-based navigation and the finished state.
     */
    public function test_flat_course_completion_order(): void {
        global $CFG;
        require_once($CFG->libdir . '/completionlib.php');
        $this->resetAfterTest();
        $CFG->enablecompletion = 1;
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'enablecompletion' => 1]);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $pages = [];
        foreach (['Start', 'End'] as $name) {
            $pages[] = $this->getDataGenerator()->create_module('page', [
                'course' => $course->id, 'section' => 1, 'name' => $name, 'completion' => COMPLETION_TRACKING_MANUAL,
            ]);
        }
        $this->setUser($student);
        $completion = new \completion_info($course);
        foreach ($pages as $page) {
            $this->assertSame($page->name, overview::build($course, $student->id)['next']['name']);
            $completion->update_state(get_fast_modinfo($course)->get_cm($page->cmid), COMPLETION_COMPLETE, $student->id);
        }
        $data = overview::build($course, $student->id);
        $this->assertNull($data['next']);
        $this->assertTrue($data['finished']);
    }
    /**
     * Long lists never precede the next action, and preserve every visible link.
     */
    public function test_overview_discloses_long_lists_without_lab_assumptions(): void {
        global $CFG, $OUTPUT;
        require_once($CFG->libdir . '/completionlib.php');
        $this->resetAfterTest();
        $CFG->enablecompletion = 1;
        foreach (['path', 'guided'] as $mode) {
            $this->setAdminUser();
            $course = $this->getDataGenerator()->create_course([
                'format' => 'duallearning', 'learningmode' => $mode, 'enablecompletion' => 1,
            ]);
            $student = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
            $this->getDataGenerator()->create_module('page', [
                'course' => $course->id, 'name' => 'Read first', 'completion' => COMPLETION_TRACKING_MANUAL,
            ]);
            $this->setUser($student);
            $empty = overview::build($course, $student->id);
            $this->assertFalse($empty['hasshortcuts']);
            $html = $OUTPUT->render_from_template('format_duallearning/overview', $empty);
            $this->assertStringNotContainsString('Lab', $html);
            $this->assertStringNotContainsString('<details', $html);
            for ($i = 1; $i <= 6; $i++) {
                $this->setAdminUser();
                $this->getDataGenerator()->create_module('lti', [
                    'course' => $course->id, 'name' => 'External reading ' . $i,
                    'toolurl' => 'https://example.org/reading',
                ]);
                $this->getDataGenerator()->create_module('assign', [
                    'course' => $course->id, 'name' => 'Submit reflection ' . $i,
                ]);
                $this->setUser($student);
                $data = overview::build($course, $student->id);
                $this->assertSame($i > 5, $data['collapseshortcuts']);
                $this->assertSame($i > 5, $data['collapseassignments']);
            }
            $this->setAdminUser();
            $this->getDataGenerator()->create_module('lti', [
                'course' => $course->id, 'name' => 'Hidden external reading', 'visible' => 0,
            ]);
            $this->getDataGenerator()->create_module('assign', [
                'course' => $course->id, 'name' => 'Hidden submission', 'visible' => 0,
            ]);
            $this->setUser($student);
            $data = overview::build($course, $student->id);
            $this->assertSame('Read first', $data['next']['name']);
            $this->assertCount(6, $data['shortcuts']);
            $this->assertCount(6, $data['assignments']);
            $html = $OUTPUT->render_from_template('format_duallearning/overview', $data);
            $dom = new \DOMDocument();
            @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
            $xpath = new \DOMXPath($dom);
            $this->assertSame(2, $xpath->query('//details[not(@open)]')->length);
            $this->assertSame(0, $xpath->query('//details/following-sibling::p/a')->length);
            $this->assertSame(1, $xpath->query('//details[1]/preceding-sibling::p/a')->length);
            $this->assertStringNotContainsString('Lab', $html);
            $this->assertStringNotContainsString('Hidden external reading', $html);
            $this->assertStringNotContainsString('Hidden submission', $html);
            foreach ($data['shortcuts'] as $index => $shortcut) {
                $this->assertSame('External reading ' . ($index + 1), $shortcut['name']);
                $this->assertStringContainsString($shortcut['name'], $html);
            }
        }
    }
}
