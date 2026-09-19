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
