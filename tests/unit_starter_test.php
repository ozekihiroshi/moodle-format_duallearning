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

use format_duallearning\local\unit_starter;

/**
 * Protects learners and existing course contents during authoring.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(unit_starter::class)]
final class unit_starter_test extends \advanced_testcase {
    /**
     * Creating a draft preserves the existing course and uses standard sections.
     */
    public function test_create_hidden_draft(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning', 'learningmode' => 'guided']);
        $activity = $this->getDataGenerator()->create_module('page', ['course' => $course->id]);
        $before = $DB->get_record('course_modules', ['id' => $activity->cmid]);
        $draft = unit_starter::create($course, 'My unit', '<p>My explanation</p>', FORMAT_HTML);
        $stored = $DB->get_record('course_sections', ['id' => $draft->id], '*', MUST_EXIST);
        $this->assertEquals(0, $stored->visible);
        $this->assertSame('My unit', $stored->name);
        $this->assertSame('<p>My explanation</p>', $stored->summary);
        $this->assertEquals($before, $DB->get_record('course_modules', ['id' => $activity->cmid]));
        $this->assertSame('guided', course_get_format($course)->get_format_options()['learningmode']);
    }

    /**
     * Students cannot create a section by calling the write service.
     */
    public function test_student_cannot_create(): void {
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $this->expectException(\required_capability_exception::class);
        unit_starter::create($course, 'Not allowed', '', FORMAT_HTML);
    }

    /**
     * Invalid titles do not leave empty sections behind.
     */
    public function test_invalid_title_has_no_side_effects(): void {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $count = $DB->count_records('course_sections', ['course' => $course->id]);
        try {
            unit_starter::create($course, '   ', '', FORMAT_HTML);
            $this->fail('Invalid title accepted');
        } catch (\invalid_parameter_exception $exception) {
            $this->assertEquals($count, $DB->count_records('course_sections', ['course' => $course->id]));
        }
    }
}
