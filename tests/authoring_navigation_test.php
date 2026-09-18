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

use format_duallearning\local\authoring_navigation;
use format_duallearning\local\unit_actions;

/**
 * Checks authoring navigation isolation and module-level editing permissions.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(authoring_navigation::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(unit_actions::class)]
final class authoring_navigation_test extends \advanced_testcase {
    /**
     * Section and activity pages resolve the same stable unit after moving content.
     */
    public function test_return_routes_follow_current_section(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'numsections' => 2]);
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'section' => 1]);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $page = new \moodle_page();
        $page->set_cm($cm, $course);
        $page->set_url('/mod/page/view.php', ['id' => $cm->id]);
        $this->assertEquals($cm->section, authoring_navigation::target($page)->get_param('sectionid'));

        $destination = get_fast_modinfo($course)->get_section_info(2);
        \core_courseformat\formatactions::cm($course)->move_end_section($cm->id, $destination->id);
        $moved = get_fast_modinfo($course)->get_cm($activity->cmid);
        $page = new \moodle_page();
        $page->set_cm($moved, $course);
        $page->set_url('/mod/page/view.php', ['id' => $moved->id]);
        $this->assertEquals($destination->id, authoring_navigation::target($page)->get_param('sectionid'));
        $this->assertEmpty(unit_actions::existing($course, $cm->section));
        $this->assertCount(1, unit_actions::existing($course, $destination->id));

        $sectionpage = new \moodle_page();
        $sectionpage->set_course($course);
        $sectionpage->set_url('/course/section.php', ['id' => $destination->id]);
        $this->assertEquals($destination->id, authoring_navigation::target($sectionpage)->get_param('sectionid'));
        $sectionpage->set_url('/course/modedit.php', ['course' => $course->id, 'section' => 2, 'add' => 'page']);
        $this->assertEquals($destination->id, authoring_navigation::target($sectionpage)->get_param('sectionid'));
    }

    /**
     * Foreign and general sections and unrelated course formats get no return link.
     */
    public function test_reject_unrelated_targets(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $other = $this->getDataGenerator()->create_course(['format' => 'topics']);
        $foreign = get_fast_modinfo($other)->get_section_info(0);
        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_url('/course/section.php', ['id' => $foreign->id]);
        $this->assertNull(authoring_navigation::target($page));
        $general = get_fast_modinfo($course)->get_section_info(0);
        $page->set_url('/course/section.php', ['id' => $general->id]);
        $this->assertNull(authoring_navigation::target($page));
        $page->set_course($other);
        $this->assertNull(authoring_navigation::target($page));
    }

    /**
     * Student pages do not disclose authoring controls.
     */
    public function test_student_has_no_authoring_route(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course->id, 'section' => 1]);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $page = new \moodle_page();
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $page->set_cm($cm, $course);
        $page->set_url('/mod/page/view.php', ['id' => $cm->id]);
        $this->assertNull(authoring_navigation::target($page));
        $this->expectException(\required_capability_exception::class);
        unit_actions::existing($course, $cm->section);
    }

    /**
     * Module overrides suppress editing without hiding allowed viewing links.
     */
    public function test_module_edit_permissions(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $quiz = $this->getDataGenerator()->create_module('quiz', ['course' => $course->id, 'section' => 1]);
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $role = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        $context = \context_module::instance($quiz->cmid);
        $this->setUser($teacher);
        $cm = get_fast_modinfo($course)->get_cm($quiz->cmid);
        $items = unit_actions::existing($course, $cm->section);
        $this->assertNotNull($items[0]['editurl']);
        $this->assertNotNull($items[0]['questionsurl']);
        assign_capability('moodle/course:manageactivities', CAP_PROHIBIT, $role, $context->id);
        assign_capability('mod/quiz:manage', CAP_PROHIBIT, $role, $context->id);
        accesslib_clear_all_caches_for_unit_testing();
        $items = unit_actions::existing($course, $cm->section);
        $this->assertCount(1, $items);
        $this->assertNull($items[0]['editurl']);
        $this->assertNull($items[0]['questionsurl']);
        $this->assertNotNull($items[0]['viewurl']);
    }
}
