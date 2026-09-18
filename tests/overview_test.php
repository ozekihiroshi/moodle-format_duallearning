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
        $html = $OUTPUT->render_from_template('format_duallearning/overview', $data);
        $this->assertStringNotContainsString($data['assignmentheading'], $html);
        $this->assertStringNotContainsString($data['statehint'], $html);
        $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'visible' => 0]);
        $this->assertFalse(overview::build($course, $student->id)['hasassignments']);
        $visible = $this->getDataGenerator()->create_module('assign', ['course' => $course->id, 'name' => 'Visible assignment']);
        $data = overview::build($course, $student->id);
        $this->assertTrue($data['hasassignments']);
        $this->assertCount(1, $data['assignments']);
        $this->assertSame($visible->name, $data['assignments'][0]['name']);
        $html = $OUTPUT->render_from_template('format_duallearning/overview', $data);
        $this->assertStringContainsString($data['assignmentheading'], $html);
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
        $submitted = get_string('submitted', 'format_duallearning');
        $returned = get_string('commentsreturned', 'format_duallearning');
        $this->assertSame($submitted, $readstatus());
        $DB->insert_record('assignfeedback_comments', (object) [
            'assignment' => $instance->id, 'grade' => $grade->id,
            'commenttext' => 'Please compare the two calculations.', 'commentformat' => FORMAT_PLAIN,
        ]);
        $this->assertSame($returned, $readstatus());
        $comments = $assignment->get_feedback_plugin_by_type('comments');
        $comments->set_config('enabled', 0);
        $this->assertSame($submitted, $readstatus());
        $comments->set_config('enabled', 1);
        $DB->set_field('assign', 'markingworkflow', 1, ['id' => $instance->id]);
        $flags = $assignment->get_user_flags($student->id, true);
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_INMARKING;
        $DB->update_record('assign_user_flags', $flags);
        $this->assertSame($submitted, $readstatus());
        $flags->workflowstate = ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
        $DB->update_record('assign_user_flags', $flags);
        $this->assertSame($returned, $readstatus());
        $submission->status = 'reopened';
        $DB->update_record('assign_submission', $submission);
        $this->assertSame(get_string('reopened', 'format_duallearning'), $readstatus());
        $submission->status = 'submitted';
        $submission->attemptnumber = 1;
        $DB->update_record('assign_submission', $submission);
        $this->assertSame($submitted, $readstatus());
    }
}
