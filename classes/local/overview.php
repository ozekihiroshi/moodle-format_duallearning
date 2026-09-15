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
 * Builds the read-only learning overview.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_duallearning\local;

use format_duallearning\local\mode\factory as mode_factory;
use format_duallearning\local\mode\mode;

/**
 * Reads standard Moodle state without persisting progress or grades.
 */
class overview {
    /**
     * Build the overview template context.
     *
     * @param \stdClass $course Course record.
     * @param int $userid User whose visible state should be read.
     * @return array Template context.
     */
    public static function build(\stdClass $course, int $userid): array {
        global $CFG;
        require_once($CFG->dirroot . '/course/format/lib.php');
        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->libdir . '/gradelib.php');
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
        $context = \context_course::instance($course->id);
        $teacher = has_capability('moodle/course:update', $context, $userid);
        $mode = mode_factory::from_name(
            course_get_format($course)->get_format_options()['learningmode'] ?? 'path'
        );
        $items = self::collect_items($course, $userid, $teacher, $mode);
        $next = self::first_incomplete($items['targets']);
        return [
            'heading' => get_string($mode->heading_string(), 'format_duallearning'),
            'next' => $next,
            'nextlabel' => get_string('next', 'format_duallearning'),
            'explanation' => get_string('explanation', 'format_duallearning'),
            'progress' => !$teacher && $items['manualtotal'] ? get_string(
                'progress',
                'format_duallearning',
                (object) [
                    'done' => $items['manualdone'],
                    'total' => $items['manualtotal'],
                ]
            ) : '',
            'progresshint' => get_string('progresshint', 'format_duallearning'),
            'shortcutheading' => get_string('shortcutheading', 'format_duallearning'),
            'hasshortcuts' => !empty($items['shortcuts']),
            'lablaunchhint' => $items['haslab'] ? get_string('lablaunchhint', 'format_duallearning') : '',
            'finished' => !$teacher && $items['targets'] && !$next,
            'finishedlabel' => get_string('finished', 'format_duallearning'),
            'shortcuts' => $items['shortcuts'],
            'assignments' => $items['assignments'],
            'assignmentheading' => get_string('assignmentheading', 'format_duallearning'),
            'deadlinehint' => get_string('deadlinehint', 'format_duallearning'),
            'timezone' => \core_date::get_user_timezone(),
            'statehint' => get_string('statehint', 'format_duallearning'),
        ];
    }

    /**
     * Collect visible activities and their derived summaries.
     *
     * @param \stdClass $course Course record.
     * @param int $userid User identifier.
     * @param bool $teacher Whether the user can update the course.
     * @param mode $mode Selected learning mode.
     * @return array Targets, shortcuts, assignments, and completion counts.
     */
    private static function collect_items(\stdClass $course, int $userid, bool $teacher, mode $mode): array {
        $completion = new \completion_info($course);
        $items = [
            'targets' => [],
            'assignments' => [],
            'shortcuts' => [],
            'haslab' => false,
            'manualtotal' => 0,
            'manualdone' => 0,
        ];
        foreach (get_fast_modinfo($course, $userid)->get_cms() as $cm) {
            if (!$cm->uservisible || !$cm->url || $cm->deletioninprogress) {
                continue;
            }
            $link = [
                'name' => format_string($cm->name, true, ['context' => $cm->context]),
                'url' => $cm->url->out(false),
            ];
            if ($cm->modname === 'lti' || $mode->include_shortcut($cm->modname)) {
                $items['shortcuts'][] = $link;
            }
            $items['haslab'] = $items['haslab'] || $cm->modname === 'lti';
            self::collect_completion(
                $items['targets'],
                $items['manualtotal'],
                $items['manualdone'],
                $cm,
                $link,
                $completion,
                $userid,
                $teacher
            );
            $assignment = self::assignment_summary($course, $cm, $link, $userid, $teacher);
            if ($assignment !== null) {
                $items['assignments'][] = $assignment;
            }
        }
        return $items;
    }

    /**
     * Add learner completion state when Moodle completion is enabled.
     *
     * @param array $targets Completion targets updated by reference.
     * @param int $manualtotal Manual target count updated by reference.
     * @param int $manualdone Completed manual target count updated by reference.
     * @param \cm_info $cm Course module information.
     * @param array $link Link template data.
     * @param \completion_info $completion Completion service.
     * @param int $userid User identifier.
     * @param bool $teacher Whether the user can update the course.
     */
    private static function collect_completion(
        array &$targets,
        int &$manualtotal,
        int &$manualdone,
        \cm_info $cm,
        array $link,
        \completion_info $completion,
        int $userid,
        bool $teacher
    ): void {
        if ($teacher || !$completion->is_enabled($cm)) {
            return;
        }
        $state = $completion->get_data($cm, false, $userid)->completionstate;
        $finished = in_array((int) $state, [COMPLETION_COMPLETE, COMPLETION_COMPLETE_PASS], true);
        $targets[] = $link + ['finished' => $finished];
        if ((int) $cm->completion === COMPLETION_TRACKING_MANUAL && $cm->modname !== 'assign') {
            $manualtotal++;
            $manualdone += (int) $finished;
        }
    }

    /**
     * Build assignment template data for an assignment activity.
     *
     * @param \stdClass $course Course record.
     * @param \cm_info $cm Course module information.
     * @param array $link Link template data.
     * @param int $userid User identifier.
     * @param bool $teacher Whether the user can update the course.
     * @return array|null Assignment data, or null for another activity type.
     */
    private static function assignment_summary(
        \stdClass $course,
        \cm_info $cm,
        array $link,
        int $userid,
        bool $teacher
    ): ?array {
        if ($cm->modname !== 'assign') {
            return null;
        }
        $assignment = new \assign($cm->context, $cm, $course);
        $instance = $assignment->get_instance();
        $deadline = $instance->duedate;
        return $link + [
            'status' => get_string(
                self::assignment_status($course, $cm, $assignment, $instance, $userid, $teacher),
                'format_duallearning'
            ),
            'deadline' => $deadline ? userdate($deadline) : get_string('nodeadline', 'format_duallearning'),
        ];
    }

    /**
     * Resolve a conservative assignment status from standard Moodle state.
     *
     * @param \stdClass $course Course record.
     * @param \cm_info $cm Course module information.
     * @param \assign $assignment Assignment API object.
     * @param \stdClass $instance Assignment instance record.
     * @param int $userid User identifier.
     * @param bool $teacher Whether the user can update the course.
     * @return string Status language key.
     */
    private static function assignment_status(
        \stdClass $course,
        \cm_info $cm,
        \assign $assignment,
        \stdClass $instance,
        int $userid,
        bool $teacher
    ): string {
        if ($teacher || !has_capability('mod/assign:submit', $cm->context, $userid)) {
            return 'teacherstatus';
        }
        if ($instance->teamsubmission) {
            return 'checkassignment';
        }
        $submission = $assignment->get_user_submission($userid, false);
        $status = match ($submission->status ?? '') {
            'submitted' => 'submitted',
            'draft' => 'draft',
            'reopened' => 'reopened',
            default => 'notsubmitted',
        };
        return self::has_published_grade($course, $assignment, $instance, $submission, $userid)
            ? 'graded' : $status;
    }

    /**
     * Check whether the learner may see a released assignment grade.
     *
     * @param \stdClass $course Course record.
     * @param \assign $assignment Assignment API object.
     * @param \stdClass $instance Assignment instance record.
     * @param \stdClass|null $submission User submission record.
     * @param int $userid User identifier.
     * @return bool Whether a published grade is visible.
     */
    private static function has_published_grade(
        \stdClass $course,
        \assign $assignment,
        \stdClass $instance,
        ?\stdClass $submission,
        int $userid
    ): bool {
        if (!$submission || $submission->status !== 'submitted') {
            return false;
        }
        $info = grade_get_grades($course->id, 'mod', 'assign', $instance->id, $userid);
        $item = $info->items[0] ?? null;
        $grade = $item->grades[$userid] ?? null;
        if (!$grade || !empty($item->hidden) || !empty($grade->hidden) || $grade->grade === null) {
            return false;
        }
        return !$instance->markingworkflow
            || $assignment->get_grading_status($userid) === ASSIGN_MARKING_WORKFLOW_STATE_RELEASED;
    }

    /**
     * Find the first visible incomplete target in course order.
     *
     * @param array $targets Completion targets.
     * @return array|null First incomplete target, or null.
     */
    private static function first_incomplete(array $targets): ?array {
        foreach ($targets as $target) {
            if (!$target['finished']) {
                return $target;
            }
        }
        return null;
    }
}
