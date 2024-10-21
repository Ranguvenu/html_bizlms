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
 * TODO describe file uninstall
 *
 * @package    local_custom_matrix
 * @copyright  2024 Moodle India Information Solutions Pvt Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function xmldb_local_custom_matrix_uninstall() {

    global $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('local_performance_logs');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_performance_overall');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_performance_template');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_performance_matrix');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_performance_monthly');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_performance_quarterly');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    return true;
}
