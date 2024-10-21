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
 * TODO describe file roles
 *
 * @package    local_costcenter
 * @copyright  2024 Moodle India Information Solutions Pvt Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
global $PAGE, $DB, $USER, $CFG;
require_once($CFG->libdir . '/accesslib.php'); // Moodle access library
$roles = ['administrator', 'trainer', 'tc'];
$resettype = 0;
$systemcontext = \context_system::instance();
foreach ($roles as $role) {

    if ($DB->record_exists('role', array('shortname' => $role))) {
        $roleid = $DB->get_field('role', 'id', ['shortname' => $role]);
        $file =  $CFG->dirroot . '/local/costcenter/roles/' . $role . '.xml';
        if (file_exists($file) && is_readable($file)) {
            // Read XML file contents
            $xmlString = file_get_contents($file);
            // Parse XML string
            $roleXml = simplexml_load_string($xmlString);

            $shortname = (string) $roleXml->shortname;
            $name = (string) $roleXml->name;
            $capabilities = $DB->get_records_sql_menu(
                "SELECT id,name
                 FROM {capabilities}
                 ORDER BY id ASC",
                []
            );

            $allpermissions = array(
                'inherit' => CAP_INHERIT,
                'allow' => CAP_ALLOW,
                'prevent' => CAP_PREVENT,
                'prohibit' => CAP_PROHIBIT
            );

            foreach ($roleXml->permissions as $capabilityXml) {
                foreach ($capabilityXml as $key => $capability) {
                    $cap = $capability->__toString();
                    $permission = $allpermissions[$key];
                    if (in_array($cap, $capabilities)) {
                        assign_capability($cap, $permission, $roleid, $systemcontext->id, true);
                    }
                }
            }
        }
    }
    echo $role . ' is updated .';
}
