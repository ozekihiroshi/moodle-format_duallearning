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
 * Start a unit with editable guidance for the course's existing learning mode.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$course = get_course($courseid);
require_login($course);
\format_duallearning\local\unit_starter::require_access($course);
$context = context_course::instance($courseid);
$PAGE->set_url('/course/format/duallearning/author.php', ['courseid' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('startunit', 'format_duallearning'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('startunit', 'format_duallearning'));
$returnurl = new moodle_url('/course/view.php', ['id' => $courseid]);
$mode = course_get_format($course)->get_format_options()['learningmode'] ?? 'path';
$mode = $mode === 'guided' ? 'guided' : 'path';
$form = new \format_duallearning\form\unit_starter(null, ['courseid' => $courseid, 'context' => $context]);
if ($form->is_cancelled()) {
    redirect($returnurl);
}
if ($data = $form->get_data()) {
    require_sesskey();
    $section = \format_duallearning\local\unit_starter::create(
        $course,
        $data->name,
        $data->summary_editor['text'],
        (int) $data->summary_editor['format']
    );
    $returnurl = new moodle_url('/course/format/duallearning/unit.php', [
        'courseid' => $courseid, 'sectionid' => $section->id,
    ]);
    $editurl = new moodle_url('/course/editsection.php', ['id' => $section->id]);
    $message = get_string('unitdraftcreated', 'format_duallearning') . ' ' . html_writer::link(
        $editurl,
        get_string('editunittext', 'format_duallearning')
    );
    redirect($returnurl, $message);
}
$form->set_data((object) [
    'summary_editor' => ['text' => \format_duallearning\local\unit_starter::summary($mode), 'format' => FORMAT_HTML],
]);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('startunit', 'format_duallearning'));
echo html_writer::tag('p', get_string('unitmode', 'format_duallearning', get_string($mode, 'format_duallearning')));
echo html_writer::tag('p', get_string('unitstarterhint', 'format_duallearning'));
$form->display();
echo $OUTPUT->footer();
