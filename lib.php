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

/**
 * Core course format implementation.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/topics/lib.php');

/**
 * Dual Learning course format.
 */
class format_duallearning extends format_topics {
    /**
     * Offer authors a return route to the current unit above standard content.
     *
     * @return \renderable|null Teacher-only authoring link.
     */
    public function course_content_header() {
        global $PAGE;
        $url = \format_duallearning\local\authoring_navigation::target($PAGE);
        if (!$url) {
            return null;
        }
        return new \format_duallearning\output\authoring_return(
            $url,
            $PAGE->url->compare(new moodle_url('/course/modedit.php'), URL_MATCH_BASE)
                || $PAGE->url->compare(new moodle_url('/course/editsection.php'), URL_MATCH_BASE)
                || $PAGE->url->compare(new moodle_url('/question/bank/editquestion/question.php'), URL_MATCH_BASE)
        );
    }

    /**
     * Return course format options.
     *
     * @param bool $foreditform Whether options are requested for the edit form.
     * @return array Course format options.
     */
    public function course_format_options($foreditform = false): array {
        $options = parent::course_format_options($foreditform);
        $options['learningmode'] = ['default' => 'path', 'type' => PARAM_ALPHA];
        if ($foreditform) {
            $options['learningmode'] += [
                'label' => new lang_string('learningmode', 'format_duallearning'),
                'element_type' => 'select',
                'element_attributes' => [[
                    'guided' => get_string('guided', 'format_duallearning'),
                    'path' => get_string('path', 'format_duallearning'),
                ]],
            ];
        }
        return $options;
    }
}

/**
 * Delegate standard inline section editing with this format's course check.
 *
 * @param string $itemtype Editable item type.
 * @param int $itemid Course section identifier.
 * @param string $newvalue New section name.
 * @return \core\output\inplace_editable|null Updated item, or null for unsupported item types.
 */
function format_duallearning_inplace_editable($itemtype, $itemid, $newvalue): ?\core\output\inplace_editable {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            [$itemid, 'duallearning'],
            MUST_EXIST
        );
        return course_get_format($section->course)->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
    return null;
}
