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
 * Creates a standard, hidden section without changing existing learning records.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unit_starter {
    /**
     * Check permissions for creating a hidden unit.
     *
     * @param \stdClass $course Course record.
     */
    public static function require_access(\stdClass $course): void {
        if ($course->format !== 'duallearning') {
            throw new \moodle_exception('invalidcourseid');
        }
        $context = \context_course::instance($course->id);
        require_capability('moodle/course:update', $context);
        require_capability('moodle/course:sectionvisibility', $context);
    }

    /**
     * Provide two entry points without duplicating the unit list.
     *
     * @param \stdClass $course Course record; the caller must check authoring access.
     * @return string Navigation HTML.
     */
    public static function navigation(\stdClass $course): string {
        $context = \context_course::instance($course->id);
        if (!has_all_capabilities(['moodle/course:update', 'moodle/course:sectionvisibility'], $context)) {
            return '';
        }
        $hasunits = false;
        foreach (get_fast_modinfo($course)->get_section_info_all() as $section) {
            if ((int) $section->sectionnum !== 0 && !$section->component) {
                $hasunits = true;
                break;
            }
        }
        $heading = get_string('buildcourse', 'format_duallearning');
        $content = \html_writer::tag('h2', $heading, ['class' => 'h4']);
        $content .= \html_writer::tag('p', get_string('buildcoursehint', 'format_duallearning'));
        $pages = $hasunits ? ['unit' => 'continueunit', 'author' => 'startunit']
            : ['author' => 'startunit', 'unit' => 'continueunit'];
        $primary = $hasunits ? 'unit' : 'author';
        $links = [];
        foreach ($pages as $page => $label) {
            $url = new \moodle_url('/course/format/duallearning/' . $page . '.php', ['courseid' => $course->id]);
            $links[] = \html_writer::link($url, get_string($label, 'format_duallearning'), [
                'class' => 'btn ' . ($page === $primary ? 'btn-primary' : 'btn-outline-secondary'),
            ]);
        }
        $content .= \html_writer::div(implode(' ', $links), 'd-flex flex-wrap gap-2');
        return \html_writer::tag('nav', $content, [
            'class' => 'duallearning-authoring-navigation border rounded p-3 mb-3', 'aria-label' => $heading,
        ]);
    }

    /**
     * Return editable learner-facing starter text for the current mode.
     *
     * @param string $mode Learning mode.
     * @return string HTML starter text.
     */
    public static function summary(string $mode): string {
        $mode = $mode === 'guided' ? 'guided' : 'path';
        $html = '';
        foreach (['goal', 'prepare', $mode, 'check', 'help', 'review'] as $part) {
            $html .= \html_writer::tag('h3', get_string('starter' . $part . 'title', 'format_duallearning'));
            $html .= \html_writer::tag('p', get_string('starter' . $part . 'body', 'format_duallearning'));
        }
        return $html;
    }

    /**
     * Append a draft using standard section storage and Moodle events.
     *
     * @param \stdClass $course Course record.
     * @param string $name Unit name.
     * @param string $summary Edited learner-facing HTML.
     * @param int $summaryformat Moodle text format.
     * @return \stdClass Created section record.
     */
    public static function create(\stdClass $course, string $name, string $summary, int $summaryformat): \stdClass {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/lib.php');
        self::require_access($course);
        $name = trim(clean_param($name, PARAM_TEXT));
        if ($name === '' || \core_text::strlen($name) > 255) {
            throw new \invalid_parameter_exception('Invalid unit name');
        }
        $transaction = $DB->start_delegated_transaction();
        $section = course_create_section($course);
        course_update_section($course, $section, (object) [
            'name' => $name,
            'summary' => $summary,
            'summaryformat' => $summaryformat,
            'visible' => 0,
        ]);
        $transaction->allow_commit();
        return $section;
    }
}
