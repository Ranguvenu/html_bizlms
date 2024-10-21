<?php

/**
 * This file is part of eAbyas
 *
 * Copyright eAbyas Info Solutons Pvt Ltd, India
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author eabyas  <info@eabyas.in>
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_local_custom_matrix_install() {
    global $DB, $CFG;
    $dbman = $DB->get_manager();

    $table = new xmldb_table('local_performance_overall');
    if (!$dbman->table_exists($table)) {

        $field = new xmldb_field('financialyear');
        $field->set_attributes(XMLDB_TYPE_CHAR, '128', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    $table = new xmldb_table('local_performance_overall');
    if (!$dbman->table_exists($table)) {
        $field = new xmldb_field('role');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    $table = new xmldb_table('local_performance_overall');
    if (!$dbman->table_exists($table)) {
        $field1 = new xmldb_field('parentid');
        $field1->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        $field2 = new xmldb_field('type');
        $field2->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }

        $table = new xmldb_table('local_performance_logs');
        $field1 = new xmldb_field('parentid');
        $field1->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        if (!$dbman->field_exists($table, $field1)) {
            $dbman->add_field($table, $field1);
        }

        $field2 = new xmldb_field('type');
        $field2->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        if (!$dbman->field_exists($table, $field2)) {
            $dbman->add_field($table, $field2);
        }
    }

    $table = new xmldb_table('local_performance_matrix');
    if (!$dbman->table_exists($table)) {

        $field = new xmldb_field('type');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    $table = new xmldb_table('local_performance_logs');
    if (!$dbman->table_exists($table)) {
        $field = new xmldb_field('period');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    $table = new xmldb_table('local_performance_overall');
    if (!$dbman->table_exists($table)) {
        $field = new xmldb_field('period');
        $field->set_attributes(XMLDB_TYPE_CHAR, '30', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    $table = new xmldb_table('local_performance_logs');
    if (!$dbman->table_exists($table)) {
        $field = new xmldb_field('role');
        $field->set_attributes(XMLDB_TYPE_CHAR, '225', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    $table = new xmldb_table('local_performance_logs');
    if (!$dbman->table_exists($table)) {
        $field = new xmldb_field('financialyear');
        $field->set_attributes(XMLDB_TYPE_CHAR, '128', null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        if (date('m') >= 4) {
            $year = date('Y') . "-" . (date('Y') + 1);
        } else {
            $year = (date('Y') - 1) . "-" . date('Y');
        }

        $matrixrecs = $DB->get_records('local_performance_logs',  array('financialyear' => NULL));

        if ($dbman->field_exists($table, $field)) {
            $matrixrecs = $DB->get_records('local_performance_logs',  array('financialyear' => NULL));
            if (!empty($matrixrecs)) {
                foreach ($matrixrecs as $rec) {
                    $datarecord = new \stdClass();
                    $datarecord->id = $rec->id;
                    $datarecord->financialyear = $year;
                    $DB->update_record('local_performance_logs',  $datarecord);
                }
            }
        }
    }
}
