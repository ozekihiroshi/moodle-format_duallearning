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
 * Teacher-only return link for standard course and activity pages.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class authoring_return implements \renderable {
    /**
     * Create a return link.
     *
     * @param \moodle_url $url Unit authoring URL.
     * @param bool $editing Whether the page contains a standard editing form.
     */
    public function __construct(
        /** @var \moodle_url Unit authoring URL. */
        public \moodle_url $url,
        /** @var bool Whether to remind the author to save first. */
        public bool $editing = false,
    ) {
    }
}
