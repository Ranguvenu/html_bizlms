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

/**
 * script for downloading admissions
 */
require_once '../../../../config.php';
require_once $CFG->libdir . '/adminlib.php';
$format = optional_param('format', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
if ($format) {
	$fields = array(
		'email' => 'Email',
		'exportformat' => 'Export Format',
		'exporttofilesystem' => 'Export to filesystem',
		'frequency' => 'Frequency',
		'schedule' => 'Schedule',
		'roleid' => 'Role',
	);

	switch ($format) {
	case 'csv':
		user_download_csv($fields, $id);
		break;
	}
	die;
}
function user_download_csv($fields, $reportid) {
	global $CFG, $DB;
	require_once $CFG->libdir . '/csvlib.class.php';
	$reportname = $DB->get_field('block_learnerscript', 'name', array('id' => $reportid));
	$filename = $reportname;
	$csvexport = new csv_export_writer();
	$csvexport->set_filename($filename);
	$csvexport->add_data($fields);
	$userprofiledatadaily = array(get_string('dailysampleinfo', 'block_learnerscript'));
	$userprofiledata = array('user@mailinator.com', 'csv', 'Send report to mail', 'daily', '14', '2');
	$userprofiledata1 = array('user@mailinator.com', 'pdf', 'Save to file system', 'daily', '15', '2');
	$userprofiledata2 = array('user@mailinator.com', 'ods', 'Save to file system and send email', 'daily', '2', '2');
	$userprofiledataweekly = array(get_string('weeklysampleinfo', 'block_learnerscript'));
	$userprofiledata3 = array('user@mailinator.com', 'xls', 'Send report to mail', 'weekly', 'Sun', '2');
	$userprofiledata4 = array('user@mailinator.com', 'csv', 'Save to file system', 'weekly', 'Mon', '3');
	$userprofiledata5 = array('user@mailinator.com', 'csv', 'Save to file system and send email', 'weekly', 'Tue', '2');
	$userprofiledata6 = array('user@mailinator.com', 'pdf', 'Send report to mail', 'weekly', 'Wed', '5');
	$userprofiledata7 = array('user@mailinator.com', 'ods', 'Save to file system', 'weekly', 'Thu', '5');
	$userprofiledata8 = array('user@mailinator.com', 'xls', 'Send report to mail', 'weekly', 'Fri', '4');
	$userprofiledata9 = array('user@mailinator.com', 'csv', 'Save to file system', 'weekly', 'Sat', '2');
	$userprofiledatamonthly = array(get_string('monthlysampleinfo', 'block_learnerscript'));
	$userprofiledata10 = array('user@mailinator.com', 'pdf', 'Send report to mail', 'monthly', '1', '3');
	$userprofiledata11 = array('user@mailinator.com', 'xls', 'Save to file system and send email', 'monthly', '2', '2');
	$userprofiledata12 = array('user@mailinator.com', 'ods', 'Send report to mail', 'monthly', '3', '3');
	$userprofiledata13 = array('user@mailinator.com', 'csv', 'Save to file system', 'monthly', '17', '1');
	$userprofiledata14 = array('user@mailinator.com', 'pdf', 'Send report to mail', 'monthly', '30', '2');
	$userprofiledataexample = array(get_string('mandatoryinfo', 'block_learnerscript'));

	// Sample data
	$csvexport->add_data($userprofiledatadaily);
	$csvexport->add_data($userprofiledata);
	$csvexport->add_data($userprofiledata1);
	$csvexport->add_data($userprofiledata2);
	$csvexport->add_data($userprofiledataweekly);
	$csvexport->add_data($userprofiledata3);
	$csvexport->add_data($userprofiledata4);
	$csvexport->add_data($userprofiledata5);
	$csvexport->add_data($userprofiledata6);
	$csvexport->add_data($userprofiledata7);
	$csvexport->add_data($userprofiledata8);
	$csvexport->add_data($userprofiledata9);
	$csvexport->add_data($userprofiledatamonthly);
	$csvexport->add_data($userprofiledata10);
	$csvexport->add_data($userprofiledata11);
	$csvexport->add_data($userprofiledata12);
	$csvexport->add_data($userprofiledata13);
	$csvexport->add_data($userprofiledata14);
	$csvexport->add_data($userprofiledataexample);

	$csvexport->download_file();
	die;
}