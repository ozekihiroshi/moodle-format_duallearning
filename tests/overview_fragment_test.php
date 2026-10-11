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

/**
 * Access checks for asynchronous learner overview rendering.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversFunction('format_duallearning_output_fragment_overview')]
final class overview_fragment_test extends \advanced_testcase {
    /**
     * Reject an unrelated context even for an administrator.
     */
    public function test_context_must_match_course(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/duallearning/lib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $other = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $this->expectException(\invalid_parameter_exception::class);
        format_duallearning_output_fragment_overview([
            'courseid' => $course->id,
            'context' => \context_course::instance($other->id),
        ]);
    }

    /**
     * A logged-in user without enrolment must not retrieve the overview.
     */
    public function test_unenrolled_user_cannot_retrieve_overview(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/duallearning/lib.php');
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->expectException(\moodle_exception::class);
        format_duallearning_output_fragment_overview([
            'courseid' => $course->id,
            'context' => \context_course::instance($course->id),
        ]);
    }

    /**
     * A supplied user ID cannot reveal another user's hidden activities.
     */
    public function test_fragment_uses_session_user_visibility(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/duallearning/lib.php');
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course(['format' => 'duallearning']);
        $student = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($student->id, $course->id, 'student');
        $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id, 'name' => 'Visible submission',
        ]);
        $this->getDataGenerator()->create_module('assign', [
            'course' => $course->id, 'name' => 'Private submission', 'visible' => 0,
        ]);
        $this->setUser($student);
        $html = format_duallearning_output_fragment_overview([
            'courseid' => $course->id,
            'context' => \context_course::instance($course->id),
            'userid' => get_admin()->id,
        ]);
        $this->assertStringContainsString('Visible submission', $html);
        $this->assertStringNotContainsString('Private submission', $html);
    }
}
