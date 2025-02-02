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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace tool_courserating\local\hooks\output;

/**
 * Hook callbacks for tool_courserating
 *
 * @package    tool_courserating
 * @copyright  2024 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_footer_html_generation {

    /**
     * Callback allowing to add contetnt inside the region-main, in the very end
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function callback(\core\hook\output\before_footer_html_generation $hook): void {

        if (during_initial_install() || isset($CFG->upgraderunning)) {
            // Do nothing during installation or upgrade.
            return;
        }

        global $PAGE;
        $res = '';
        if (\tool_courserating\helper::course_ratings_enabled_anywhere()) {
            /** @var \tool_courserating\output\renderer $output */
            $output = $PAGE->get_renderer('tool_courserating');
            if (($componentid = \tool_courserating\helper::is_course_page()) ||
                ($componentid = \tool_courserating\helper::is_single_activity_course_page())) {
                $res .= $output->course_rating_block($componentid);
            }
        }
        $hook->add_html($res);
    }
}
