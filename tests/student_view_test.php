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

use format_duallearning\local\student_view;

/**
 * Student-role checks must not publish content or expose authoring to students.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(student_view::class)]
final class student_view_test extends \advanced_testcase {
    /**
     * Only permitted student roles are offered; starting a check is read-only.
     */
    public function test_start_preserves_hidden_draft(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $this->setUser($teacher);
        $section = \format_duallearning\local\unit_starter::create($course, 'Hidden draft', '', FORMAT_HTML);
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        $roles = student_view::roles($course);
        $this->assertArrayHasKey($roleid, $roles);
        $this->assertArrayNotHasKey($DB->get_field('role', 'id', ['shortname' => 'editingteacher']), $roles);
        $url = student_view::start_url($course, $section->id, $roleid);
        $returnurl = new \moodle_url($url->get_param('returnurl'));
        $this->assertEquals($section->id, $returnurl->get_param('duallearningunit'));
        $this->assertEquals($roleid, $url->get_param('switchrole'));
        $this->assertFalse(is_role_switched($course->id));
        $this->assertEquals(0, $DB->get_field('course_sections', 'visible', ['id' => $section->id]));
        $course->visible = 0;
        $this->assertSame([], student_view::roles($course));
    }

    /**
     * A switched teacher can restore their role and return to the original unit.
     */
    public function test_restore_validates_unit_and_keeps_role_switched(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'numsections' => 1]);
        $other = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'numsections' => 1]);
        $section = get_fast_modinfo($course)->get_section_info(1);
        $foreign = get_fast_modinfo($other)->get_section_info(1);
        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_url('/course/view.php', ['id' => $course->id]);
        $context = \context_course::instance($course->id);
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student'], MUST_EXIST);
        role_switch($roleid, $context);
        try {
            $url = student_view::return_url($page, $section->id);
            $this->assertEquals(0, $url->get_param('switchrole'));
            $this->assertEquals($section->id, (new \moodle_url($url->get_param('returnurl')))->get_param('sectionid'));
            $foreignurl = student_view::return_url($page, $foreign->id);
            $this->assertNull((new \moodle_url($foreignurl->get_param('returnurl')))->get_param('sectionid'));
            $this->assertTrue(is_role_switched($course->id));
        } finally {
            role_switch(0, $context);
        }
    }

    /**
     * Ordinary students never receive a role-restoration or authoring action.
     */
    public function test_real_student_has_no_restore_action(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_url('/course/view.php', ['id' => $course->id]);
        $this->assertNull(student_view::return_url($page));
        $this->expectException(\required_capability_exception::class);
        student_view::roles($course);
    }

    /**
     * A missing switch-role capability is not bypassed by the authoring action.
     */
    public function test_switch_permission_is_required(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'numsections' => 1]);
        $teacher = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, 'editingteacher');
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher'], MUST_EXIST);
        assign_capability('moodle/role:switchroles', CAP_PROHIBIT, $roleid, \context_course::instance($course->id)->id);
        $this->setUser($teacher);
        $this->assertSame([], student_view::roles($course));
        $section = get_fast_modinfo($course)->get_section_info(1);
        $this->expectException(\required_capability_exception::class);
        student_view::start_url($course, $section->id, $DB->get_field('role', 'id', ['shortname' => 'student']));
    }
}
