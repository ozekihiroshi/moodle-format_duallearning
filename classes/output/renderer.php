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
 * Course format renderer.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_duallearning\output;

/**
 * Reuse the standard Topics renderer.
 */
class renderer extends \format_topics\output\renderer {
    /**
     * Render subsection-aware links using standard Moodle link and selector components.
     *
     * @param activity_navigation $navigation Ordered activity navigation.
     * @return string HTML.
     */
    protected function render_activity_navigation(activity_navigation $navigation): string {
        return $this->render_from_template(
            'format_duallearning/activity_navigation',
            $navigation->export_for_template($this)
        );
    }

    /**
     * Render a return link without changing standard activity forms.
     *
     * @param authoring_return $link Return route.
     * @return string HTML.
     */
    protected function render_authoring_return(authoring_return $link): string {
        $html = \html_writer::link($link->url, get_string('returntounit', 'format_duallearning'), [
            'class' => 'btn btn-outline-secondary',
        ]);
        if ($link->editing) {
            $html .= \html_writer::tag('p', get_string('savebeforereturn', 'format_duallearning'), ['class' => 'small mt-2']);
        }
        return \html_writer::div($html, 'mb-3');
    }
}
