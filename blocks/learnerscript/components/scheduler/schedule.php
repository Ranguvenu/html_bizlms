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
 * @subpackage block_learnerscript
 */

require_once('../../../../config.php');

global $CFG, $DB, $USER, $OUTPUT;

require_once($CFG->dirroot . '/blocks/learnerscript/components/scheduler/schedule_form.php');
use block_learnerscript\local\ls;
use block_learnerscript\local\schedule;
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->css('/blocks/learnerscript/css/select2.min.css', true);
$PAGE->requires->css('/blocks/learnerscript/css/jquery.dataTables.min.css', true);
//$PAGE->requires->js('/blocks/learnerscript/js/highcharts/highcharts.js');

$reportid = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$scheduledreportid = optional_param('scheduleid', -1, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_BOOL);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();

if ($courseid == SITEID) {
	require_login();
	$context = context_system::instance();
} else {
	require_login($courseid);
	$context = context_course::instance($courseid);
}
$PAGE->set_context($context);
$PAGE->set_url('/blocks/learnerscript/components/scheduler/schedule.php', array('id' => $reportid));
$PAGE->set_pagelayout('admin');

//$PAGE->set_title(get_string('schedulereport', 'block_learnerscript'));
$PAGE->requires->js_call_amd('block_learnerscript/schedule', 'ScheduledTimings', array(array('reportid' => $reportid, 'courseid' => $courseid, 'action' => 'scheduledtimings')));

if ($scheduledreportid > 0) {
	if (!($scheduledreport = $DB->get_record('block_ls_schedule', array('id' => $scheduledreportid)))) {
		print_error('invalidscheduledreportid', 'block_learnerscript');
	}
}

if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
	print_error('reportdoesnotexists', 'block_learnerscript');
}

$PAGE->navbar->add(get_string('managereports', 'block_learnerscript'), new moodle_url('/blocks/learnerscript/managereport.php', array('courseid' => $courseid)));
$PAGE->navbar->add($report->name, new moodle_url('/blocks/learnerscript/viewreport.php',
					array('id' => $reportid, 'courseid' => $courseid)));
$PAGE->navbar->add(get_string('schedulereport', 'block_learnerscript'));
$PAGE->set_heading($report->name);

if (!has_capability('block/learnerscript:managereports', $context) && !has_capability('block/learnerscript:manageownreports', $context)) {
	print_error('permissiondenied');
}

$renderer = $PAGE->get_renderer('block_learnerscript');
if ($report->type) {
	require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
} else {
	print_error('reporttypeerror', 'block_learnerscript');
}

$reportclassname = 'report_' . $report->type;
$properties = new stdClass();
$reportclass = new $reportclassname($report, $properties);

if (!$reportclass->check_permissions($USER->id, $context)) {
	print_error("badpermissions", 'block_learnerscript');
}
$returnurl = new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php', array('id' => $reportid, 'courseid' => $courseid));

if ($delete) {
	$PAGE->url->param('delete', 1);
	if ($confirm and confirm_sesskey()) {
		$DB->delete_records('block_ls_schedule', array('id' => $scheduledreportid));
		$_SESSION['ls_ele_delete'] = $confirm;
		redirect($returnurl);
	}
	$strheading = get_string('deletescheduledreport', 'block_learnerscript');
	$PAGE->navbar->add($strheading);
	$PAGE->set_title($strheading);
	echo $OUTPUT->header();
	echo '<script src="'.$CFG->wwwroot . '/blocks/learnerscript/js/highcharts/highcharts.js"></script>';
	echo $OUTPUT->heading($strheading);
	$yesurl = new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php', array('id' => $reportid, 'courseid' => $courseid, 'scheduleid' => $scheduledreportid, 'confirm' => 1, 'sesskey' => sesskey(), 'delete' => 1));
	$message = get_string('delconfirm', 'block_learnerscript');
	echo $OUTPUT->confirm($message, $yesurl, $returnurl);
	echo $OUTPUT->footer();
	die;
}
$scheduling = new schedule();
/**  Form data **/
$roles_list = $scheduling->reportroles('', $reportid);
list($schusers, $schusersids) = $scheduling->userslist($reportid, $scheduledreportid);
$exportoptions = (new ls)->cr_get_export_plugins();
$frequencyselect = $scheduling->get_options();
if (!empty($scheduledreport)) {
	$schedule_list = $scheduling->getschedule($scheduledreport->frequency);
} else {
	$schedule_list = array(null => '--SELECT--');
}

$schrecords = $DB->get_records("block_ls_schedule",  array('reportid' => $reportid));
if (empty($schrecords)) {
	$collapse = false;
} else {
	$collapse = true;
} 

$organizationid = isset($scheduledreport->organizationid) ? $scheduledreport->organizationid : 0;
$departmentid = isset($scheduledreport->departmentid) ? $scheduledreport->departmentid : 0;
$subdepartmentid = isset($scheduledreport->subdepartmentid) ? $scheduledreport->subdepartmentid : 0;
$mform = new scheduled_reports_form($returnurl, array('id' => $reportid,
												'schusers' => $schusers,
												'scheduleid' => $scheduledreportid,
												'roles_list' => $roles_list,
	 											'schusersids' => $schusersids,
	 											'exportoptions' => $exportoptions,
	 											'schedule_list' => $schedule_list,
	 											'frequencyselect' => $frequencyselect,
	 											'organizationid' => $organizationid,
	 											'departmentid' => $departmentid,
	 											'subdepartmentid' => $subdepartmentid,
	 											'reportfilters' => $reportclass->basicparams));
if ($scheduledreportid > 0) {

	$collapse = false;

	$scheduledreport->users_data = explode(',', $scheduledreport->sendinguserid);
	$scheduledreport->role = explode(',', $scheduledreport->roleid);
	if (count($scheduledreport->users_data) > 10) {
		$scheduledreport->users_data = $scheduledreport->users_data + array(-1 => -1);
	}
	$scheduledreport->frequency = array($scheduledreport->frequency, $scheduledreport->schedule); 
	$scheduledreport->filter_organization = $scheduledreport->organizationid;
	$scheduledreport->filter_departments = $scheduledreport->departmentid;
	$mform->set_data($scheduledreport);
}

if ($mform->is_cancelled()) {
	redirect(new moodle_url('/blocks/learnerscript/viewreport.php', array('id' => $reportid, 'courseid' => $courseid)));
} else if ($fromform = $mform->get_data()) {
	$data = data_submitted();
	// $role = implode(',', $data->role);
	$fromform->roleid = $data->role;
	$fromform->sendinguserid = $data->schuserslist;
	$fromform->exportformat = $data->exportformat;
	$fromform->frequency = $data->frequency;
	$fromform->schedule = $data->schedule;
	$fromform->userid = $USER->id; 
	$fromform->costcenterid = !empty($data->filter_organization) ? $data->filter_organization : 0;
	$fromform->departmentid = !empty($data->filter_departments) ? $data->filter_departments : 0;
	$fromform->subdepartment = !empty($data->filter_subdepartment) ? $data->filter_subdepartment : 0;
	if (empty($data->contextlevel) AND empty($fromform->contextlevel))
	$fromform->contextlevel = CONTEXT_SYSTEM; // implicitly for BIZLMS
	$fromform->nextschedule = $scheduling->next($fromform, null, false);
	if ($scheduledreportid > 0) {
		$fromform->timemodified = time();
		$fromform->id = $fromform->scheduleid;
		$schedule = $DB->update_record('block_ls_schedule', $fromform);
		$collapse = true;
	} else {
		$fromform->timecreated = time();
		$fromform->timemodified = 0;
		$schedule = $DB->insert_record('block_ls_schedule', $fromform);
		$event = \block_learnerscript\event\schedule_report::create(array(
				    'objectid' => $fromform->reportid,
				    'context' => $context
				));
		$event->trigger();
	}

	if ($schedule) {
		if ($schedule == 1) {
			$_SESSION['ls_ele_update'] = $schedule;
		} else {
			$_SESSION['ls_ele_schedule'] = $schedule;
		}
		redirect($returnurl);
	}
}
echo $OUTPUT->header();
echo '<script src="'.$CFG->wwwroot . '/blocks/learnerscript/js/highcharts/highcharts.js"></script>';
//echo $OUTPUT->heading($report->name);

if (isset($_SESSION['ls_ele_schedule']) && $_SESSION['ls_ele_schedule']) {
	echo $OUTPUT->notification(get_string('reportschedule', 'block_learnerscript'), 'notifysuccess');
	unset($_SESSION['ls_ele_schedule']);
}
if (isset($_SESSION['ls_ele_delete']) && $_SESSION['ls_ele_delete']) {
	echo $OUTPUT->notification(get_string('deleteschedulereport', 'block_learnerscript'), 'notifysuccess');
	unset($_SESSION['ls_ele_delete']);
}
if (isset($_SESSION['ls_ele_update']) && $_SESSION['ls_ele_update']) {
	echo $OUTPUT->notification(get_string('updateschedulereport', 'block_learnerscript'), 'notifysuccess');
	unset($_SESSION['ls_ele_update']);
}

if (has_capability('block/learnerscript:managereports', $context) ||
	(has_capability('block/learnerscript:manageownreports', $context)) && $report->ownerid == $USER->id) {
    $plots = (new block_learnerscript\local\ls)->get_components_data($report->id, 'plot');
   	$calcbutton = false;
	$plotoptions = new \block_learnerscript\output\plotoption(false, $report->id, $calcbutton, 'schreportform');
	echo $renderer->render($plotoptions);
}
echo "<div  class='bulkupload'><a href='" . $CFG->wwwroot . "/blocks/learnerscript/components/scheduler/sch_upload.php?id=$reportid&courseid=" . $courseid . "'><button>Bulk Upload</button></a></div>";

if($scheduledreportid > 0){
	$schreport = get_string('editscheduledreport', 'block_learnerscript');
} else {
	$schreport = get_string('addschedulereport', 'block_learnerscript');
}

print_collapsible_region_start(' ', 'scheduleform', ' '.' '.$schreport , false, $collapse);
$mform->display();
print_collapsible_region_end();

echo $renderer->schedulereportsdata($reportid, $courseid, true);
echo $OUTPUT->footer();
