<?php
defined('MOODLE_INTERNAL') || die();
function xmldb_local_users_install() {
    global $CFG, $USER, $DB, $OUTPUT;
    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    // Rename student role shortname to employee for BIZ.
    $role = $DB->get_record('role', array('shortname' => 'student', 'archetype' => 'student'));
    if ($role->shortname === 'student') {
        $updaterole = new stdClass();
        $updaterole->id = $role->id;
        $updaterole->shortname = 'employee';
        $updaterole->name = 'Learner';
        $DB->update_record('role',  $updaterole);
    }
    //.
    // Create default roles on plugin installation.
    require_once($CFG->libdir . '/accesslib.php'); // Moodle access library
    $roles = ['administrator', 'trainer', 'trainingcoord'];
    $systemcontext = \context_system::instance();
    foreach ($roles as $role) {
        if (!$DB->get_record('role', array('shortname' => $role))) {
            $file =  $CFG->dirroot . '/local/users/roles/' . $role . '.xml';
            if (file_exists($file) && is_readable($file)) {
                // Read XML file contents
                $xmlString = file_get_contents($file);

                // Parse XML string
                $roleXml = simplexml_load_string($xmlString);

                $definitiontable = new core_role_define_role_table_advanced($systemcontext, 0);

                $xmlcontent = file_get_contents($file);
                $options = array(
                    'shortname'     => 1,
                    'name'          => 1,
                    'description'   => 1,
                    'permissions'   => 1,
                    'archetype'     => 1,
                    'contextlevels' => 1,
                    'allowassign'   => 1,
                    'allowoverride' => 1,
                    'allowswitch'   => 1,
                    'allowview'   => 1
                );
                $definitiontable->force_preset($xmlcontent, $options);
                if ($definitiontable->is_submission_valid()) {
                    $definitiontable->save_changes();
                }

                $roleid = $definitiontable->get_role_id();
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
    }
    //end.

    $table = new xmldb_table('user');
    if ($dbman->table_exists($table)) {

        $field1 = new xmldb_field('open_path');
        $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $dbman->add_field($table, $field1);

        $field2 = new xmldb_field('open_supervisorid');
        $field2->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->add_field($table, $field2);

        $field5 = new xmldb_field('open_employeeid');
        $field5->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $dbman->add_field($table, $field5);

        $field6 = new xmldb_field('open_usermodified');
        $field6->set_attributes(XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->add_field($table, $field6);

        $field7 = new xmldb_field('open_designation');
        $field7->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $dbman->add_field($table, $field7);

        // $field11 = new xmldb_field('open_state');
        // $field11->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field11);

        // $field13 = new xmldb_field('open_jobfunction');
        // $field13->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field13);

        $field14 = new xmldb_field('open_group');
        $field14->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $dbman->add_field($table, $field14);

        $field18 = new xmldb_field('open_qualification');
        $field18->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $dbman->add_field($table, $field18);

        // $field30 = new xmldb_field('open_location');
        // $field30->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field30);

        $field31 = new xmldb_field('open_team');
        $field31->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $dbman->add_field($table, $field31);

        // $field32 = new xmldb_field('open_client');
        // $field32->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field32);

        $field34 = new xmldb_field('open_supervisorempid');
        $field34->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $dbman->add_field($table, $field34);

        // $field35 = new xmldb_field('open_band');
        // $field35->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field35);

        $field36 = new xmldb_field('open_hrmsrole');
        $field36->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $dbman->add_field($table, $field36);

        // $field37 = new xmldb_field('open_zone');
        // $field37->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field37);

        // $field38 = new xmldb_field('open_region');
        // $field38->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field38);

        // $field39 = new xmldb_field('open_grade');
        // $field39->set_attributes(XMLDB_TYPE_CHAR, '200', null, null, null, null);
        // $dbman->add_field($table, $field39);

        $field8 = new xmldb_field('open_positionid');
        $field8->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $dbman->add_field($table, $field8);

        $field8 = new xmldb_field('open_domainid');
        $field8->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $dbman->add_field($table, $field8);

        // $field = new xmldb_field('open_states');
        // $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        // if (!$dbman->field_exists($table, $field)) {
        //     $dbman->add_field($table, $field);
        // }

        // $field1 = new xmldb_field('open_district');
        // $field1->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        // if (!$dbman->field_exists($table, $field1)) {
        //     $dbman->add_field($table, $field1);
        // }

        // $field2 = new xmldb_field('open_subdistrict');
        // $field2->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        // if (!$dbman->field_exists($table, $field2)) {
        //     $dbman->add_field($table, $field2);
        // }

        // $field3 = new xmldb_field('open_village');
        // $field3->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        // if (!$dbman->field_exists($table, $field3)) {
        //     $dbman->add_field($table, $field3);
        // }
        $field5 = new xmldb_field('open_joindate');
        $field5->set_attributes(XMLDB_TYPE_CHAR, '512', null, null, null, null);
        if (!$dbman->field_exists($table, $field5)) {
            $dbman->add_field($table, $field5);
        }
        $field6 = new xmldb_field('open_dateofbirth');
        $field6->set_attributes(XMLDB_TYPE_CHAR, '512', null, null, null, null);
        if (!$dbman->field_exists($table, $field6)) {
            $dbman->add_field($table, $field6);
        }
        $field7 = new xmldb_field('gender');
        $field7->set_attributes(XMLDB_TYPE_CHAR, '512', null, null, null, null);
        if (!$dbman->field_exists($table, $field7)) {
            $dbman->add_field($table, $field7);
        }
        // $field8 = new xmldb_field('open_employmenttype');
        // $field8->set_attributes(XMLDB_TYPE_CHAR, '512', null, null, null, null);
        // if (!$dbman->field_exists($table, $field8)) {
        //     $dbman->add_field($table, $field8);
        // }
        $prefix = new xmldb_field('open_prefix');
        $prefix->set_attributes(XMLDB_TYPE_CHAR, '512', null, null, null, null);
        if (!$dbman->field_exists($table, $prefix)) {
            $dbman->add_field($table, $prefix);
        }
        // $orgactive = new xmldb_field('open_orgactive');
        // $orgactive->set_attributes(XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0);
        // if (!$dbman->field_exists($table, $orgactive)) {
        //     $dbman->add_field($table, $orgactive);
        // }

        $project = new xmldb_field('open_project');
        $project->set_attributes(XMLDB_TYPE_CHAR, '225', null, null, null, null);
        if (!$dbman->field_exists($table, $project)) {
            $dbman->add_field($table, $project);
        }
    }
    $table = new xmldb_table('local_uniquelogins');

    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('day', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('month', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('year', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('count_date', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}
