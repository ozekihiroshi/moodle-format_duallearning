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
 * Course display entry point.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($displaysection === null) {
    $context = context_course::instance($course->id);
    $canauthor = has_all_capabilities([
        'moodle/course:update',
        'moodle/course:sectionvisibility',
    ], $context);
    if ($PAGE->user_is_editing() && $canauthor) {
        $hasunits = false;
        foreach (get_fast_modinfo($course)->get_section_info_all() as $section) {
            // Match the selectable units on the authoring page, including hidden drafts.
            if ((int) $section->sectionnum !== 0 && !$section->component) {
                $hasunits = true;
                break;
            }
        }
        if ($hasunits) {
            $continueurl = new moodle_url('/course/format/duallearning/unit.php', ['courseid' => $course->id]);
            echo html_writer::div(html_writer::link($continueurl, get_string('continueunit', 'format_duallearning'), [
                'class' => 'btn btn-primary',
            ]), 'mb-3');
        }
        $url = new moodle_url('/course/format/duallearning/author.php', ['courseid' => $course->id]);
        echo html_writer::div(html_writer::link($url, get_string('startunit', 'format_duallearning'), [
            'class' => $hasunits ? 'btn btn-secondary' : 'btn btn-primary',
        ]), 'mb-3');
    }
    $panel = \format_duallearning\local\overview::build($course, $USER->id);
    echo html_writer::div($OUTPUT->render_from_template('format_duallearning/overview', $panel), '', [
        'id' => 'duallearning-overview', 'aria-live' => 'polite',
    ]);
    $PAGE->requires->js_call_amd('format_duallearning/overview', 'init', [$course->id, $context->id]);
}
require($CFG->dirroot . '/course/format/topics/format.php');
