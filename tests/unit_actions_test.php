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
     * Hidden containers must not be mistaken for learner access to a shown activity.
     */
    public function test_existing_reports_settings_without_publishing(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'visible' => 0]);
        $section = unit_starter::create($course, 'Private draft', '', FORMAT_HTML);
        $activity = $this->getDataGenerator()->create_module('page', [
            'course' => $course->id, 'section' => $section->section,
        ]);
        $before = $DB->get_record('course_modules', ['id' => $activity->cmid], '*', MUST_EXIST);
        $items = unit_actions::existing($course, $section->id);
        $this->assertCount(1, $items);
        $this->assertContains(get_string('materialcoursehidden', 'format_duallearning'), $items[0]['visibility']);
        $this->assertContains(get_string('materialunithidden', 'format_duallearning'), $items[0]['visibility']);
        $key = $before->visible ? 'materialshown' : 'materialhidden';
        $this->assertContains(get_string($key, 'format_duallearning'), $items[0]['visibility']);
        $this->assertEquals($activity->cmid, $items[0]['editurl']->get_param('update'));
        $this->assertEquals($before, $DB->get_record('course_modules', ['id' => $activity->cmid]));
        $this->assertEquals(0, $DB->get_field('course_sections', 'visible', ['id' => $section->id]));
    }

    /**
     * A listed activity with restrictions is not described as accessible to everyone.
     */
    public function test_existing_reports_restrictions(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        set_config('enableavailability', 1);
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $activity = $this->getDataGenerator()->create_module('page', [
            'course' => $course->id, 'section' => 1,
            'availability' => json_encode((object) [
                'op' => '&', 'c' => [(object) ['type' => 'date', 'd' => '>=', 't' => time() + DAYSECS]],
                'showc' => [true],
            ]),
        ]);
        $cm = get_fast_modinfo($course)->get_cm($activity->cmid);
        $items = unit_actions::existing($course, $cm->section);
        $this->assertContains(get_string('materialrestricted', 'format_duallearning'), $items[0]['visibility']);
        $this->assertContains(get_string('materialshown', 'format_duallearning'), $items[0]['visibility']);
    }

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
        $chooser = unit_actions::chooser($course, $section->id);
        $data = $chooser->export_for_template($GLOBALS['OUTPUT']);
        $this->assertEquals($section->id, $data->sectionid);
        $this->assertEquals($section->section, $data->sectionnum);
        $this->assertEquals($section->id, $data->sectionreturnid);
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
        unit_actions::chooser($course, $section->id);
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
        unit_actions::chooser($course, $section->id);
    }
}
