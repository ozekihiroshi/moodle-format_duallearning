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
    /**
     * Compare the standard chooser with the unit entry, using browser actions for both.
     *
     * @When I choose :activity for section :sectionnum in :shortname through its authoring entry
     * @param string $activity Activity name.
     * @param int $sectionnum Section number.
     * @param string $shortname Course short name.
     */
    public function choose_material($activity, $sectionnum, $shortname): void {
        global $DB;
        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        if ($course->format !== 'duallearning') {
            $this->getSession()->visit((new moodle_url('/course/view.php', ['id' => $course->id]))->out(false));
            $this->execute('behat_navigation::i_turn_editing_mode_on');
            $this->execute('behat_course::i_add_to_section_using_the_activity_chooser', [$activity, $sectionnum]);
            return;
        }
        $this->open_unit_authoring($sectionnum, $shortname);
        $this->execute('behat_general::i_click_on', [get_string('addresourceoractivity'), 'button']);
        // New fixture users start on the standard All tab.
        $this->execute('behat_general::i_click_on_in_the', [
            $activity, 'link', get_string('addresourceoractivity'), 'dialogue',
        ]);
        $this->execute('behat_general::i_click_on_in_the', [
            get_string('addselectedactivity', 'course'), 'button', get_string('addresourceoractivity'), 'dialogue',
        ]);
    }

    /**
     * Check saved resources, their insertion target and the retained hidden state.
     *
     * @Then the three comparison materials belong to hidden section :sectionnum in :shortname
     * @param int $sectionnum Section number.
     * @param string $shortname Course short name.
     */
    public function check_materials($sectionnum, $shortname): void {
        global $DB;
        $course = $DB->get_record('course', ['shortname' => $shortname], '*', MUST_EXIST);
        $section = $DB->get_record('course_sections', ['course' => $course->id, 'section' => $sectionnum], '*', MUST_EXIST);
        if ($section->visible || $DB->count_records('course_modules', ['course' => $course->id]) !== 3) {
            throw new \Exception('Expected exactly three activities in a still-hidden draft.');
        }
        foreach (['page', 'resource', 'url'] as $type) {
            $instance = $DB->get_record($type, ['course' => $course->id], '*', MUST_EXIST);
            $cm = get_coursemodule_from_instance($type, $instance->id, $course->id, false, MUST_EXIST);
            if ((int) $cm->section !== (int) $section->id) {
                throw new \Exception('Material saved outside the selected unit: ' . $type);
            }
            if ($type === 'resource') {
                $files = get_file_storage()->get_area_files(
                    \context_module::instance($cm->id)->id,
                    'mod_resource',
                    'content',
                    0,
                    'id',
                    false,
                );
                $file = reset($files);
                if (!$file || $file->get_filename() !== 'temperatures.pdf' || $file->get_filesize() === 0) {
                    throw new \Exception('Uploaded PDF was not retained.');
                }
            }
        }
    }
}
