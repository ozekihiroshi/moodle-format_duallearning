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
 * Continue building a unit using standard Moodle content and activity forms.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

$courseid = required_param('courseid', PARAM_INT);
$sectionid = optional_param('sectionid', 0, PARAM_INT);
$course = get_course($courseid);
require_login($course);
\format_duallearning\local\unit_starter::require_access($course);
$modinfo = get_fast_modinfo($course);
$section = $sectionid ? $modinfo->get_section_info_by_id($sectionid, MUST_EXIST) : null;
$context = context_course::instance($courseid);
$params = ['courseid' => $courseid];
if ($section) {
    $params['sectionid'] = $section->id;
}
$PAGE->set_url('/course/format/duallearning/unit.php', $params);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('continueunit', 'format_duallearning'));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add(get_string('continueunit', 'format_duallearning'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('continueunit', 'format_duallearning'));
if (!$section) {
    echo html_writer::tag('p', get_string('chooseunit', 'format_duallearning'));
    $links = [];
    foreach ($modinfo->get_section_info_all() as $item) {
        if ((int) $item->sectionnum === 0 || $item->component) {
            continue;
        }
        $url = new moodle_url('/course/format/duallearning/unit.php', [
            'courseid' => $courseid, 'sectionid' => $item->id,
        ]);
        $links[] = html_writer::link($url, get_section_name($course, $item));
    }
    if ($links) {
        echo html_writer::alist($links);
    } else {
        echo html_writer::tag('p', get_string('nounitsyet', 'format_duallearning'));
    }
    echo html_writer::link(
        new moodle_url('/course/format/duallearning/author.php', ['courseid' => $courseid]),
        get_string('startunit', 'format_duallearning'),
        ['class' => 'btn btn-secondary']
    );
} else {
    echo $OUTPUT->heading(get_section_name($course, $section), 3);
    $status = $section->visible ? 'unitshownhint' : 'unithiddenhint';
    echo html_writer::tag('p', get_string($status, 'format_duallearning'));
    $editurl = new moodle_url('/course/editsection.php', ['id' => $sectionid]);
    $editlink = html_writer::link($editurl, get_string('editunittext', 'format_duallearning'), ['class' => 'btn btn-secondary']);
    echo html_writer::tag('p', $editlink);
    $items = \format_duallearning\local\unit_actions::existing($course, $sectionid);
    if ($items) {
        echo $OUTPUT->heading(get_string('existingmaterials', 'format_duallearning'), 4);
        $rows = [];
        foreach ($items as $item) {
            $name = $item['name'];
            $links = [];
            foreach (['viewurl' => 'viewmaterial', 'editurl' => 'editmaterial', 'questionsurl' => 'editquestions'] as $key => $label) {
                if ($item[$key]) {
                    $text = get_string($label, 'format_duallearning');
                    $links[] = html_writer::link($item[$key], $text, ['aria-label' => $text . ': ' . strip_tags($name)]);
                }
            }
            $rows[] = html_writer::div(
                html_writer::tag('strong', $name) . html_writer::div(implode(' · ', $links), 'mt-1'),
                'mb-3'
            );
        }
        echo html_writer::alist($rows, ['class' => 'list-unstyled']);
    }
    echo html_writer::tag('p', get_string('unitactionshint', 'format_duallearning'));
    $actions = \format_duallearning\local\unit_actions::build($course, $sectionid);
    foreach ($actions as $action) {
        $link = html_writer::link($action['url'], $action['title'], ['class' => 'btn btn-primary']);
        echo html_writer::div($link . html_writer::tag('p', $action['hint'], ['class' => 'mt-2']), 'mb-4');
    }
    if (!$actions) {
        echo html_writer::tag('p', get_string('noaddactions', 'format_duallearning'));
    }
    $viewurl = new moodle_url('/course/section.php', ['id' => $sectionid]);
    echo html_writer::tag('p', html_writer::link($viewurl, get_string('viewunit', 'format_duallearning')));
    echo html_writer::tag('p', get_string('unitreturnhint', 'format_duallearning'));
}
$backurl = new moodle_url('/course/view.php', ['id' => $courseid]);
$backlink = html_writer::link($backurl, get_string('backtocourse', 'format_duallearning'));
echo html_writer::tag('p', $backlink, ['class' => 'mt-3']);
echo $OUTPUT->footer();
