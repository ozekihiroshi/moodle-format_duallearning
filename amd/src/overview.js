// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Refresh the learning overview after standard completion changes.
 *
 * @module format_duallearning/overview
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core_course/events', 'core/fragment', 'core/templates', 'core/notification'],
    function(Events, Fragment, Templates, Notification) {
        /**
         * Observe successful completion changes without replacing the activity controls.
         *
         * @param {number} courseid Course identifier.
         * @param {number} contextid Course context identifier.
         */
        const init = (courseid, contextid) => {
            const root = document.getElementById('duallearning-overview');
            if (!root || root.dataset.listening) {
                return;
            }
            root.dataset.listening = '1';
            let revision = 0;
            document.addEventListener(Events.manualCompletionToggled, async(event) => {
                // Moodle replaces the pressed button; restore focus only if it was lost.
                if (document.activeElement === document.body && event.target instanceof HTMLButtonElement) {
                    event.target.focus({preventScroll: true});
                }
                const current = ++revision;
                root.setAttribute('aria-busy', 'true');
                try {
                    const html = await Fragment.loadFragment('format_duallearning', 'overview', contextid, {courseid});
                    if (current !== revision || !root.isConnected) {
                        return;
                    }
                    const expanded = Array.from(root.querySelectorAll('details')).map(detail => detail.open);
                    const focused = root.contains(document.activeElement) ? document.activeElement : null;
                    const href = focused ? focused.getAttribute('href') : null;
                    await Templates.replaceNodeContents(root, html, '');
                    root.querySelectorAll('details').forEach((detail, index) => {
                        if (expanded[index] !== undefined) {
                            detail.open = expanded[index];
                        }
                    });
                    if (focused) {
                        const target = Array.from(root.querySelectorAll('a')).find(link => link.getAttribute('href') === href);
                        const heading = root.querySelector('h2');
                        if (target) {
                            target.focus();
                        } else if (heading) {
                            heading.setAttribute('tabindex', '-1');
                            heading.focus();
                        }
                    }
                } catch (error) {
                    if (current === revision) {
                        // Do not leave stale progress visible as if it were current.
                        root.replaceChildren();
                        Notification.exception(error);
                    }
                } finally {
                    if (current === revision) {
                        root.removeAttribute('aria-busy');
                    }
                }
            });
        };
        return {init};
    });
