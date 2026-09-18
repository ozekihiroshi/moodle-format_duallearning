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

namespace format_duallearning\output;

/**
 * Activity navigation following displayed subsection order.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ordered_activity_navigation extends \core_course\output\activity_navigation {
    /**
     * Replace navigation only where core's activity order differs from the course display.
     *
     * @param \moodle_page $page Current page.
     * @return self|null Ordered navigation, or null to retain standard navigation.
     */
    public static function for_page(\moodle_page $page): ?self {
        if (
            !in_array($page->pagelayout, ['incourse', 'frametop'])
            || $page->context->contextlevel !== CONTEXT_MODULE
            || !$page->cm
            || $page->cm->is_stealth()
            || $page->course->format !== 'duallearning'
        ) {
            return null;
        }
        $format = course_get_format($page->course);
        if ($page->theme->usescourseindex && $format->uses_course_index() && $page->pagelayout !== 'frametop') {
            return null;
        }
        $modinfo = get_fast_modinfo($page->course);
        $modules = $modinfo->get_cms();
        $eligible = static fn($cm) => $cm->uservisible && !$cm->is_stealth()
            && !empty($cm->url) && $cm->is_of_type_that_can_display();
        $original = array_filter($modules, $eligible);
        $modinfo->sort_cm_array($modules);
        $modules = array_filter($modules, static fn($cm) => $eligible($cm) && !$cm->deletioninprogress);
        if (array_keys($original) === array_keys($modules)) {
            return null;
        }
        $ids = array_keys($modules);
        $position = array_search((int)$page->cm->id, $ids, true);
        if ($position === false || count($ids) < 2) {
            return null;
        }
        $activities = [];
        foreach ($modules as $cm) {
            if ($cm->id == $page->cm->id) {
                continue;
            }
            $name = $cm->get_formatted_name();
            if (!$cm->visible) {
                $name .= ' ' . get_string('hiddenwithbrackets');
            }
            $url = new \moodle_url($cm->url, ['forceview' => 1]);
            $activities[$url->out(false)] = $name;
        }
        $previous = $position > 0 ? $modules[$ids[$position - 1]] : null;
        $next = $position < count($ids) - 1 ? $modules[$ids[$position + 1]] : null;
        $navigation = new self($previous, $next, $activities);
        // The hidden core navigation still exists in the DOM; keep all IDs distinct.
        if ($navigation->prevlink) {
            $navigation->prevlink->attributes['id'] = 'duallearning-prev-activity-link';
        }
        if ($navigation->nextlink) {
            $navigation->nextlink->attributes['id'] = 'duallearning-next-activity-link';
        }
        $navigation->activitylist->attributes['id'] = 'duallearning-jump-to-activity';
        return $navigation;
    }
}
