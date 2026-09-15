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
 * Learning mode factory tests.
 *
 * @package   format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_duallearning;

use format_duallearning\local\mode\factory;

/**
 * Verifies the separate presentation rules for both learning modes.
 */
final class mode_factory_test extends \advanced_testcase {
    /**
     * Guided mode includes the question forum.
     */
    public function test_guided_mode(): void {
        $mode = factory::from_name('guided');

        $this->assertSame('guidedheading', $mode->heading_string());
        $this->assertTrue($mode->include_shortcut('forum'));
        $this->assertFalse($mode->include_shortcut('assign'));
    }

    /**
     * Path mode keeps its shortcuts minimal.
     */
    public function test_path_mode(): void {
        $mode = factory::from_name('path');

        $this->assertSame('pathheading', $mode->heading_string());
        $this->assertFalse($mode->include_shortcut('forum'));
    }

    /**
     * Unknown stored values fail closed to the path presentation.
     */
    public function test_unknown_mode_uses_path(): void {
        $mode = factory::from_name('unexpected');

        $this->assertSame('pathheading', $mode->heading_string());
        $this->assertFalse($mode->include_shortcut('forum'));
    }
}
