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
     * List existing unit content in course order with permitted editing links.
     *
     * @param \stdClass $course Course record.
     * @param int $sectionid Stable section identifier.
     * @return array Existing content and its permitted actions.
     */
    public static function existing(\stdClass $course, int $sectionid): array {
        unit_starter::require_access($course);
        $modinfo = get_fast_modinfo($course);
        $section = $modinfo->get_section_info_by_id($sectionid, MUST_EXIST);
        $items = [];
        foreach ($modinfo->sections[$section->sectionnum] ?? [] as $cmid) {
            $cm = $modinfo->get_cm($cmid);
            if (!$cm->uservisible || $cm->deletioninprogress) {
                continue;
            }
            $context = \context_module::instance($cmid);
            // Describe settings, not the effective access of an individual learner.
            $visibility = [];
            if (!$course->visible) {
                $visibility[] = get_string('materialcoursehidden', 'format_duallearning');
            }
            if (!$section->visible) {
                $visibility[] = get_string('materialunithidden', 'format_duallearning');
            }
            $visibility[] = get_string($cm->visible ? 'materialshown' : 'materialhidden', 'format_duallearning');
            if ($cm->availability || $section->availability) {
                $visibility[] = get_string('materialrestricted', 'format_duallearning');
            }
            $items[] = [
                'name' => $cm->get_formatted_name(),
                'visibility' => $visibility,
                'viewurl' => $cm->url,
                'editurl' => has_capability('moodle/course:manageactivities', $context)
                    ? new \moodle_url('/course/modedit.php', ['update' => $cmid, 'sr' => $section->sectionnum]) : null,
                'questionsurl' => $cm->modname === 'quiz' && has_capability('mod/quiz:manage', $context)
                    ? new \moodle_url('/mod/quiz/edit.php', ['cmid' => $cmid]) : null,
            ];
        }
        return $items;
    }

    /**
     * Return Moodle's chooser control for the selected unit without changing edit mode.
     *
     * @param \stdClass $course Course record.
     * @param int $sectionid Stable section identifier.
     * @return \core_courseformat\output\local\content\activitychooserbutton|null Standard control, if permitted.
     */
    public static function chooser(\stdClass $course, int $sectionid):
            ?\core_courseformat\output\local\content\activitychooserbutton {
        unit_starter::require_access($course);
        $section = get_fast_modinfo($course)->get_section_info_by_id($sectionid, MUST_EXIST);
        if (!has_capability('moodle/course:manageactivities', \context_course::instance($course->id))) {
            return null;
        }
        return new \core_courseformat\output\local\content\activitychooserbutton(
            section: $section,
            sectionreturn: $section,
        );
    }
}
