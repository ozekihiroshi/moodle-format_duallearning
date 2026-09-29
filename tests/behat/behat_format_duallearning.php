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

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Navigation to the authoring route using stable section IDs.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_format_duallearning extends behat_base {
    /**
     * Prepare a hidden draft through the standard section API, before browser actions.
     *
     * @Given /^the Dual Learning section "(?P<sectionnum>\d+)" in course "(?P<shortname>[^"]+)" is hidden$/
     * @param int $sectionnum Section position in the fixture.
     * @param string $shortname Course short name.
     */
    public function prepare_hidden_unit($sectionnum, $shortname): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/lib.php');
        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $sectionnum], '*', MUST_EXIST);
        rebuild_course_cache($course->id, true);
        course_update_section($course, $section, (object) ['visible' => 0]);
    }

    /**
     * Open authoring for a numbered section in a fixture course.
     *
     * @Given /^I open Dual Learning authoring for section "(?P<sectionnum>\d+)" in course "(?P<shortname>[^"]+)"$/
     * @param int $sectionnum Section position in the fixture.
     * @param string $shortname Course short name.
     */
    public function open_unit_authoring($sectionnum, $shortname): void {
        global $DB;
        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $sectionnum], '*', MUST_EXIST);
        $url = new moodle_url('/course/format/duallearning/unit.php', [
            'courseid' => $course->id, 'sectionid' => $section->id,
        ]);
        $this->getSession()->visit($url->out(false));
    }
}
