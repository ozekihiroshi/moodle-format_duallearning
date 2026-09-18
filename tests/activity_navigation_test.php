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

namespace format_duallearning;

use format_duallearning\output\activity_navigation;

/**
 * Regression coverage for activity links across subsection boundaries.
 *
 * @package format_duallearning
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(activity_navigation::class)]
final class activity_navigation_test extends \advanced_testcase {
    /**
     * Build a page with a theme that displays activity navigation.
     *
     * @param \stdClass $course Course.
     * @param int $cmid Current activity.
     * @return \moodle_page Page.
     */
    private function page(\stdClass $course, int $cmid): \moodle_page {
        $page = new \moodle_page();
        $page->set_cm(get_fast_modinfo($course)->get_cm($cmid));
        $page->set_url('/mod/page/view.php', ['id' => $cmid]);
        $page->set_pagelayout('incourse');
        $page->force_theme('classic');
        return $page;
    }

    /**
     * Check both directions, endpoint links, and selector order in nested content.
     */
    public function test_links_follow_subsection_order(): void {
        global $CFG, $OUTPUT, $DB;
        require_once($CFG->dirroot . '/course/lib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['format' => 'duallearning', 'numsections' => 1]);
        $first = $generator->create_module('page', ['course' => $course->id, 'section' => 1, 'name' => 'Before']);
        $last = $generator->create_module('page', ['course' => $course->id, 'section' => 1, 'name' => 'After']);
        $unit = $generator->create_module('subsection', ['course' => $course->id, 'section' => 1]);
        $section = get_fast_modinfo($course)->get_cm($unit->cmid)->get_delegated_section_info();
        $inside = $generator->create_module('page', [
            'course' => $course->id, 'section' => $section->sectionnum, 'name' => 'Inside',
        ]);
        $second = $generator->create_module('page', [
            'course' => $course->id, 'section' => $section->sectionnum, 'name' => 'Inside second',
        ]);
        \core_courseformat\formatactions::cm($course)->move_before($unit->cmid, $last->cmid);
        $student = $generator->create_user();
        $generator->enrol_user($student->id, $course->id, 'student');
        $this->setUser($student);
        $ordered = [$first, $inside, $second, $last];
        foreach ($ordered as $position => $activity) {
            $nav = activity_navigation::for_page($this->page($course, $activity->cmid));
            $this->assertNotNull($nav);
            if ($position === 0) {
                $this->assertNull($nav->prevlink);
            } else {
                $this->assertEquals($ordered[$position - 1]->cmid, $nav->prevlink->url->param('id'));
            }
            if ($position === count($ordered) - 1) {
                $this->assertNull($nav->nextlink);
            } else {
                $this->assertEquals($ordered[$position + 1]->cmid, $nav->nextlink->url->param('id'));
            }
        }
        $page = $this->page($course, $inside->cmid);
        $nav = activity_navigation::for_page($page);
        $data = $nav->export_for_template($OUTPUT);
        $this->assertSame('duallearning-prev-activity-link', $data->prevlink->id);
        $this->assertSame('duallearning-next-activity-link', $data->nextlink->id);
        $names = array_column($data->activitylist->options, 'name');
        $this->assertSame(['Before', 'Inside second', 'After'], array_slice($names, 1));
        $html = course_get_format($course)->get_renderer($page)->render($nav);
        $this->assertStringContainsString('duallearning-activity-navigation', $html);
        $this->assertStringContainsString('forceview=1', $html);

        // Themes using the course index retain the standard choice to omit these links.
        $boostpage = new \moodle_page();
        $boostpage->set_cm(get_fast_modinfo($course)->get_cm($inside->cmid));
        $boostpage->set_pagelayout('incourse');
        $boostpage->force_theme('boost');
        $this->assertNull(activity_navigation::for_page($boostpage));

        // Restricted parents and activities being deleted must not leak into navigation.
        $this->setAdminUser();
        $DB->set_field('course_modules', 'deletioninprogress', 1, ['id' => $second->cmid]);
        rebuild_course_cache($course->id, true);
        $this->setUser($student);
        $nav = activity_navigation::for_page($this->page($course, $inside->cmid));
        $this->assertEquals($last->cmid, $nav->nextlink->url->param('id'));
        $this->setAdminUser();
        $DB->set_field('course_modules', 'deletioninprogress', 0, ['id' => $second->cmid]);
        $DB->set_field('course_modules', 'availability', json_encode([
            'op' => '&', 'c' => [['type' => 'date', 'd' => '>=', 't' => time() + 86400]], 'showc' => [false],
        ]), ['id' => $unit->cmid]);
        rebuild_course_cache($course->id, true);
        $this->setUser($student);
        $this->assertNull(activity_navigation::for_page($this->page($course, $first->cmid)));
        $this->setAdminUser();
        $DB->set_field('course_modules', 'availability', null, ['id' => $unit->cmid]);
        rebuild_course_cache($course->id, true);

        // A hidden parent must not leak links to its child activities.
        $this->setAdminUser();
        set_coursemodule_visible($unit->cmid, 0);
        $this->setUser($student);
        $this->assertNull(activity_navigation::for_page($this->page($course, $first->cmid)));
    }

    /**
     * Flat course navigation and non-activity pages remain unchanged.
     */
    public function test_standard_navigation_is_preserved(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['format' => 'duallearning']);
        $first = $generator->create_module('page', ['course' => $course->id]);
        $generator->create_module('page', ['course' => $course->id]);
        $page = $this->page($course, $first->cmid);
        $this->assertNull(activity_navigation::for_page($page));
        $page = new \moodle_page();
        $page->set_course($course);
        $page->set_context(\context_course::instance($course->id));
        $page->set_pagelayout('course');
        $this->assertNull(activity_navigation::for_page($page));
    }
}
