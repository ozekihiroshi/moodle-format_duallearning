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

use format_duallearning\local\unit_actions;
use format_duallearning\local\unit_starter;

/**
 * Verifies authoring targets and permissions without creating activities.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(unit_actions::class)]
final class unit_actions_test extends \advanced_testcase {
    /**
     * Actions target the selected section and reading them has no write effects.
     */
    public function test_actions_target_section(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $section = unit_starter::create($course, 'Draft', '', FORMAT_HTML);
        $before = $DB->count_records('course_modules', ['course' => $course->id]);
        $actions = unit_actions::build($course, $section->id);
        $this->assertSame(['page', 'quiz', 'assign'], array_column($actions, 'module'));
        foreach ($actions as $action) {
            $url = new \moodle_url($action['url']);
            $this->assertEquals($section->id, $url->get_param('sectionid'));
            $this->assertEquals($course->id, $url->get_param('course'));
        }
        $this->assertEquals($before, $DB->count_records('course_modules', ['course' => $course->id]));
        $this->assertEquals(0, $DB->get_field('course_sections', 'visible', ['id' => $section->id]));
    }

    /**
     * A section in another course cannot be used as an insertion target.
     */
    public function test_reject_foreign_section(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $other = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $section = unit_starter::create($other, 'Other course', '', FORMAT_HTML);
        $this->expectException(\moodle_exception::class);
        unit_actions::build($course, $section->id);
    }

    /**
     * Disabled modules are not offered as available authoring choices.
     */
    public function test_disabled_module_is_not_offered(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $section = unit_starter::create($course, 'Draft', '', FORMAT_HTML);
        $DB->set_field('modules', 'visible', 0, ['name' => 'quiz']);
        \core_plugin_manager::reset_caches();
        $actions = unit_actions::build($course, $section->id);
        $this->assertNotContains('quiz', array_column($actions, 'module'));
    }

    /**
     * Learners cannot use the authoring service.
     */
    public function test_student_cannot_get_actions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $section = unit_starter::create($course, 'Draft', '', FORMAT_HTML);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $this->expectException(\required_capability_exception::class);
        unit_actions::build($course, $section->id);
    }
}
