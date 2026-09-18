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
 * Resolves a teacher return route from the current course page, without session state.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class authoring_navigation {
    /**
     * Resolve only sections belonging to the current Dual Learning course.
     *
     * @param \moodle_page $page Current page.
     * @return \moodle_url|null Permitted return URL, or no link.
     */
    public static function target(\moodle_page $page): ?\moodle_url {
        $course = $page->course;
        if ($course->format !== 'duallearning') {
            return null;
        }
        $context = \context_course::instance($course->id);
        if (!has_all_capabilities(['moodle/course:update', 'moodle/course:sectionvisibility'], $context)) {
            return null;
        }
        if (in_array($page->pagetype, ['mod-quiz-attempt', 'mod-quiz-summary'])) {
            return null;
        }
        $sectionid = 0;
        if ($page->cm) {
            $sectionid = (int) $page->cm->section;
        } else if (
            $page->url->compare(new \moodle_url('/course/section.php'), URL_MATCH_BASE)
            || $page->url->compare(new \moodle_url('/course/editsection.php'), URL_MATCH_BASE)
        ) {
            $sectionid = (int) $page->url->get_param('id');
        } else if ($page->url->compare(new \moodle_url('/course/modedit.php'), URL_MATCH_BASE)) {
            $sectionid = (int) $page->url->get_param('sectionid');
            if (!$sectionid && $page->url->get_param('section') !== null) {
                // Core normalises new-activity URLs to a section number.
                $section = get_fast_modinfo($course)->get_section_info((int) $page->url->get_param('section'), IGNORE_MISSING);
                $sectionid = $section ? (int) $section->id : 0;
            }
        }
        if (!$sectionid) {
            return null;
        }
        $section = get_fast_modinfo($course)->get_section_info_by_id($sectionid, IGNORE_MISSING);
        if (!$section || (int) $section->sectionnum === 0 || $section->component) {
            return null;
        }
        return new \moodle_url('/course/format/duallearning/unit.php', [
            'courseid' => $course->id,
            'sectionid' => $section->id,
        ]);
    }
}
