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
 * Learning mode contract.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_duallearning\local\mode;

/**
 * Defines presentation decisions belonging to one learning mode.
 */
interface mode {
    /**
     * Return the overview heading language key.
     *
     * @return string Language key.
     */
    public function heading_string(): string;

    /**
     * Decide whether an activity type is a mode-specific shortcut.
     *
     * @param string $modname Moodle activity type.
     * @return bool Whether the activity should be included.
     */
    public function include_shortcut(string $modname): bool;
}
