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

namespace format_duallearning\local;

/**
 * Offers standard activity forms for a specific unit without creating content.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unit_actions {
    /**
     * Build permitted authoring actions using a stable section identifier.
     *
     * @param \stdClass $course Course record.
     * @param int $sectionid Section identifier, not its position.
     * @return array Actions with URLs and explanatory text.
     */
    public static function build(\stdClass $course, int $sectionid): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        unit_starter::require_access($course);
        $section = get_fast_modinfo($course)->get_section_info_by_id($sectionid, MUST_EXIST);
        $context = \context_course::instance($course->id);
        $actions = [];
        if (!has_capability('moodle/course:manageactivities', $context)) {
            return $actions;
        }
        foreach (['page', 'quiz', 'assign'] as $module) {
            if (!course_allowed_module($course, $module)) {
                continue;
            }
            $actions[] = [
                'module' => $module,
                'title' => get_string('add' . $module, 'format_duallearning'),
                'hint' => get_string('add' . $module . 'hint', 'format_duallearning'),
                'url' => (new \moodle_url('/course/modedit.php', [
                    'course' => $course->id,
                    'sectionid' => $section->id,
                    'add' => $module,
                    'sr' => $section->sectionnum,
                ]))->out(false),
            ];
        }
        return $actions;
    }
}
