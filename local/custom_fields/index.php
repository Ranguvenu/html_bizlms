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
 * @package BizLMS
 * @subpackage local_custom_fields
 */
require_once(dirname(__FILE__) . '/../../config.php');
global $USER, $CFG, $PAGE, $OUTPUT, $DB;
require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/local/custom_fields/lib.php');
$categorycontext = (new \local_custom_fields\lib\accesslib())::get_module_context();

$id = optional_param('id', 0, PARAM_INT);
$parentcatid = optional_param('parentcatid', 0, PARAM_INT);
$delete_id = optional_param('delete_id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);
$submitbutton = optional_param('submitbutton', '', PARAM_RAW);
require_login();

if (!has_capability('local/custom_fields:view_custom_fields', $categorycontext)) {
    print_error('nopermissiontoviewpage');
}
$PAGE->navbar->add(get_string('dashboard', 'local_costcenter'), new moodle_url('/my/dashboard.php'));
if ($parentcatid > 0) {
    $querylib = new \local_custom_fields\querylib();
    $childcatname = $querylib->customfield_field('fullname', array('id' => $parentcatid));
    $PAGE->set_url('/local/custom_fields/index.php?parentcatid=' . $parentcatid);
    $PAGE->set_heading($childcatname);
    $PAGE->navbar->add(get_string('manage_custom_fields', 'local_custom_fields'), new moodle_url('/local/custom_fields/index.php'));
    $PAGE->navbar->add($childcatname);
} else {
    $PAGE->set_heading(get_string('manage_custom_fields', 'local_custom_fields'));
    $PAGE->set_url('/local/custom_fields/index.php');
    $PAGE->navbar->add(get_string('manage_custom_fields', 'local_custom_fields'));
}
$PAGE->set_context($categorycontext);
$PAGE->set_pagelayout('standard');

$PAGE->set_title(get_string('pluginname', 'local_custom_fields'));
$PAGE->requires->js_call_amd('local_custom_fields/newcustomcategory', 'load', array());

$renderer = $PAGE->get_renderer('local_custom_fields');
$filterparams = $renderer->custom_fields_content($parentcatid, true);

echo $OUTPUT->header();
if (is_siteadmin() || has_capability('local/custom_fields:create_custom_fields', $categorycontext)) {
    echo $renderer->get_top_action_buttons_custom_fields($parentcatid);
}
$filterparams['submitid'] = 'form#filteringform';
echo $OUTPUT->render_from_template('local_costcenter/global_filter', $filterparams);
echo $renderer->custom_fields_content($parentcatid);
echo $OUTPUT->footer();
