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

/**
 * Install script for Epsilon_classic
 *
 * Documentation: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @package    theme_epsilon_classic
 * @copyright  2024 Moodle India Information Solutions Pvt Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Executed on installation of Epsilon_classic
 *
 * @return bool
 */
function xmldb_theme_epsilon_classic_install() {
    global $DB;
    $record = $DB->get_record('config', array('name' => 'theme'));
    if ($record) {
        $record->value = 'epsilon_classic';
        $DB->update_record('config', $record);
    }
    return true;
}
