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
 * Learning mode factory.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_duallearning\local\mode;

/**
 * Resolves stored course options to supported mode objects.
 */
final class factory {
    /**
     * Resolve a stored learning mode.
     *
     * Unknown values use the minimal self-paced presentation.
     *
     * @param string $name Stored mode name.
     * @return mode Learning mode implementation.
     */
    public static function from_name(string $name): mode {
        return $name === 'guided' ? new guided() : new path();
    }
}
