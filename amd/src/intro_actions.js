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
 * Clear a draft introduction without deleting its title or saving the form.
 *
 * @module format_duallearning/intro_actions
 * @copyright 2026 Hiroshi Ozeki
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['editor_tiny/editor'], function(Tiny) {
    /** Initialise controls for the standard Moodle editor. */
    const init = () => {
        const controls = document.querySelector('[data-region="intro-actions"]');
        const field = document.querySelector('textarea[name="summary_editor[text]"]');
        if (!controls || !field) {
            return;
        }
        const clear = controls.querySelector('[data-action="clear-intro"]');
        const undo = controls.querySelector('[data-action="undo-clear-intro"]');
        const status = controls.querySelector('[role="status"]');
        let previous = null;
        let cleared = null;
        controls.hidden = false;
        const access = () => {
            const editor = Tiny.getInstanceForElementId(field.id);
            if (editor) {
                return {
                    read: () => editor.getContent(),
                    write: value => {
                        editor.undoManager.transact(() => editor.setContent(value));
                        editor.save();
                        editor.dispatch('change');
                    },
                };
            }
            if (field.getClientRects().length) {
                return {
                    read: () => field.value,
                    write: value => {
                        field.value = value;
                        field.dispatchEvent(new Event('input', {bubbles: true}));
                        field.dispatchEvent(new Event('change', {bubbles: true}));
                    },
                };
            }
            status.textContent = controls.dataset.loading;
            return null;
        };
        clear.addEventListener('click', () => {
            const editor = access();
            if (!editor || editor.read() === cleared) {
                return;
            }
            previous = editor.read();
            editor.write('');
            cleared = editor.read();
            undo.disabled = false;
            status.textContent = controls.dataset.cleared;
        });
        undo.addEventListener('click', () => {
            const editor = access();
            if (!editor || previous === null) {
                return;
            }
            if (editor.read() === cleared) {
                editor.write(previous);
                status.textContent = controls.dataset.restored;
            } else {
                status.textContent = controls.dataset.changed;
            }
            previous = null;
            cleared = null;
            undo.disabled = true;
            clear.focus();
        });
    };
    return {init};
});
