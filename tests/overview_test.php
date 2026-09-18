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
}
