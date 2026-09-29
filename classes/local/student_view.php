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
 * Uses Moodle role switching without publishing drafts or impersonating learners.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class student_view {
    /**
     * Separate course-index state when the current user's assumed role changes.
     *
     * Moodle 5.2 uses a second-resolution courseeditorstate generation. A rapid
     * role switch can therefore retain the previous browser state. Keep the
     * core numeric key shape, but use a distinct generation on role transitions,
     * including restoration to the user's own role. No capabilities are changed.
     *
     * @param \stdClass $course Course record.
     */
    public static function refresh_role_state(\stdClass $course): void {
        global $USER, $SESSION;
        if (!isloggedin() || $course->format !== 'duallearning') {
            return;
        }
        $context = \context_course::instance($course->id);
        $roleid = $USER->access['rsw'][$context->path] ?? 0;
        $identity = $USER->id . ':' . $roleid;
        if (($SESSION->format_duallearning_viewroles[$course->id] ?? null) === $identity) {
            return;
        }
        $cache = \cache::make('core', 'courseeditorstate');
        $previous = $cache->get($course->id);
        do {
            $key = $course->cacherev . '_' . random_int(1, PHP_INT_MAX);
        } while ($key === $previous);
        $cache->set($course->id, $key);
        $SESSION->format_duallearning_viewroles[$course->id] = $identity;
    }

    /**
     * Offer only permitted student-archetype roles for a visible course.
     *
     * @param \stdClass $course Course record.
     * @return array Role IDs and locally configured display names.
     */
    public static function roles(\stdClass $course): array {
        unit_starter::require_access($course);
        if (!$course->visible || is_role_switched($course->id)) {
            return [];
        }
        $allowed = get_switchable_roles(\context_course::instance($course->id));
        return array_intersect_key($allowed, get_archetype_roles('student'));
    }

    /**
     * Build a standard switch-role action returning to the learner course page.
     *
     * @param \stdClass $course Course record.
     * @param int $sectionid Stable unit identifier.
     * @param int $roleid Permitted student role.
     * @return \moodle_url Standard POST target; the renderer adds a session key.
     */
    public static function start_url(\stdClass $course, int $sectionid, int $roleid): \moodle_url {
        $roles = self::roles($course);
        if (!isset($roles[$roleid])) {
            throw new \required_capability_exception(
                \context_course::instance($course->id),
                'moodle/role:switchroles',
                'nopermissions',
                ''
            );
        }
        $section = get_fast_modinfo($course)->get_section_info_by_id($sectionid, MUST_EXIST);
        $returnurl = new \moodle_url('/course/view.php', [
            'id' => $course->id, 'duallearningunit' => $section->id,
        ]);
        return new \moodle_url('/course/switchrole.php', [
            'id' => $course->id, 'switchrole' => $roleid, 'returnurl' => $returnurl->out_as_local_url(false),
        ]);
    }

    /**
     * Restore the real role and return to a validated unit, or the unit chooser.
     *
     * @param \moodle_page $page Current course page.
     * @param int $sectionid Optional originating unit on the course overview.
     * @return \moodle_url|null Standard restore action, never an authoring grant.
     */
    public static function return_url(\moodle_page $page, int $sectionid = 0): ?\moodle_url {
        $course = $page->course;
        if ($course->format !== 'duallearning' || !is_role_switched($course->id)) {
            return null;
        }
        if (in_array($page->pagetype, ['mod-quiz-attempt', 'mod-quiz-summary'])) {
            return null;
        }
        if ($page->cm) {
            $sectionid = (int) $page->cm->section;
        } else if ($page->url->compare(new \moodle_url('/course/section.php'), URL_MATCH_BASE)) {
            $sectionid = (int) $page->url->get_param('id');
        }
        $section = get_fast_modinfo($course)->get_section_info_by_id($sectionid, IGNORE_MISSING);
        $params = ['courseid' => $course->id];
        if ($section && $section->sectionnum > 0 && !$section->component) {
            $params['sectionid'] = $section->id;
        }
        $returnurl = new \moodle_url('/course/format/duallearning/unit.php', $params);
        return new \moodle_url('/course/switchrole.php', [
            'id' => $course->id, 'switchrole' => 0, 'returnurl' => $returnurl->out_as_local_url(false),
        ]);
    }
}
