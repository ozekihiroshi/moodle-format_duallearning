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

namespace format_duallearning\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');

/**
 * A title and editable starter, rather than an authoring questionnaire.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unit_starter extends \moodleform {
    /**
     * Define the draft form.
     */
    public function definition(): void {
        $mform = $this->_form;
        $mform->addElement('hidden', 'courseid', $this->_customdata['courseid']);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('text', 'name', get_string('unitname', 'format_duallearning'), ['maxlength' => 255]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addElement('editor', 'summary_editor', get_string('unittext', 'format_duallearning'), null, [
            'context' => $this->_customdata['context'],
            'maxfiles' => 0,
            'trusttext' => false,
        ]);
        $mform->setType('summary_editor', PARAM_RAW);
        $this->add_action_buttons(true, get_string('createunitdraft', 'format_duallearning'));
    }

    /**
     * Reject whitespace-only titles before starting a write.
     *
     * @param array $data Submitted values.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        if (trim($data['name']) === '' || \core_text::strlen($data['name']) > 255) {
            $errors['name'] = get_string('invalidunitname', 'format_duallearning');
        }
        return $errors;
    }
}
