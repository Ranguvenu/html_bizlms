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

/** LearnerScript
 * A Moodle block for creating LearnerScript
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
namespace block_learnerscript\local;
require_once($CFG->dirroot . '/lib/evalmath/evalmath.class.php');
require_once($CFG->dirroot . "/course/lib.php");
use stdclass;
use DateTime;
use DateTimeZone;
use core_date;
use context_system;
use context_course;
use context_module;
use core_course_category;
use highreports;
use block_learnerscript\local\schedule;
use EvalMath;

define('DAILY', 1);
define('WEEKLY', 2);
define('MONTHLY', 3);
define('ONDEMAND', -1);

define('OPENSANS', 1);
define('PTSANS', 2);
/**
 * [urlencode_recursive description]
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
class ls {
	/**
	 * [add_report description]
	 * @param [type] $data    [description]
	 * @param [type] $context [description]
	 */
	public function add_report($data, $context){
		global $CFG,$DB;
		if (!$lastid = $DB->insert_record('block_learnerscript', $data)) {
			print_error('errorsavingreport', 'block_learnerscript');
		} else {
			$event = \block_learnerscript\event\create_report::create(array(
			    'objectid' => $lastid,
			    'context' => $context
			));
			$event->trigger();
			$data->id = $lastid;
			if(in_array($data->type, array('sql','statistics'))){
				self::update_report_sql($data);
			}
		}
		return $lastid;
	}
	/**
	 * [update_report description]
	 * @param  [type] $data    [description]
	 * @param  [type] $context [description]
	 * @return [type]          [description]
	 */
	public function update_report($data,$context){
		global $DB;
		$data->global = $data->global ? $data->global : 0;
		$data->disabletable = isset($data->disabletable) ? $data->disabletable : 0;
		$data->summary = isset($data->summary->text) ? $data->summary->text : '';
		if (!$DB->update_record('block_learnerscript', $data)) {
			print_error('errorsavingreport', 'block_learnerscript');
		} else {
	        $event = \block_learnerscript\event\update_report::create(array(
				    'objectid' => $data->id,
				    'context' => $context
				));
			$event->trigger();
			if(in_array($data->type, array('sql','statistics'))){
				self::update_report_sql($data);
			}
		}
	}
	/**
	 * [delete_report description]
	 * @param  [type] $report  [description]
	 * @param  [type] $context [description]
	 * @return [type]          [description]
	 */
	public function delete_report($report,$context){
		global $DB;
		if ($DB->delete_records('block_learnerscript', array('id' => $report->id))) {
			if ($DB->delete_records('block_ls_schedule', array('reportid' => $report->id))) {
				$event = \block_learnerscript\event\delete_report::create(array(
				    'objectid' => $report->id,
				    'context' => $context
				));
				$event->add_record_snapshot('role_assignments', $report);
				$event->trigger();
			}
		}
	}
	/**
	 * [update_report_sql description]
	 * @param  [type] $report [description]
	 * @return [type]         [description]
	 */
	public function update_report_sql($report){
		global $CFG,$DB;
		require_once $CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php';
		$reportclassname = 'report_' . $report->type;
		$reportproperties = new stdclass();
		$reportclass = new $reportclassname($report->id, $reportproperties);
		$components = self::cr_unserialize($reportclass->config->components);
	    $components['customsql']['config'] = $report;
	    $reportclass->config->components = (new ls)->cr_serialize($components);
	    $DB->update_record('block_learnerscript', $reportclass->config);
	}
	/**
	 * [generate_report_plot description]
	 * @param  [type] $reportclass   [description]
	 * @param  [type] $graphdata     [description]
	 * @param  [type] $blockinstance [description]
	 * @return [type]                [description]
	 */
	public function generate_report_plot($reportclass, $graphdata, $blockinstanceid = null) {
			global $CFG, $PAGE;
			$components = (new ls)->cr_unserialize($reportclass->config->components);
			$seriesvalues = (isset($components['plot']['elements'])) ? $components['plot']['elements'] : array();
			require_once($CFG->dirroot . '/blocks/learnerscript/components/highcharts/graphicalreport.php');
			$highcharts = new highreports();
			if (!empty($seriesvalues)) {
				$series = array();
				switch ($graphdata['pluginname']) {
				case 'pie':
					return $highcharts->piechart($reportclass->finalreport->table->data, $graphdata, $reportclass->config,null, $reportclass->finalreport->table->head);
					break;
				case 'line':
					return $highcharts->lbchart($reportclass->finalreport->table->data, $graphdata, $reportclass->config, 'spline', $blockinstanceid, $reportclass->finalreport->table->head);
					break;
				case 'bar':
					return $highcharts->lbchart($reportclass->finalreport->table->data, $graphdata, $reportclass->config, 'bar', $blockinstanceid, $reportclass->finalreport->table->head);
					break;
				case 'column':
					return $highcharts->lbchart($reportclass->finalreport->table->data, $graphdata, $reportclass->config, 'column', $blockinstanceid, $reportclass->finalreport->table->head);
					break;
				case 'combination':
					return $highcharts->combination_chart($reportclass->finalreport->table->data, $graphdata, $reportclass->config, 'combination', $blockinstanceid, $reportclass->finalreport->table->head, $seriesvalues);
					break;
				case 'worldmap':
					return $highcharts->worldmap($reportclass->finalreport->table->data, $graphdata, $reportclass->config,null, $reportclass->finalreport->table->head);
					break;
				case 'treemap':
					return $highcharts->treemap($reportclass->finalreport->table->data, $graphdata, $reportclass->config,'treemap', $reportclass->finalreport->table->head);
				break;
				}
			}
			return true;
		}
		/**
		 * [get_components_data description]
		 * @param  [type] $reportid  [description]
		 * @param  [type] $component [description]
		 * @return [type]            [description]
		 */
		public function get_components_data($reportid, $component) {
			global $CFG, $DB;

			if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
				print_error(get_string('noreportexists', 'block_learnerscript'));
			}
			$elements = (new ls)->cr_unserialize($report->components);
			$elements = isset($elements[$component]['elements']) ? $elements[$component]['elements'] : array();

			require_once $CFG->dirroot . '/blocks/learnerscript/components/' . $component . '/component.class.php';
			$componentclassname = 'component_' . $component;
			$compclass = new $componentclassname($report->id);
			if ($compclass->plugins) {
				$currentplugins = array();
				if ($elements) {
					foreach ($elements as $e) {
						$currentplugins[] = $e['pluginname'];
					}
				}
				$plugins = get_list_of_plugins('blocks/learnerscript/components/' . $component);
				$optionsplugins = array();
				foreach ($plugins as $p) {
					require_once $CFG->dirroot . '/blocks/learnerscript/components/' . $component . '/' . $p . '/plugin.class.php';
					$pluginclassname = 'plugin_' . $p;
					$pluginclass = new $pluginclassname($report);
					if (in_array($report->type, $pluginclass->reporttypes)) {
						if ($pluginclass->unique && in_array($p, $currentplugins)) {
							continue;
						}
						$optionsplugins[] = array('shortname'=>$p,'fullname'=>ucfirst($p));
					}
				}
				asort($optionsplugins);
			}
			return $optionsplugins;
		}
		/**
		 * [report_tabledata description]
		 * @param  [type] $table [description]
		 * @return [type]        [description]
		 */
		public function report_tabledata($table) {
		global $COURSE, $PAGE, $OUTPUT;
			if (isset($table->align)) {
				foreach ($table->align as $key => $aa) {
					if ($aa) {
						$align[$key] = ' text-align:' . fix_align_rtl($aa) . ';'; // Fix for RTL languages
					} else {
						$align[$key] = '';
					}
				}
			}
			if (isset($table->size)) {
				foreach ($table->size as $key => $ss) {
					if ($ss) {
						$size[$key] = ' width:' . $ss . ';';
					} else {
						$size[$key] = '';
					}
				}
			}
			if (isset($table->wrap)) {
				foreach ($table->wrap as $key => $ww) {
					if ($ww) {
						$wrap[$key] = ($ww == 'wrap') ? 'word-break:break-all;' : 'word-break:normal;';
					} else {
						$wrap[$key] = 'word-break:normal;';
					}
				}
			}
			if (empty($table->width)) {
				$table->width = '100%';
			}

			if (empty($table->tablealign)) {
				$table->tablealign = 'center';
			}

			if (!isset($table->cellpadding)) {
				$table->cellpadding = '5';
			}

			if (!isset($table->cellspacing)) {
				$table->cellspacing = '1';
			}

			if (empty($table->class)) {
				$table->class = 'generaltable';
			}

			$tableid = empty($table->id) ? '' : 'id="' . $table->id . '"';
			$countcols = 0;
			$isuserid = -1;
			$countcols = count($table->head);
			$keys = array_keys($table->head);
			$lastkey = end($keys);
			$tableheadkeys = array_keys($table->head);
			foreach ($table->head as $key => $heading) {
				$k = array_search($key, $tableheadkeys);
				$size[$key] = isset($size[$k]) ? $size[$k] : null;
				$wrap[$key] = isset($wrap[$k]) ? $wrap[$k] : 'word-break:normal;';
				$align[$key] = isset($align[$k]) ? $align[$k] : null;
				$tablehead[] = array('key'=>$key,
									 'heading'=>$heading,
									 'size' => $size[$k],
									 'wrap' => $wrap[$k],
									 'align' => $align[$k]);
			}
			$tableproperties = array('width' => $table->width,
								    // 'summary' => $table->summary,
								    'tablealign' => $table->tablealign,
								    'cellpadding' => $table->cellpadding,
								    'cellspacing' => $table->cellspacing,
								    'class' => $table->class);

			return compact('tablehead','tableproperties');
	}
	/**
	 * [urlencode_recursive description]
	 * @param  [type] $var [description]
	 * @return [type]      [description]
	 */
	public function urlencode_recursive($var) {
		if (is_object($var)) {
			$new_var = new stdClass();
			$properties = get_object_vars($var);
			foreach ($properties as $property => $value) {
				$new_var->$property = (new self)->urlencode_recursive($value);
			}
		} else if (is_array($var)) {
			$new_var = array();
			foreach ($var as $property => $value) {
				$new_var[$property] = (new self)->urlencode_recursive($value);
			}
		} else if (is_string($var)) {
			$new_var = urlencode($var);
		} else {
			// nulls, integers, etc.
			$new_var = $var;
		}

		return $new_var;
	}
	/**
	 * [urldecode_recursive description]
	 * @param  [type] $var [description]
	 * @return [type]      [description]
	 */
	public function urldecode_recursive($var) {
		if (is_object($var)) {
			$new_var = new stdClass();
			$properties = get_object_vars($var);
			foreach ($properties as $property => $value) {
				$new_var->$property = self::urldecode_recursive($value);
			}
		} else if (is_array($var)) {
			$new_var = array();
			foreach ($var as $property => $value) {
				$new_var[$property] = self::urldecode_recursive($value);
			}
		} else if (is_string($var)) {
			$new_var = urldecode($var);
		} else {
			$new_var = $var;
		}

		return $new_var;
	}
	/**
	 * [cr_get_my_reports description]
	 * @param  [type]  $courseid   [description]
	 * @param  [type]  $userid     [description]
	 * @param  boolean $allcourses [description]
	 * @return [type]              [description]
	 */
	public function cr_get_my_reports($courseid, $userid, $allcourses = true) {
		global $DB;

		$reports = array();
		if ($courseid == SITEID) {
			$context = context_system::instance();
		} else {
			$context = context_course::instance($courseid);
		}
		if (has_capability('block/learnerscript:managereports', $context, $userid)) {
			if ($courseid == SITEID && $allcourses) {
				$reports = $DB->get_records('block_learnerscript', null, 'name ASC');
			} else {
				$reports = $DB->get_records('block_learnerscript', array('courseid' => $courseid), 'name ASC');
			}

		} else {
			$reports = $DB->get_records_select('block_learnerscript', 'ownerid = ? AND courseid = ? ORDER BY name ASC', array($userid, $courseid));
		}
		return $reports;
	}
	/**
	 * [cr_serialize description]
	 * @param  [type] $var [description]
	 * @return [type]      [description]
	 */
	public function cr_serialize($var) {
		return serialize((new self)->urlencode_recursive($var));
	}
	/**
	 * [cr_unserialize description]
	 * @param  [type] $var [description]
	 * @return [type]      [description]
	 */
	public function cr_unserialize($var) {
		return (!empty($var)) ? (new self)->urldecode_recursive(unserialize($var)) : '';
	}
	/**
	 * [cr_check_report_permissions description]
	 * @param  [type] $report  [description]
	 * @param  [type] $userid  [description]
	 * @param  [type] $context [description]
	 * @return [type]          [description]
	 */
	public function cr_check_report_permissions($report, $userid, $context) {
		global $DB, $CFG;

		require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
		$properties = new stdClass();
		$properties->courseid = $report->id;
		$properties->start = 0;
		$properties->length = 1;
		$properties->search = '';
		$properties->ls_startdate = 0;
		$properties->ls_enddate = time();
		$properties->filters = array();
		$classn = 'report_' . $report->type;
		$classi = new $classn($report->id, $properties);
		return $classi->check_permissions($userid, $context);
	}
	/**
	 * [cr_get_report_plugins description]
	 * @param  [type] $courseid [description]
	 * @return [type]           [description]
	 */
	public function cr_get_report_plugins($courseid) {
		$pluginoptions = array();
		$context = ($courseid == SITEID) ? context_system::instance() : context_course::instance($courseid);
		$plugins = get_list_of_plugins('blocks/learnerscript/reports');
		if ($plugins) {
			foreach ($plugins as $p) {
				if ($p == 'sql' && !has_capability('block/learnerscript:managesqlreports', $context)) {
					continue;
				}

				$pluginoptions[$p] = get_string('report_' . $p, 'block_learnerscript');
			}
		}
		return $pluginoptions;
	}
	/**
	 * [cr_get_export_plugins description]
	 * @return [type] [description]
	 */
	public function cr_get_export_plugins() {
		$exportoptions = array();
		$plugins = get_list_of_plugins('blocks/learnerscript/export');
		if ($plugins) {
			foreach ($plugins as $p) {
				$pluginoptions[$p] = get_string('export_' . $p, 'block_learnerscript');
			}
		}
		return $pluginoptions;
	}
	/**
	 * [cr_get_export_options description]
	 * @param  [type] $reportid [description]
	 * @return [type]           [description]
	 */
	public function cr_get_export_options($reportid) {
		global $DB;
		$reportconfig = $DB->get_record('block_learnerscript', array('id' => $reportid));
		if ($reportconfig->export) {
			$export_options = array_filter(explode(',', $reportconfig->export));
		} else {
			$export_options = false;
		}
		return $export_options;
	}
	/**
	 * [table_to_excel description]
	 * @param  [type] $filename [description]
	 * @param  [type] $table    [description]
	 * @return [type]           [description]
	 */
	public function table_to_excel($filename, $table) {
		global $DB, $CFG;
		require_once $CFG->dirroot . '/lib/excellib.class.php';

		if (!empty($table->head)) {
			$countcols = count($table->head);
			$keys = array_keys($table->head);
			$lastkey = end($keys);
			foreach ($table->head as $key => $heading) {
				$matrix[0][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($heading))));
			}
		}

		if (!empty($table->data)) {
			foreach ($table->data as $rkey => $row) {
				foreach ($row as $key => $item) {
					$matrix[$rkey + 1][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($item))));
				}
			}
		}

		$downloadfilename = clean_filename($filename);
		/// Creating a workbook
		$workbook = new MoodleExcelWorkbook("-");
		/// Sending HTTP headers
		$workbook->send($downloadfilename);
		/// Adding the worksheet
		$myxls = &$workbook->add_worksheet($filename);

		foreach ($matrix as $ri => $col) {
			foreach ($col as $ci => $cv) {
				$myxls->write_string($ri, $ci, $cv);
			}
		}

		$workbook->close();
		exit;
	}

	/**
	 * Returns contexts in deprecated and current modes
	 *
	 * @param  int $context The context
	 * @param  int $id      The context id
	 * @param  int $flags   The flags to be used
	 * @return stdClass     An object instance
	 */
	public function cr_get_context($context, $id = null, $flags = null) {

		if ($context == CONTEXT_SYSTEM) {
			if (class_exists('context_system')) {
				return context_system::instance();
			} else {
				return get_context_instance(CONTEXT_SYSTEM);
			}
		} else if ($context == CONTEXT_COURSE) {
			if (class_exists('context_course')) {
				return context_course::instance($id, $flags);
			} else {
				return get_context_instance($context, $id, $flags);
			}
		} else if ($context == CONTEXT_COURSECAT) {
			if (class_exists('context_coursecat')) {
				return context_coursecat::instance($id, $flags);
			} else {
				return get_context_instance($context, $id, $flags);
			}
		} else if ($context == CONTEXT_BLOCK) {
			if (class_exists('context_block')) {
				return context_block::instance($id, $flags);
			} else {
				return get_context_instance($context, $id, $flags);
			}
		} else if ($context == CONTEXT_MODULE) {
			if (class_exists('context_module')) {
				return get_context_instance::instance($id, $flags);
			} else {
				return get_context_instance($context, $id, $flags);
			}
		} else if ($context == CONTEXT_USER) {
			if (class_exists('context_user')) {
				return context_user::instance($id, $flags);
			} else {
				return get_context_instance($context, $id, $flags);
			}
		}

		return get_context_instance($context, $id, $flags);
	}
	/**
	 * [cr_make_categories_list description]
	 * @param  [type]  &$list              [description]
	 * @param  [type]  &$parents           [description]
	 * @param  string  $requiredcapability [description]
	 * @param  integer $excludeid          [description]
	 * @param  [type]  $category           [description]
	 * @param  string  $path               [description]
	 * @return [type]                      [description]
	 */
	public function cr_make_categories_list(&$list, &$parents, $requiredcapability = '', $excludeid = 0, $category = NULL, $path = "") {
		global $CFG, $DB;
		// require_once $CFG->libdir . '/coursecatlib.php';

		// For categories list use just this one public function:
		if (empty($list)) {
			$list = array();
		}
		$list += core_course_category::make_categories_list($requiredcapability, $excludeid);

		// Building the list of all parents of all categories in the system is highly undesirable and hardly ever needed.
		// Usually user needs only parents for one particular category, in which case should be used:
		// coursecat::get($categoryid)->get_parents()
		if (empty($parents)) {
			$parents = array();
		}
		$all = $DB->get_records_sql('SELECT id, parent FROM {course_categories} WHERE visible = 1  ORDER BY sortorder');
		foreach ($all as $record) {
			if ($record->parent) {
				$parents[$record->id] = array_merge($parents[$record->parent], array($record->parent));
			} else {
				$parents[$record->id] = array();
			}
		}
	}
	/**
	 * [cr_import_xml description]
	 * @param  [type]  $xml        [description]
	 * @param  [type]  $course     [description]
	 * @param  boolean $timeprefix [description]
	 * @return [type]              [description]
	 */
	public function cr_import_xml($xml, $course, $timeprefix = true, $config = false) {
		global $CFG, $DB, $USER, $PAGE;
		$context = context_system::instance();
		require_once($CFG->dirroot . '/lib/xmlize.php');
		$data = xmlize($xml, 1, 'UTF-8');
		if (isset($data['report']['@']['version'])) {
			$newreport = new stdclass;
			foreach ($data['report']['#'] as $key => $val) {
				if ($key == 'components') {
					$val[0]['#'] = base64_decode(trim($val[0]['#']));
					// fix url_encode " and ' when importing SQL queries
					$temp_components = (new self)->cr_unserialize($val[0]['#']);
					if(isset($temp_components['customsql'])){
						$temp_components['customsql']['config']->querysql = str_replace("\'", "'", $temp_components['customsql']['config']->querysql);
						$temp_components['customsql']['config']->querysql = str_replace('\"', '"', $temp_components['customsql']['config']->querysql);
					}
					$val[0]['#'] = (new self)->cr_serialize($temp_components);

				}
				$newreport->{$key} = $val[0]['#'];
			}
			$newreport->courseid = $course->id;
			$newreport->ownerid = $USER->id;
			if ($timeprefix) {
				$newreport->name .= " (" . userdate(time()) . ")";
			}
			try {
				$reportid = $DB->insert_record('block_learnerscript', $newreport);
				$event = \block_learnerscript\event\create_report::create(array(
				    'objectid' => $reportid,
				    'context' => $context
				));
				$event->trigger();
				if($config && $reportid)  {
					$PAGE->set_context($context);
					$regions = array('side-db-first', 'side-db-second', 'side-db-third', 'side-db-four', 'learning-first', 'learning-second', 'learning-third', 'learning-fourth', 'learning-fifth', 'learning-sixth', 'first-maindb', 'learner-first', 'learner-second', 'learner-third', 'learner-fourth', 'learner-fifth', 'learner-sixth', 'learner-maindb', 'ol-one', 'ol-second', 'ol-third', 'ol-fourth', 'ol-fifth', 'ol-sixth', 'reportdb-one', 'reportdb-second', 'reportdb-third', 'center-first', 'center-second', 'reports-db-one', 'reports-db-two', 'side-db-main', 'side-db-one', 'side-db-two', 'side-db-three', 'lp-one', 'lp-second', 'lp-third', 'lp-fourth', 'lp-fifth', 'lp-sixth', 'learn-one', 'learn-second', 'learn-third', 'lp-main', 'labs-one', 'labs-second', 'labs-third', 'labs-fourth', 'labs-fifth', 'labs-sixth', 'exam-one', 'exam-second', 'labs-db-main', 'assess-one', 'assess-second', 'assess-third', 'assess-fourth', 'assess-fifth', 'assess-sixth', 'certifi-one', 'certifi-second', 'certifi-third', 'assess-db-main', 'webinars-one', 'webinars-second', 'webinars-third', 'webinars-fourth', 'webinars-fifth', 'webinars-sixth', 'compliance-one', 'compliance-second', 'compliance-third', 'webinars-db-main', 'classroom-one', 'classroom-second', 'classroom-third', 'classroom-four', 'classroom-main', 'program-one', 'program-second', 'program-third', 'program-four', 'program-fifth', 'program-sixth', 'program-main', 'compliance-first', 'reportcert-one', 'reportcert-two', 'reportcert-third', 'reportcert-four', 'reportcert-fifth', 'cert-one', 'cert-second');
					$PAGE->blocks->add_regions($regions);
					$blocksinstancedata = isset($data['report']['#']['instance']) ? $data['report']['#']['instance'] : 0;
					$blockspositiondata = isset($data['report']['#']['position']) ? $data['report']['#']['position'] : 0;
					if(!empty($blocksinstancedata)) {
						foreach($blocksinstancedata as $k => $blockinstancedata) {
							if (isset($blockinstancedata['@']['version'])) {
								$blockinstance = new stdClass();
								foreach ($blockinstancedata['#'] as $key => $val) {
									$blockinstance->{$key} = trim($val[0]['#']);
								}
                                $blockexists = $PAGE->blocks->is_known_block_type($blockinstance->blockname, true);
                                if ($blockexists) {
                                    $blockconfig = new stdClass();
                                    $blockconfig->title = $blockinstance->title;
                                    $blockconfig->reportlist = $reportid;
                                    $blockconfig->reportcontenttype = $blockinstance->reportcontenttype;
                                    $blockconfig->reporttype = $blockinstance->reporttype;
                                    $blockconfig->logo = $blockinstance->logo;
                                    $blockconfig->tilescolourpicker = $blockinstance->tilescolourpicker;
                                    if ($blockinstance->blockname == 'reporttiles') {
                                    	$blockconfig->tileformat = $blockinstance->tileformat;
                                    } else if ($blockinstance->blockname == 'reportdashboard') {$blockconfig->disableheader = $blockinstance->disableheader;
                                    }
                                    $blockconfig->tilescolour = $blockinstance->tilescolour;
                                    $blockconfig->url = $blockinstance->url;
                                    $configdata = base64_encode(serialize($blockconfig));
                                    $PAGE->blocks->add_block($blockinstance->blockname, $blockinstance->defaultregion, $blockinstance->defaultweight, false, $blockinstance->pagetypepattern,
                                    	$blockinstance->subpagepattern);
                                    $lastblockinstanceid = $DB->get_field_sql("SELECT id from {block_instances} where blockname = '$blockinstance->blockname' order by id DESC", array(), IGNORE_MULTIPLE);
                                    $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $lastblockinstanceid));
                                    if($lastblockinstanceid) {
                                        if (isset($blockspositiondata[$k]['@']['version'])) {
                                            if(!empty($blockspositiondata[$k]['#'])) {
                                                $blockposition = new stdClass();
                                                $blockposition->blockinstanceid = $lastblockinstanceid;
                                                foreach ($blockspositiondata[$k]['#'] as $key => $val) {
                                                    $blockposition->{$key} = trim($val[0]['#']);
                                                }
                                                $DB->insert_record('block_positions', $blockposition);
                                            }
                                        }
                                    }
                                }
				            }
				        }
		        	}
				}
			} catch (dml_exception $ex) {
				return false;
			}
			return $reportid;
		}
		return false;
	}

	// For avoid warnings in versions minor than 2.7
	/**
	 * [cr_add_to_log description]
	 * @param  [type]  $courseid [description]
	 * @param  [type]  $module   [description]
	 * @param  [type]  $action   [description]
	 * @param  string  $url      [description]
	 * @param  string  $info     [description]
	 * @param  integer $cm       [description]
	 * @param  integer $user     [description]
	 * @return [type]            [description]
	 */
	public function cr_add_to_log($courseid, $module, $action, $url = '', $info = '', $cm = 0, $user = 0) {
		global $CFG;

		if ($CFG->version < 2014051200) {
			add_to_log($courseid, $module, $action, $url, $info, $cm, $user);
		}
	}
	/**
	 * [cr_get_reportinstance description]
	 * @param  [type] $reportid [description]
	 * @return [type]           [description]
	 */
	public function cr_get_reportinstance($reportid) {
		global $DB;
		if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
			print_error('reportdoesnotexists', 'block_learnerscript');
		}
		return $report;
	}
	/**
	 * [create_reportclass description]
	 * @param  [type]  $reportid         [description]
	 * @param  boolean $reportproperties [description]
	 * @return [type]                    [description]
	 */
	public function create_reportclass($reportid, $reportproperties = false) {
		global $CFG, $DB;
		$report = (new self)->cr_get_reportinstance($reportid);
		require_once $CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php';
		$reportclassname = 'report_' . $report->type;
		$reportclass = new $reportclassname($report, $reportproperties);
		if($reportproperties){
			isset($reportproperties->courseid) ? $reportclass->courseid = $reportproperties->courseid: null;
			isset($reportproperties->ls_startdate) ? $reportclass->ls_startdate = $reportproperties->ls_startdate: null;
			isset($reportproperties->ls_enddate) ? $reportclass->ls_enddate = $reportproperties->ls_enddate: null;
		}
		return $reportclass;
	}
	/**
	 * [cr_listof_reporttypes description]
	 * @param  [type]  $reportid       [description]
	 * @param  boolean $checktable     [description]
	 * @param  boolean $component_data [description]
	 * @return [type]                  [description]
	 */
	public function cr_listof_reporttypes($reportid, $checktable = true, $component_data = true) {
		global $DB;
		// $reportclass = (new self)->create_reportclass($reportid);
		$reportcomponents = $DB->get_field('block_learnerscript', 'components', array('id' => $reportid));
		$components = (new self)->cr_unserialize($reportcomponents);

		$reportcontenttypes = array();
		if (isset($components['plot'])) {
			foreach ($components['plot']['elements'] as $key => $value) {
				if(isset($value['formdata'])){
					if($component_data) {
						$reportcontenttypes[$value['id']] = ucfirst($value['formdata']->chartname);
					} else {
						$reportcontenttypes[] = array('pluginname' => $value['pluginname'], 'chartname' => ucfirst($value['formdata']->chartname),'chartid'=>$value['id'], 'title' => get_string($value['pluginname'], 'block_learnerscript'));
					}
				}
			}
		}

		if ($checktable) {
			if ($component_data) {
				$reportcontenttypes['table'] = get_string('table', 'block_learnerscript');
			} else {
				$disablereporttable = $DB->get_field('block_learnerscript', 'disabletable', array('id' => $reportid));
				if ($disablereporttable == 0) {
					$reportcontenttypes[] = array('chartid' => 'table', 'chartname' => get_string('table', 'block_learnerscript'));
				}
			}
		}
		return $reportcontenttypes;
	}
	/**
	 * [add_customreports_sql description]
	 * @param [type] $reports [description]
	 */
	public function add_customreports_sql($reports) {
		global $DB, $CFG;
		foreach ($reports as $report) {
			$importurl = urldecode($CFG->wwwroot . '/blocks/learnerscript/reports/sql/customreports/' . $report . '.xml');
			$fcontents = file_get_contents($importurl);
			$course = $DB->get_record("course", array("id" => SITEID));
			if (cr_import_xml($fcontents, $course, false)) {
				//return true;
				//redirect("$CFG->wwwroot/blocks/learnerscript/managereport.php?courseid={$course->id}", get_string('reportcreated', 'block_learnerscript'));
			} else {
				print_error(get_string('errorimporting', 'block_learnerscript'));
			}
		}
	}
	/**
	 * List of scheduled reports data
	 * @param  [Int] $frequency
	 * @return Object       List of scheduled reports
	 */
	public function schedulereportsquery($frequency = false) {
		global $DB;
		core_date::set_default_server_timezone();
		$now = new DateTime("now", core_date::get_server_timezone_object());
		$date = $now->format('Y-m-d');
		$hour = $now->format('H');
		$frequencyquery = '';
		if ($frequency == ONDEMAND) {
			$frequencyquery = " AND crs.frequency = $frequency AND crs.timemodified = 0 ";
		} else {
			$frequencyquery = " AND DATE(FROM_UNIXTIME(crs.nextschedule)) = '$date' AND HOUR(FROM_UNIXTIME(crs.nextschedule)) = $hour";
		}
		$sql = "SELECT crs.*, cr.name, cr.courseid, u.timezone
	              FROM {block_ls_schedule} as crs
	              JOIN {block_learnerscript} as cr ON crs.reportid = cr.id
	              JOIN {user} as u ON crs.userid = u.id
	             WHERE u.confirmed = 1 AND u.suspended = 0 AND u.deleted = 0 $frequencyquery";
		$scheduledreports = $DB->get_records_sql($sql);
		return $scheduledreports;
	}
	/**
	 * Processing scheduling reports cron based on frequency
	 * @param  Integer $frequency DAILY/WEEKLY/MONTHLY const values
	 * @return  boolean
	 */
	public function process_scheduled_reports($frequency = false) {
		global $CFG, $DB;
		$schedule = new \block_learnerscript\local\schedule;
		$scheduledreports = (new self)->schedulereportsquery($frequency);
		$totalschedulereports = count($scheduledreports); 
		mtrace('Processing ' . $totalschedulereports . ' scheduled reports');
		if ($totalschedulereports > 0) {
			foreach ($scheduledreports as $scheduled) {
				switch ($scheduled->exporttofilesystem) {
					case REPORT_EXPORT_AND_EMAIL:
						mtrace('ReportID (' . $scheduled->reportid . ') - ScheduleID (' . $scheduled->id . ') Option: Email and save scheduled report to file.');
						break;
					case REPORT_EXPORT:
						mtrace('ReportID (' . $scheduled->reportid . ') - ScheduleID (' . $scheduled->id . ') Option: Save scheduled report to file system only.');
						break;
					case REPORT_EMAIL:
						mtrace('ReportID (' . $scheduled->reportid . ') - ScheduleID (' . $scheduled->id . ') Option: Email scheduled report.');
						break;
				}
				$schedule->scheduledreport_send_scheduled_report($scheduled);
				if ($scheduled->frequency == DAILY) {
					$scheduletype = 'dailyreport';
				} else if ($scheduled->frequency == WEEKLY) {
					$scheduletype = 'weeklyreport';
				} else if ($scheduled->frequency == MONTHLY) {
					$scheduletype = 'monthlyreport';
				} else if ($scheduled->frequency == ONDEMAND) {
					$scheduletype = 'On Demand';
				} else {
					$scheduletype = 'N/A';
				}

				if ($frequency != ONDEMAND) {
					$scheduled->nextschedule = $schedule->next($scheduled);
					$scheduled->timemodified = time();
					if (!$DB->update_record('block_ls_schedule', $scheduled)) {
						mtrace('Failed to update next report field for scheduled report id:' . $scheduled->id);
					}
				}
			}
		}
		return true;
	}
	/**
	 * Scheduled reports cron
	 * @return boolean true/false
	 */
	public function initiate_scheduled_reports() {
		(new self)->process_scheduled_reports();
	}
	/**
	 * PDF Report Export Header
	 * @return Report Header
	 */
	public function pdf_reportheader() {
		$reportlogo = get_config('block_learnerscript', 'logo');

		$headerimgpath = get_reportheader_imagepath();
		if (!(empty($headerimgpath)) && @getimagesize($headerimgpath)) {
			$headerimgpath = $headerimgpath;
		}
		if ($headerimgpath) {
			$reportlogoimage = '<img src="' . $headerimgpath . '" alt=' . get_string("altreportimage", "block_learnerscript") . ' height="80px">';
		} else {
			$reportlogoimage = "";
		}
		return $reportlogoimage;
	}
	/**Dynamic Columns For Sections */
	/**
	 * [learnerscript_sections_dynamic_columns description]
	 * @param  [type] $columns [description]
	 * @param  [type] $config  [description]
	 * @return [type]          [description]
	 */
	// public function learnerscript_sections_dynamic_columns($columns, $config, $basicparams){
	// 	global $CFG;
	// 	$flag = 0;
	// 	foreach ($columns as $colvalue) {
	//         $colvalue = (object)$colvalue;
	//         if ($colvalue->pluginname == "topicwiseperformance") {
	//             require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/topicwiseperformance/plugin.class.php');
	//             $pluginname = $colvalue->pluginname;
	//             $plgname = 'plugin_topicwiseperformance';
	//             $dynclass = new $plgname($config, $colvalue);
	//             $flag = 1;
	//         }
	//     }
	//     if ($flag == 1) {
	//         $reportinfo = $dynclass->report;
	//         if ($reportinfo->type == 'topic_wise_performance') {
	//             $filter_course = optional_param('filter_course', 0, PARAM_INT);
	//             $courseid = isset( $basicparams['filter_course']) ? $basicparams['filter_course'] : null;
	//             if (!empty($filter_course)) {
	//             	$courseid = $filter_course;
	//             }
	//             // $qlist = $dynclass->get_sessionslist($courseid);
	//             if($courseid > 1) {
	// 		        $format = \course_get_format($courseid);
	// 		        $modinfo = \get_fast_modinfo($courseid);
	// 		        $modules = $modinfo->get_used_module_names();
	// 		        $sections = array();
	// 		        if ($format->uses_sections()) {
	// 		            foreach ($modinfo->get_section_info_all() as $section) {
	// 		                if ($section->uservisible) {
	// 		                    $sections[] = $format->get_section_name($section);
	// 		                }
	// 		            }
	// 		        }
	// 		    }
	//         }
	//         if (!empty($sections)) {
	// 	        foreach($sections as $k => $value){
	// 	            $columns[] = (new self)->learnerscript_create_dynamic_sectioncolumns($value,
	// 	            	'topicwiseperformance', $k);
	// 	        }
	// 	    }
	//     }
	// 	return $columns;
	// }
	/**
	 * [learnerscript_create_dynamic_sectioncolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_sectioncolumns($value, $columname, $k){
		$newcolumn = array();
		$formdata = new stdclass();
		$formdata->column = "section$k";
		$formdata->columname = $value;;
		$newcolumn['formdata'] = $formdata;
		$newcolumn['pluginname'] = $columname;
		$newcolumn['pluginfullname'] = $columname;
		return $newcolumn;
	}

	/**Dynamic Columns For Course users */
	/**
	 * [learnerscript_courseusers_dynamic_columns description]
	 * @param  [type] $columns [description]
	 * @param  [type] $config  [description]
	 * @return [type]          [description]
	 */
	public function learnerscript_courseusers_dynamic_columns($columns, $config){
		global $DB, $CFG;

		foreach($columns as $colvalue){
	        $colvalue =(object)$colvalue;
	        if($colvalue->pluginname=="courseactivitiesinfo"){
	            require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/courseactivitiesinfo/plugin.class.php');
	            $pluginname =$colvalue->pluginname;
	            $plgname= 'plugin_courseactivitiesinfo';
	            $dynclass = new $plgname($config, $colvalue);
	            $flag=1;
	        }
	    }
	    if($flag==1){
	        $reportinfo= $dynclass->report;
	        if($reportinfo->type=='courseactivitiesinfo'){
	            $filter_course = optional_param('filter_course', 0, PARAM_INT);
	            $courseid = optional_param('courseid', $filter_course, PARAM_INT);
	            if(!empty($filter_course)){
	            	$courseid = $filter_course;
	            }
	            $coursecontext = context_course::instance($courseid);
	            $studentroleid = $DB->get_field('role','id',array('shortname'=>'student'));
	            $qlist = get_role_users($studentroleid, $coursecontext);
	            $sql ="SELECT u.id, u.lastname, u.firstname,c.id AS courseid
						FROM {user} u
						JOIN {role_assignments} ra ON ra.userid = u.id
						JOIN {context} ct ON ct.id = ra.contextid
						JOIN {course} c ON c.id = ct.instanceid
						JOIN {role} r ON r.id = ra.roleid
						WHERE r.id = $studentroleid AND c.id = $filter_course AND u.id IN (3,4)";
				$qlist = $DB->get_records_sql($sql);
	        }
	        foreach($qlist as $value){
	            $columns[]=learnerscript_create_dynamic_usergradecolumns($value,'courseactivitiesinfo');
	            $columns[]=learnerscript_create_dynamic_userstatuscolumns($value,'courseactivitiesinfo');
	        }
	    }
		return $columns;
	}
	/**
	 * [learnerscript_create_dynamic_usergradecolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_usergradecolumns($value, $columname){
		global $DB;
		$newcolumn=array();
		$formdata= new stdclass();
		$formdata->column="grade$value->id";
		$userrecord = $DB->get_record_sql("SELECT * FROM {user} WHERE id = $value->id");
		$formdata->columname='Grade('.$userrecord->firstname.' '.$userrecord->lastname .')';
		$newcolumn['formdata']=$formdata;
		$newcolumn['pluginname']=$columname;
		$newcolumn['pluginfullname']=$columname;
		return $newcolumn;
	}
	/**
	 * [learnerscript_create_dynamic_userstatuscolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_userstatuscolumns($value, $columname){
		global $DB;
		$newcolumn=array();
		$formdata= new stdclass();
		$formdata->column="status$value->id";
		$userrecord = $DB->get_record_sql("SELECT * FROM {user} WHERE id = $value->id");
		$formdata->columname='Status('.$userrecord->firstname.' '.$userrecord->lastname .')';
		$newcolumn['formdata']=$formdata;
		$newcolumn['pluginname']=$columname;
		$newcolumn['pluginfullname']=$columname;
		return $newcolumn;
	}
	/**
	 * [report_componentslist description]
	 * @param  [type] $report [description]
	 * @param  [type] $comp   [description]
	 * @return [type]         [description]
	 */
	public function report_componentslist($report,$comp){
		global $CFG;
		require_once($CFG->dirroot.'/blocks/learnerscript/reports/'.$report->type.'/report.class.php');

		$reportclassname = 'report_'.$report->type;
		$properties = new stdClass();
		$reportclass = new $reportclassname($report->id, $properties);
		// if(!in_array($comp,$reportclass->components))
		// 	print_error('badcomponent');

		$elements = (new self)->cr_unserialize($report->components);
		$elements = isset($elements[$comp]['elements'])? $elements[$comp]['elements'] : array();

		require_once($CFG->dirroot.'/blocks/learnerscript/components/'.$comp.'/component.class.php');
		$componentclassname = 'component_'.$comp;
		$compclass = new $componentclassname($report->id);
			if($compclass->plugins){
				$currentplugins = array();
				if($elements){
					foreach($elements as $e){
						$currentplugins[] = $e['pluginname'];
					}
				}
				$plugins = get_list_of_plugins('blocks/learnerscript/components/'.$comp);
				$optionsplugins = array();
				foreach($plugins as $p){
					require_once($CFG->dirroot.'/blocks/learnerscript/components/'.$comp.'/'.$p.'/plugin.class.php');
					$pluginclassname = 'plugin_'.$p;
					$pluginclass = new $pluginclassname($report);
					if(in_array($report->type,$pluginclass->reporttypes)){
						if($pluginclass->unique && in_array($p,$currentplugins))
							continue;
						$optionsplugins[$p] = get_string($p,'block_learnerscript');
					}
				}
				asort($optionsplugins);
			}
		return $optionsplugins;
	}
	/**
	 * [column_definations description]
	 * @param  [type] $reportclass [description]
	 * @return [type]              [description]
	 */
	public function column_definations($reportclass){
		$columnDefs = array();
		$datacolumns = array();
		$i = 0;
		$re = array();
		if (!empty($reportclass->finalreport->table->head)) {
			$re = array_diff(array_keys($reportclass->finalreport->table->head), $reportclass->orderable);
		}
		if (!empty($reportclass->finalreport->table->head)) {
			foreach ($reportclass->finalreport->table->head as $key => $value) {
				$datacolumns[]['data'] = $value;
				$columnDef = new stdClass();
				$align = $reportclass->finalreport->table->align[$i] ? $reportclass->finalreport->table->align[$i] : 'left';
			    $wrap = ($reportclass->finalreport->table->wrap[$i] == 'wrap') ? 'break-all' : 'normal';
			    $width = ($reportclass->finalreport->table->size[$i]) ? $reportclass->finalreport->table->size[$i] : '';
			    $columnDef->className = 'dt-body-'. $align;

			    $columnDef->wrap = $wrap;
			    $columnDef->width = $width;
			    $columnDef->targets = $i;
			    if($re[$i]) {
				    $columnDef->orderable = false;
			    } else {
			    	$columnDef->orderable = true;
			    }
			    $i++;
			    $columnDefs[] = $columnDef;
			}
		}
		return compact('datacolumns','columnDefs');
	}
    public function check_rolewise_permission($reportid, $role) {
        global $DB, $CFG, $USER;
        $context = context_system::instance();
        $roleid = $DB->get_field('role', 'id', array('shortname' => $role));
        $reportcomponents = $DB->get_field('block_learnerscript', 'components', array('id' => $reportid));
        $components = (new ls)->cr_unserialize($reportcomponents);
        $permissions = (isset($components['permissions'])) ? $components['permissions'] : array();
        
		if(empty($permissions['elements'])){
			return false;
		} else {
            foreach ($permissions['elements'] as $p) {
            	if($p['pluginname'] == 'sitelevelroles') {
            		if($roleid == $p['formdata']->roleid) {
            			return true;
            		}
            	}
            }
            return false;
		}
    }

    public function listofreportsbyrole($coursels = false, $statistics = false, $parentcheck = false, $reportslist = false, $enablestatistics = false) {
    	global $DB,$PAGE;
    	//Course context reports
    	if($PAGE->context->contextlevel == 50 || $PAGE->context->contextlevel == 70){
    		$coursels = true;
    	}

    	if($statistics){
			$statisticsreports = array();
			$roles = $DB->get_records_sql("SELECT id, shortname FROM {role}");
			foreach($roles as $role){
				$rolereports = (new ls)->rolewise_statisticsreports($role->shortname);
				foreach($rolereports as $key => $value){
					$statisticsreports[$value] = $value;
				}
			}
    		if(empty($_SESSION['role']) && !empty($statisticsreports)){
				$implodereports = implode(',', $statisticsreports);
    			$reportlist = $DB->get_records_select_menu('block_learnerscript', 'global = 1 AND visible = 1 AND id NOT IN ('.$implodereports.') AND (type = "statistics" OR enablestatistics=1)', null, '', 'id, name');
    		} else{
    			$reportlist = $DB->get_records_select_menu('block_learnerscript', 'global = 1 AND visible = 1 AND (type = "statistics" OR enablestatistics=1)', null, '', 'id, name');
    		}
    	} else{
    		$reportlist = $DB->get_records_select_menu('block_learnerscript', 'global = 1 AND visible = 1 AND (type != "statistics" OR enablestatistics=0)', null, '', 'id, name');
    	}
		$rolereports = array();
		if(!empty($reportlist)) {
			$properties = new stdClass();
			$properties->courseid = SITEID;
			$properties->start = 0;
			$properties->length = 1;
			$properties->search = '';
			foreach ($reportlist as $key => $value) {
				if(!empty($_SESSION['role'])) {
		            $sql = "SELECT rc.id
							FROM {role_capabilities} rc
				            JOIN {role} r ON r.id = rc.roleid
				            JOIN {context} cxt ON cxt.id = rc.contextid
				            WHERE rc.capability = :capname AND cxt.contextlevel = :context 
				            AND r.shortname = :rolename AND rc.permission = :permission ";

	             	$params = array();
	             	$params['capname'] = 'block/learnerscript:managereports';
	             	$params['rolename'] = $_SESSION['role'];
	                if($_SESSION['role'] == "user") {
	        			$params['rolename'] = "employee";
	        	    }
	             	$params['context'] = 10;
	             	$params['permission'] = CAP_ALLOW;

            		$has_cap = $DB->record_exists_sql($sql, $params);
            		if(!$has_cap){
            			$check_rolewise_permission = (new ls)->check_rolewise_permission($key, $_SESSION['role']);

				        if($check_rolewise_permission == false) {
				            continue;
				        }
            		}
				}
				$reportcontenttypes = (new ls)->cr_listof_reporttypes($key);
				if(sizeof($reportcontenttypes) < 1 && $coursels){
				    continue;
            	}
	            $report = $this->create_reportclass($key, $properties);
	            if (!$reportslist) {
	            	if($report->parent == false && !$parentcheck && !$coursels) {
	            	continue;
	            }
	            }
	            if($coursels){
	            	if(!$report->courselevel){
	            		continue;
	            	}
	            }
	            $rolereports[] = ['id'=> $key, 'name' => $value];
	        }
	    }
        return $rolereports;
    }
    public function rolewise_statisticsreports($role) {
    	global $DB,$PAGE;
    	if(empty($role) || $role == 'manager'){
    		return array();
    	}
		$reportlist = $DB->get_records_select_menu('block_learnerscript', 'global = 1 AND visible = 1 AND (type = "statistics" OR enablestatistics=1)', null, '', 'id, name');
		$statisticsreports = array();
		if(!empty($reportlist)) {
			foreach ($reportlist as $key => $value) {
				if(!empty($role)) {
		            $check_rolewise_permission = (new ls)->check_rolewise_permission($key, $_SESSION['role']);
			        if($check_rolewise_permission == false) {
			            continue;
			        }
				}
	            $statisticsreports[] =$key;
	        }
	    }
        return $statisticsreports;
    }
    function get_reporttitle($reporttype,$reportclassparams){
    	global $DB;
    	$reporttitle = $reporttype;
    	if(array_key_exists('filter_course',$reportclassparams) && $reporttitle != 'Course profile'){
    		$coursename = $DB->get_field('course','fullname',array('id'=>$reportclassparams['filter_course']));
    		$reporttitle = str_replace('Course', '<b>'.$coursename.'</b> Course', $reporttype);
    	}
    	if(array_key_exists('filter_status',$reportclassparams) && $reportclassparams['filter_status'] != 'all'){
			$reporttitle = $reporttitle . ' - ' . '<b>' . get_string($reportclassparams['filter_status'],'block_learnerscript') . '</b>';
    	}
    	if(array_key_exists('filter_users',$reportclassparams)){
    		if(is_int($reportclassparams['filter_users'])){
	    		$learnername = $DB->get_field_sql("select firstname as fullname from {user}
	    											where id=".$reportclassparams['filter_users']."");
	    		$reporttitle = str_replace('Learner', '<b>'.$learnername.'</b>', $reporttitle);
	    		$reporttitle = str_replace('My', '<b>'.$learnername.'\'s</b>', $reporttitle);
    		}
    	}
    	return $reporttitle;
    }
    public function importlsusertours() {
	    global $CFG, $DB;
	    $usertours = $CFG->dirroot . '/blocks/learnerscript/usertours/';
	    $totalusertours = count(glob($usertours . '*.json'));
	    $usertoursjson = glob($usertours . '*.json');
	    $pluginmanager = new \tool_usertours\manager();
	    for ($i = 0; $i < $totalusertours; $i++) {
	        $importurl = $usertoursjson[$i];
	        if (file_exists($usertoursjson[$i])
	                && pathinfo($usertoursjson[$i], PATHINFO_EXTENSION) == 'json') {
	            $data = file_get_contents($importurl);
	            $tourconfig = json_decode($data);
	            $tourexists = $DB->record_exists('tool_usertours_tours', array('name' => $tourconfig->name));
	            if (!$tourexists) {
	                $tour = $pluginmanager->import_tour_from_json($data);
	            }
	        }
	    }
	}
    public function lsconfigreports() {
		global $CFG, $DB;
		$path = $CFG->dirroot . '/blocks/learnerscript/backup/';
		$learnerscriptreports = glob($path . '*.xml');
		$lsreportscount = $DB->count_records('block_learnerscript');
		// $lsimportlogssql = "SELECT other
		//                       FROM {logstore_standard_log}
		//                      WHERE action = :action AND target = :target
		//                             AND objecttable = :objecttable AND other <> :other";
		// $lsimportlogs = $DB->get_fieldset_sql($lsimportlogssql, array('action' => 'import',
		//     'target' => 'report', 'objecttable' => 'block_learnerscript', 'other' => 'N;'));
		$lastreport = 0;
		$lsimportlogs =array();
		foreach ($lsimportlogs as $lsimportlog) {
		    $lslog = unserialize($lsimportlog);
		    if ($lslog['status'] == false) {
		        $errorreportsposition[$lslog['position']] = $lslog['position'];
		    }

		    if ($lslog['status'] == true) {
		        $lastreportposition = $lslog['position'];
		    }
		}

		$importstatus = false;
		if (empty($lsimportlogs) || $lsreportscount < 1) {
		    $total = count($learnerscriptreports);
		    $current = 1;
		    $percentwidth = $current / $total * 100;
		    $importstatus = true;
		    $errorreportsposition = array();
		    $lastreportposition = 0;
		} else {
		    $total = 0;
		    foreach ($learnerscriptreports as $position => $learnerscriptreport) {
		        if ((!empty($errorreportsposition) && in_array($position, $errorreportsposition)) || $position >= $lastreportposition) {
		            $total++;
		        }
		    }
		    if (empty($errorreportsposition)) {
		        $current = $lastreportposition + 1;
		        $errorreportsposition = array();
		    } else {
		        $occuredpositions = array_merge($errorreportsposition, array($lastreportposition));
		        $current = min($occuredpositions);
		    }
		    if ($total > 0) {
		        $importstatus = true;
		    }
		}
		$errorreportspositiondata = serialize($errorreportsposition);
		return compact('importstatus', 'total', 'current', 'errorreportspositiondata',
			'lastreportposition');
	}
	/**
     * login user roles (course and system)
     * @return [array] list of roles
     */
    public function get_currentuser_roles($userid = false, $contextlevel = null){
      global $DB, $USER;
      $userid = $userid > 0 ? $userid : $USER->id;
      $rolesql = "SELECT DISTINCT r.id, r.shortname
                   FROM {role} AS r
                   JOIN {role_assignments} AS ra ON ra.roleid = r.id
                   JOIN {context} as ctx ON ctx.id = ra.contextid
                   WHERE ra.userid = $userid";
      if($contextlevel){
      	$rolesql .= " AND ctx.contextlevel=$contextlevel";
      }
      $roles = $DB->get_records_sql_menu($rolesql);
      ksort($roles);
      return $roles;
    }

    //Get All categories of Reports
    public function get_categories_from_reports(){
		global $DB;

    	$query = "SELECT DISTINCT category 
    				FROM {block_learnerscript}";

    	$results = $DB->get_records_sql($query);

    	return $results;
    }

    //get report details category wise
    public function get_reportdetails_categorywise($category){
    	global $DB, $USER;

    	$query = "SELECT * 
    			  FROM {block_learnerscript} 
    			  WHERE category = :category  ";

    	$params=array();
    	$params['category'] = $category;

    	$results=$DB->get_records_sql($query,$params);

    	return $results;
    }

    public function learnerscript() {
        global $CFG, $USER, $DB;

        $curl = new \curl;
        $ls = get_config('block_learnerscript', 'serialkey');
        if (empty($ls)) {
            return false;
        }
        $params = array();
        $params['serial'] = $ls;
        $params['surl'] = $CFG->wwwroot;
		$params['level'] = true;
        $param = json_encode($params);

        $json = $curl->post('https://learnerscript.com?wc-api=custom_validate_serial_key', $param);
        for ($i = 0; $i <= 31; ++$i) { 
            $json = str_replace(chr($i), "", $json); 
        }
        $json = str_replace(chr(127), "", $json);

        // This is the most common part
        // Some file begins with 'efbbbf' to mark the beginning of the file. (binary level)
        // here we detect it and we remove it, basically it's the first 3 characters 
        if (0 === strpos(bin2hex($json), 'efbbbf')) {
           $json = substr($json, 3);
        }
        $jsondata = json_decode($json, true);
        if ($jsondata['success'] == 'true') {
            $status = 1;
        } else {
            $status = 0;
	    	set_config('serialkey', null ,'block_learnerscript');
        }
    }
	public function timetoseconds($timevalue) {
		$str_time = $timevalue;
		$str_time = preg_replace("/^([\d]{1,2}):([\d]{2})$/", "00:$1:$2", $str_time);
		sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
		$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
		return $time_seconds;
	}
    public function userscormtimespent() {
        global $DB;
        $scormrecord = get_config('block_learnerscript', 'userscormtimespent');
        if (empty($scormrecord)) {
            set_config('userscormtimespent', 0, 'block_learnerscript');
        }
        $scormcrontime = get_config('block_learnerscript', 'userscormtimespent');
        $moduleid = $DB->get_field('modules', 'id', ['name' => 'scorm']);
        if ($scormcrontime == 0) {
            $scormdetails = $DB->get_records_sql("SELECT sst.id, sa.userid, sa.scormid, sst.value AS time
            FROM {scorm_scoes_value} sst
            JOIN {scorm_scoes} ss ON ss.id = sst.scoid
            JOIN {scorm_attempt} sa ON sa.id = sst.attemptid
            JOIN {scorm_element} se ON se.id = sst.elementid
            JOIN {scorm_scoes_value} sst1 ON sst1.scoid = sst.scoid AND sa.id = sst1.attemptid
            JOIN {scorm_element} se1 ON se1.id = sst1.elementid
            WHERE se.element LIKE 'cmi.core.total_time' AND sst1.value IN ('passed', 'completed', 'failed')
            AND sa.userid > 2 ");
            $time = time();
            set_config('userscormtimespent', $time, 'block_learnerscript');
        } else if ($scormcrontime > 0) {
            $scormdetails = $DB->get_records_sql("SELECT sst.id, sa.userid, sa.scormid, sst.value AS time
            FROM {scorm_scoes_value} sst
            JOIN {scorm_scoes} ss ON ss.id = sst.scoid
            JOIN {scorm_attempt} sa ON sa.id = sst.attemptid
            JOIN {scorm_element} se ON se.id = sst.elementid
            JOIN {scorm_scoes_value} sst1 ON sst1.scoid = sst.scoid AND sa.id = sst1.attemptid
            JOIN {scorm_element} se1 ON se1.id = sst1.elementid
            WHERE se.element LIKE 'cmi.core.total_time' AND sst1.value IN ('passed', 'completed', 'failed')
            AND sa.userid > 2 AND sst.timemodified > $scormcrontime ");
            $time = time();
            set_config('userscormtimespent', $time, 'block_learnerscript');
        }
        if (empty($scormdetails)) {
            return true;
        }
        foreach ($scormdetails as $scormdetail) {
            $coursemoduleid = $DB->get_field('course_modules', 'id', ['module' => $moduleid,
            'instance' => $scormdetail->scormid, 'visible' => 1, 'deletioninprogress' => 0, ]);
            $courseid = $DB->get_field('scorm', 'course', ['id' => $scormdetail->scormid]);
            $insertdata = new stdClass();
            $insertdata->userid = $scormdetail->userid;
            $insertdata->courseid = $courseid;
            $insertdata->instanceid = $scormdetail->scormid;
            $insertdata->timespent = round($this->timetoseconds($scormdetail->time));
            $insertdata->activityid = $coursemoduleid;
            $insertdata->timecreated = time();
            $insertdata->timemodified = 0;
            $insertdata1 = new stdClass();
            $insertdata1->userid = $scormdetail->userid;
            $insertdata1->courseid = $courseid;
            $insertdata1->timespent = round($this->timetoseconds($scormdetail->time));
            $insertdata1->timecreated = time();
            $insertdata1->timemodified = 0;
            $records1 = $DB->get_records('block_ls_coursetimestats',
                        ['userid' => $insertdata1->userid,
                            'courseid' => $insertdata1->courseid, ]);
            if (!empty($records1)) {
                foreach ($records1 as $record1) {
                    $insertdata1->id = $record1->id;
                    $insertdata1->timespent += round($record1->timespent);
                    $insertdata1->timemodified = time();
                    $DB->update_record('block_ls_coursetimestats', $insertdata1);
                }
            } else {
                $insertdata1->timecreated = time();
                $insertdata1->timemodified = 0;
                $DB->insert_record('block_ls_coursetimestats', $insertdata1);
            }
            $records = $DB->get_records('block_ls_modtimestats',
                        ['courseid' => $insertdata->courseid,
                            'activityid' => $insertdata->activityid,
                            'instanceid' => $insertdata->instanceid,
                            'userid' => $insertdata->userid, ]);
            if ($insertdata->instanceid != 0) {
                if (!empty($records)) {
                    foreach ($records as $record) {
                        $insertdata->id = $record->id;
                        $insertdata->timespent += round($record->timespent);
                        $insertdata->timemodified = time();
                        $DB->update_record('block_ls_modtimestats', $insertdata);
                    }
                } else {
                    $insertdata->timecreated = time();
                    $insertdata->timemodified = 0;
                    $DB->insert_record('block_ls_modtimestats', $insertdata);
                }
            }
        }
    }
    public function coursetimepsent() {
    	global $DB, $PAGE;
    	$scormrecord = get_config('block_learnerscript', 'timepsent');

    	if (empty($scormrecord)) {
	    	$timespentdetails = $DB->get_records_sql("SELECT * FROM {block_ls_timestats}");
		    if (empty($timespentdetails)) {
		    	return true;
		    }
	    	foreach ($timespentdetails as $timespentdetail) {
	    		$insertdata = new stdClass();
		        $insertdata->userid = $timespentdetail->userid;
		        $insertdata->courseid = $timespentdetail->courseid;
		        $insertdata->instanceid = $timespentdetail->instanceid;
		        $insertdata->timespent = $timespentdetail->time;
		        $insertdata->activityid = $timespentdetail->contextinstanceid;
		        $insertdata->timecreated = time();
		        $insertdata->timemodified = 0;
		        $insertdata1 = new stdClass();
		        $insertdata1->userid = $timespentdetail->userid;
		        $insertdata1->courseid = $timespentdetail->courseid;
		        $insertdata1->timespent = $timespentdetail->time;
		        $insertdata1->timecreated = time();
		        $insertdata1->timemodified = 0;
		        $records1 = $DB->get_records('block_ls_coursetimestats',
		                    array('userid' => $insertdata1->userid,
		                    	'courseid' => $insertdata1->courseid,));
		        if (!empty($records1)) {
		        	foreach ($records1 as $record1) {
			            $insertdata1->id = $record1->id;
			            $insertdata1->timespent += $record1->timespent;
			            $insertdata1->timemodified = time();
			            $DB->update_record('block_ls_coursetimestats', $insertdata1);
		        	}
		        } else {
		            $insertdata1->timecreated = time();
		            $insertdata1->timemodified = 0;
		            $DB->insert_record('block_ls_coursetimestats', $insertdata1);
		        }
		        $records = $DB->get_records('block_ls_modtimestats',
		                    array('courseid' => $insertdata->courseid,
		                        'activityid' => $insertdata->activityid,
		                        'instanceid' => $insertdata->instanceid,
		                        'userid' => $insertdata->userid));
			    if ($insertdata->instanceid != 0) {
			    	foreach ($records as $record) {
				        if (!empty($record)) {
				            $insertdata->id = $record->id;
				            $insertdata->timespent += $record->timespent;
				            $insertdata->timemodified = time();
				            $DB->update_record('block_ls_modtimestats', $insertdata);
				        }
				    }
			        } else {
			            $insertdata->timecreated = time();
			            $insertdata->timemodified = 0;
			            $DB->insert_record('block_ls_modtimestats', $insertdata);
			        }  
		    	}
	    				  
	    	set_config('timepsent', true, 'block_learnerscript');
    	}	
    }

	public function strTime($values) { 
      	$day = intval($values/86400); 
      	$values -= $day*86400; 

      	$hours = intval($values/3600); 
      	$values -= $hours*3600; 

      	$minutes = intval($values/60); 
      	$values -= $minutes*60; 
      	if (!empty($hours)) {
      		$hrs = ($hours == 1) ? $hours. 'hr ' : $hours. 'hrs ';
      	} else {
      		$hrs = '';
      	}

      	if (!empty($minutes)) {
      		$min = $minutes. 'min ';
      	} else {
      		$min = '';
      	}

      	if (!empty($values)) {
      		$sec = $values. 'sec';
      	} else {
      		$sec = '';
      	}

      	if ($day == 1) $days = $day. 'day  ';
      	else if ($day > 1) $days = $day. 'days  ';
      	else $days = '';

      	$result = $days . $hrs . $min . $sec;
	   
      	return $result; 
  	}

  	public function switchrole_options() {
  		global $DB, $USER;
  		$data = [];
        $systemcontext = context_system::instance();
        if (!empty($_SESSION['role'])) {
            $data['currentrole'] = $_SESSION['role'];
            $data['dashboardrole'] = $_SESSION['role'];
            if($_SESSION['role'] == 'user'){
            	$data['dashboardrole'] = "employee";
            }
        } else {
            $data['currentrole'] = 'Switch Role';
            $data['dashboardrole'] = '';
        }
        if (!is_siteadmin()) {
           // $roles = $this->get_currentuser_roles();
	      	$rolesql = "SELECT DISTINCT CONCAT(r.id,'-',ctx.contextlevel)  as roleid, r.shortname
	                   FROM {role} AS r
	                   JOIN {role_assignments} AS ra ON ra.roleid = r.id
	                   JOIN {context} as ctx ON ctx.id = ra.contextid
	                   WHERE ra.userid = $USER->id";
			$roles = $DB->get_records_sql($rolesql);
			ksort($roles);
        } else {
            // $roles = get_switchable_roles($systemcontext);
            $rolesql = "SELECT DISTINCT concat(r.id,'-',rcl.contextlevel)  as roleid, r.shortname
                   FROM {role} AS r
                   JOIN {role_context_levels} AS rcl ON rcl.roleid = r.id 
                   WHERE 1 = 1 AND rcl.contextlevel != " . CONTEXT_MODULE;
		    $roles = $DB->get_records_sql($rolesql);
		    ksort($roles);
	       
        } 
        if (is_siteadmin() || count($roles) > 0) {
            $data['switchrole'] = true;
        }
        $unusedroles = array('user', 'guest', 'frontpage');
        foreach ($roles as $key => $value) {
           
            $rolecontext = explode("-", $key);
            $roleshortname = $DB->get_field('role', 'shortname', array('id' => $rolecontext[0]));
            if (in_array($roleshortname, $unusedroles)) {
                continue;
            }
            $active = '';

            if ($roleshortname == $_SESSION['role'] && $rolecontext[1] == $_SESSION['ls_contextlevel']) {
                $active = 'active';
            } 
            switch ($roleshortname) {
            	     case 'manager': $value = get_string('manager' , 'role'); 
            	     break;
                    case 'coursecreator':   $value = get_string('coursecreators'); break;
                    case 'editingteacher':  $value = get_string('defaultcourseteacher'); break;
                    case 'teacher':         $value = get_string('noneditingteacher'); break;
                    case 'student':         $value = get_string('defaultcoursestudent'); break;
                    case 'guest':           $value = get_string('guest'); break;
                    case 'user':            $value = get_string('authenticateduser'); break;
                    case 'frontpage':       $value = get_string('frontpageuser', 'role'); break;
                    // We should not get here, the role UI should require the name for custom roles!
                    default:                $value = $value; break;
            }
            if($rolecontext[1] == 10) {
                $contexttext = "System";
            } else if ($rolecontext[1] == 40) {
               $contexttext = "Category"; 
            } else if ($rolecontext[1] == 50) {
               $contexttext = "Course";
            }
            $data['roles'][] = ['roleshortname' => $roleshortname, 'rolename' => $contexttext." ".$value,
                                'active' => $active, 'contextlevel' => $rolecontext[1]];
        }
        return $data;
  	}
  	public function is_manager($userid = null) {
  		global $USER, $DB;
  		if(isset($_SESSION['role']) && $_SESSION['role'] != 'manager'){
  			return false;
  		}
  		if($userid == null){
  			$userid = $USER->id;
  		}
  		$context = context_system::instance();
  		$roleid = $DB->get_field('role','id', ['shortname' => 'manager']);
        if(user_has_role_assignment($userid, $roleid, $context->id)){
            return true;
        }
  	}


  	/**Dynamic Columns For activities */
	/**
	 * [learnerscript_sections_dynamic_columns description]
	 * @param  [type] $columns [description]
	 * @param  [type] $config  [description]
	 * @return [type]          [description]
	 */
	public function learnerscript_activities_dynamic_columns($columns, $config,$basicparams){
		global $DB, $CFG;

		foreach($columns as $colvalue){
	        $colvalue =(object)$colvalue;
	        if($colvalue->pluginname=="coursegradeactivities"){
	            require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/coursegradeactivities/plugin.class.php');
	            $pluginname =$colvalue->pluginname;
	            $plgname= 'plugin_coursegradeactivities';
	            $dynclass = new $plgname($config, $colvalue);
	            $flag=1;
	        }
	        if($colvalue->pluginname=="programlevels"){
	            require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/programlevels/plugin.class.php');
	            $pluginname =$colvalue->pluginname;
	            $plgname= 'plugin_programlevels';
	            $dynclass = new $plgname($config, $colvalue);
	            $flag=1;
	        }
	    }
	    if($flag==1){
	        $reportinfo= $dynclass->report;
	        if($reportinfo->type=='coursegradeactivities'){
	            $courseid = isset( $basicparams['filter_course']) ? $basicparams['filter_course'] : null;
	            if($courseid){
	            	/* =======code only for grdaed activities =============*/
	            	$sql =  "SELECT cm.*,m.name as itemname
			            	FROM {course_modules} cm
			            	JOIN {modules} m ON cm.module = m.id
			            	WHERE cm.deletioninprogress = 0 AND cm.visible = 1 
			            	AND cm.course = $courseid ";
	            	$qlist = $DB->get_records_sql($sql);

	            	if($qlist){
	            		foreach($qlist as $value){
		            		$newarray = array();
		            		$sqllist = "SELECT gi.id,gi.itemname as name,gi.grademax
			            				FROM  {grade_items} gi
			            				WHERE gi.courseid= $courseid AND gi.itemtype = 'mod'
			            				AND gi.iteminstance = '$value->instance' 
			            				AND gi.itemmodule = '$value->itemname' ";

		            		$data_lists = $DB->get_records_sql($sqllist);
		            		foreach($data_lists as $data_list){
		            			// if($value->itemname != "scorm"){
					            	$columns[] =(new self)->learnerscript_create_dynamic_activitiescolumns($data_list,'coursegradeactivities');
					            	$columns[] =(new self)->learnerscript_create_dynamic_activitiescolumnsgrades($data_list,'coursegradeactivities');
					            // }
				            }
				        }
	            	}

	            }
	        }
	        if($reportinfo->type=='programlevels'){
	            $programid = isset( $basicparams['filter_programs']) ? $basicparams['filter_programs'] : null;
	            if($programid){	  
            		$newarray = array();
            		$sqllist = "SELECT lpl.id, lpl.level 
                                FROM {local_program_levels} lpl
                                WHERE 1 = 1 AND lpl.programid = $programid";

            		$levelslist = $DB->get_records_sql($sqllist);
			        foreach ($levelslist as $levellist) {
			            $levels[] = $levellist->level;
			        }
	            }
	            if (!empty($levels)) {
			        foreach($levels as $k => $value){
			            $columns[] = (new self)->learnerscript_create_dynamic_levelcolumns($value,
			            	'programlevels', $k);
			        }
			    }
	        }
	    }
		return $columns;
	} // end of public function
	public function learnerscript_feedbackcoursequestions_dynamic_columns($columns, $config,$basicparams){
        global $DB, $CFG, $USER;

        foreach($columns as $colvalue){
            $colvalue =(object)$colvalue;
            if($colvalue->pluginname=="feedback_coursescolumns"){
                require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/feedback_coursescolumns/plugin.class.php');
                $pluginname =$colvalue->pluginname;
                $plgname = 'plugin_feedback_coursescolumns';
                $dynclass = new $plgname($config, $colvalue);
                $flag=1;
            }
        }

        if($flag == 1){
            $reportinfo = $dynclass->report;
            if($reportinfo->type == 'feedbackcourses'){
            	$download = optional_param('download', null, PARAM_INT);
             	$courseid = isset( $basicparams['filter_courses']) ? $basicparams['filter_courses'] : null;
	            if($courseid){
	            	$sql =  "SELECT le.id 
		            			FROM {feedback} le 
		            			WHERE le.course = $courseid ";

				    $systemcontext = context_system::instance();

				    if(is_siteadmin() || has_capability('local/costcenter:manage_multiorganizations', $systemcontext)){
		                $sql .= " AND 1=1 ";
		            }
		            //  if($download){
		            // 	$startdate = optional_param('ls_fstartdate', 0, PARAM_INT);
		            // 	$enddate = optional_param('ls_fenddate', 0, PARAM_INT);
		            // 	$basicparams['ls_fstartdate'] = $startdate;
	            	// 	$basicparams['ls_fenddate'] = $enddate;
		            // }
		            // if($basicparams['ls_fstartdate'] > 0 && $basicparams['ls_fenddate']) {
		            // 	$startdate = $basicparams['ls_fstartdate'];
		            // 	$enddate = $basicparams['ls_fenddate'];

		            //     $sql .= " AND le.timeopen BETWEEN $startdate AND $enddate ";
		            // }else{
		            //     $from = date('Y-m', strtotime('-2 month'));
		            //     $fromdate = $from."-01";
		            //     $fromtimestamp = strtotime($fromdate);
		            //     $now = time();

		            //     $sql .= " AND le.timeopen BETWEEN $fromtimestamp AND $now ";
		            // }
		            $sql .= " GROUP BY le.id ORDER BY le.id ASC ";		            

		            $course_evaluations = $DB->get_fieldset_sql($sql);
	            	if($course_evaluations){
	            		foreach ($course_evaluations as $evaluationid) {
           
                        	$sql =  "SELECT fi.name,fi.id, fi.feedback
                                    FROM {feedback_item} fi
                                    WHERE fi.feedback = $evaluationid AND fi.typ != 'label' 
                                    ORDER BY fi.position ASC ";

			                $evaluationitems = $DB->get_records_sql($sql);

			                // for add Date of submission column after the feedback score column
			                $columns[] =(new self)->learnerscript_create_dynamic_cfbsubmitdate_column($evaluationid,'feedback_coursescolumns');

				                foreach($evaluationitems as $item){
				                    $columns[] =(new self)->learnerscript_create_dynamic_cfb_questionscolumns($item,'feedback_coursescolumns');
				                }
				                // for add Feedback Score column at the end of the feedback qns 
					        	$columns[] =(new self)->learnerscript_create_dynamic_cfb_totalcolumn($evaluationid,'feedback_coursescolumns');
	            		}//for each course_evaluation
	           		}//if cousre_evaluations
                }//if courseid
           }
        }
	
        return $columns;
    }

	public function learnerscript_programlevels_dynamic_columns($columns, $config,$basicparams){
		global $DB, $CFG, $USER;
		
		foreach($columns as $colvalue){
            $colvalue =(object)$colvalue;
			if($colvalue->pluginname=="programlevelscompletion"){
				require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/programlevelscompletion/plugin.class.php');
				$pluginname =$colvalue->pluginname;
				$plgname= 'plugin_programlevelscompletion';
				$dynclass = new $plgname($config, $colvalue);
				$flag=1;
			}
		}
		if($flag == 1){
					
			$reportinfo = $dynclass->report;
			if($reportinfo->type == 'programlevelcompletions'){
				$programid = isset( $basicparams['filter_programs']) ? $basicparams['filter_programs'] : null;
				if($programid){	
					$newarray = array();
					$sqllist = "SELECT lpl.id, lpl.level 
								FROM {local_program_levels} lpl
								WHERE 1 = 1 AND lpl.programid = $programid";

					$levelslist = $DB->get_records_sql($sqllist);
					foreach ($levelslist as $levellist) {
						$levels[] = $levellist->level;
					}
				}
				if (!empty($levels)) {
					foreach($levels as $k => $value){
						$columns[] = (new self)->learnerscript_create_dynamic_levelcolumns($value,
							'programlevels', $k);
					}
				}
			}
		}
		
        return $columns;
	}

	   /**
	 * [learnerscript_create_dynamic_cfb_questionscolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_cfbsubmitdate_column($feedbackid, $columname){
		global $DB;

		$newcolumn=array();

	    $formdata= new stdclass();
	    $formdata->column = "submitteddate_$feedbackid";//"feedback_submitteddate";

	    $formdata->columname = 'Date of Submission';

	    $newcolumn['formdata'] = $formdata;
	    $newcolumn['pluginname'] = $columname;
	    $newcolumn['pluginfullname'] = $columname;
	    // var_dump($newcolumn);
	    return $newcolumn;
	} // end of public function

	/**
	 * [learnerscript_create_dynamic_cfb_questionscolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_cfb_questionscolumns($class, $columname){
		global $DB;
			$newcolumn=array();

		if(!empty($class->name)){
		    $formdata= new stdclass();
		    //$formdata->column = "itemid_$class->name";
		    $formdata->column = "itemid_$class->id";

		    $formdata->columname = $class->name;

		    $newcolumn['formdata'] = $formdata;
		    $newcolumn['pluginname'] = $columname;
		    $newcolumn['pluginfullname'] = $columname;
	     }
	    return $newcolumn;
	} // end of public function


	/**
	 * [learnerscript_create_dynamic_fb_questionscolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_cfb_totalcolumn($feedbackid, $columname){
		global $DB;

		$newcolumn=array();

	    $formdata= new stdclass();
	    $formdata->column = "cfbtotal_$feedbackid";

	    $formdata->columname = 'Feedback Score';
	    
	    $newcolumn['formdata'] = $formdata;
	    $newcolumn['pluginname'] = $columname;
	    $newcolumn['pluginfullname'] = $columname;

	    return $newcolumn;
	} // end of public function
	/**
	 * [learnerscript_create_dynamic_sectioncolumns description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_levelcolumns($value, $columname, $k){
		$newcolumn = array();
		$formdata = new stdclass();
		$formdata->column = "level_$k";
		$formdata->columname = $value;;
		$newcolumn['formdata'] = $formdata;
		$newcolumn['pluginname'] = $columname;
		$newcolumn['pluginfullname'] = $columname;
		return $newcolumn;
	}

	/**
	 * [learnerscript_create_dynamic_activities description]
	 * @param  [type] $value     [description]
	 * @param  [type] $columname [description]
	 * @return [type]            [description]
	 */
	public function learnerscript_create_dynamic_activitiescolumns($class, $columname){
		global $DB;
			$newcolumn=array();

		if(!empty($class->name)){
		    $formdata= new stdclass();
		    $formdata->column="classid_$class->id";

		    // $formdata->columname=$class->name." (Score/Grade) ";

		    $formdata->columname=$class->name;

		    //}
		    $newcolumn['formdata']=$formdata;
		    $newcolumn['pluginname']=$columname;
		    $newcolumn['pluginfullname']=$columname;
	     }
	    return $newcolumn;
	} // end of public function


	public function learnerscript_create_dynamic_activitiescolumnsgrades($class, $columname){
		global $DB;
			$newcolumn=array();

		if(!empty($class->name)){
		    $formdata= new stdclass();
		    $formdata->column="gradeclassid_$class->id";

		    // $formdata->columname=$class->name." (Score/Grade) ";

		    $formdata->columname=$class->name." Max grade";

		    //}
		    $newcolumn['formdata']=$formdata;
		    $newcolumn['pluginname']=$columname;
		    $newcolumn['pluginfullname']=$columname;
	     }
	    return $newcolumn;
	} // end of public function
	public function learningformats() { 
		global $DB; 
		$learningformatstime = get_config('block_learnerscript', 'learningformats');
		if (!empty($learningformatstime)) {
			$sql = "SELECT CONCAT(clf.id, '@', u.id, '&', c.id, '-', ue.id, '#', ra.id) AS sample, ue.id AS tableid, clf.id AS moduleid, clf.name AS moduletype, c.id AS learningformatid, c.fullname AS name, ue.userid AS userid, CONCAT(u.firstname, ' ', u.lastname) AS username, ue.timecreated AS enroldate, u.open_costcenterid AS user_costcenterid, u.open_departmentid AS user_departmentid, c.open_costcenterid AS costcenterid, c.open_departmentid AS departmentid, c.timecreated as timecreated, ra.timemodified AS role_assign_timemodified, c.open_contentvendor AS open_contentvendor, c.open_subdepartment AS subdepartment, u.open_subdepartment AS user_subdepartment, cc.timecompleted AS completiondate, ue.completiondate AS upcomingdeadline, CONCAT('@',ue.id) AS refid
				FROM {user_enrolments} ue
				JOIN {enrol} e ON e.id = ue.enrolid
				JOIN {role_assignments} ra ON ra.userid = ue.userid
				JOIN {context} ct ON ct.id = ra.contextid
				JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'employee'
				JOIN {user} u ON u.id = ue.userid AND u.confirmed = 1 AND u.deleted = 0 
				JOIN {course} c ON c.id = e.courseid AND c.id = ct.instanceid 
				JOIN {local_courses_learningformat} clf ON clf.id = c.open_learningformat AND clf.name IN ('Online Course','Lab','Assessment', 'Webinar') 
				LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ue.userid
				WHERE 1 = 1 AND c.visible = 1 AND (ue.timecreated >= $learningformatstime OR
					u.timemodified >= $learningformatstime OR ue.timemodified >= $learningformatstime OR c.timemodified >= $learningformatstime OR cc.timecompleted >= $learningformatstime OR ra.timemodified >= $learningformatstime OR e.timemodified >= $learningformatstime)
				UNION ALL
				SELECT CONCAT(8, '@', llpu.userid, '&', llp.id, '-', llpu.id, '@') AS sample, llpu.id AS tableid, 8 AS moduleid, 'Learning path' AS moduletype, llp.id AS learningformatid, llp.name AS name, llpu.userid AS userid, CONCAT(u.firstname, ' ', u.lastname) AS username, llpu.timecreated AS enroldate,
				u.open_costcenterid AS user_costcenterid, u.open_departmentid AS user_departmentid, llp.costcenter AS costcenterid, llp.department AS departmentid, llp.timecreated AS timecreated, llpu.timecreated AS role_assign_timemodified, '0', llp.subdepartment AS subdepartment, u.open_subdepartment AS user_subdepartment, llpu.completiondate AS completiondate, llpu.lpdeadline AS upcomingdeadline, CONCAT('&',llpu.id) AS refid
				FROM {local_learningplan_user} AS llpu
				JOIN {local_learningplan} AS llp ON llp.id = llpu.planid
				JOIN {user} AS u ON u.id = llpu.userid AND u.confirmed = 1 AND u.deleted = 0 
				WHERE 1 = 1 AND (llpu.timecreated >= $learningformatstime OR llpu.timemodified >= $learningformatstime OR llp.timemodified >= $learningformatstime OR u.timemodified >= $learningformatstime)
				UNION ALL
				SELECT CONCAT(9, '@', lpu.userid, '&', lp.id, '-', lpu.id, '#') AS sample, lpu.id AS tableid, 9 AS moduleid, 'Program' AS moduletype, lp.id AS learningformarid, lp.name AS name, lpu.userid AS userid, CONCAT(u.firstname, ' ', u.lastname) AS username, lpu.timecreated AS enroldate, u.open_costcenterid AS user_costcenterid, u.open_departmentid AS user_departmentid, lp.costcenter AS costcenterid, lp.department AS departmentid, lp.timecreated AS timecreated, lpu.timecreated AS role_assign_timemodified, '0', lp.subdepartment AS subdepartment, u.open_subdepartment AS user_subdepartment, lpu.completiondate AS completiondate, lpu.programdeadline AS upcomingdeadline, CONCAT('$', lpu.id) AS refid
				FROM {local_program_users} as lpu
				JOIN {local_program} as lp on lp.id = lpu.programid
				JOIN {user} AS u ON u.id = lpu.userid
				WHERE 1 = 1 AND (lpu.timecreated >= $learningformatstime OR lpu.timemodified >= $learningformatstime OR lp.timemodified >= $learningformatstime AND u.timemodified >= $learningformatstime)
				UNION ALL
				SELECT CONCAT(10, '@', lcu.userid, '&', lc.id, '-', lcu.id, '$', lca.id) AS sample, lca.id AS tableid, 10 AS moduleid, 'Instructor-led courses' AS moduletype, lc.id AS learningformatid, lc.name AS name, lcu.userid AS userid, CONCAT(u.firstname, ' ', u.lastname) AS username, lcu.timecreated AS enroldate, u.open_costcenterid AS user_costcenterid, u.open_departmentid AS user_departmentid, lc.costcenter AS costcenterid, lc.department AS departmentid, lc.timecreated AS timecreated, lcu.timecreated AS role_assign_timemodified, '0', lc.subdepartment AS subdepartment, u.open_subdepartment AS user_subdepartment, IF ((lca.status = 1 AND lcs.timefinish < UNIX_TIMESTAMP()), lcs.timefinish, 0) AS completiondate, '0', CONCAT('#', lca.id) AS refid
				FROM {local_classroom_attendance} as lca
				JOIN {local_classroom_users} AS lcu ON lca.userid = lcu.userid
				JOIN {local_classroom} AS lc ON lc.id = lcu.classroomid AND lca.classroomid = lc.id
				JOIN {user} AS u ON u.id = lcu.userid AND u.confirmed = 1 AND u.deleted = 0 
				JOIN {local_classroom_sessions} lcs ON lcs.classroomid = lca.classroomid AND lcs.id = lca.sessionid
				WHERE 1 = 1 AND lca.enrol_status = 0 AND (lca.timecreated >= $learningformatstime OR lca.timemodified >= $learningformatstime OR lcu.timecreated >= $learningformatstime OR lcu.timemodified >= $learningformatstime OR lc.timemodified >= $learningformatstime OR u.timemodified >= $learningformatstime OR lcs.timefinish >= $learningformatstime)";
			$learningformatsinfo = $DB->get_recordset_sql($sql); 
			foreach ($learningformatsinfo as $lfinfo) {
                $lsinfodata = new stdClass;
                $lsinfodata->moduleid = $lfinfo->moduleid;
                $lsinfodata->moduletype = $lfinfo->moduletype;
                $lsinfodata->learningformatid = $lfinfo->learningformatid;
                $lsinfodata->name = $lfinfo->name;
                $lsinfodata->userid = $lfinfo->userid;
                $lsinfodata->username = $lfinfo->username;
                $lsinfodata->enroldate = $lfinfo->enroldate;
                $lsinfodata->timecreated = $lfinfo->timecreated;
                $lsinfodata->role_assign_timemodified = $lfinfo->role_assign_timemodified;
                $lsinfodata->open_contentvendor = !empty($lfinfo->open_contentvendor) ? $lfinfo->open_contentvendor : 0;
                $lsinfodata->completiondate = !empty($lfinfo->completiondate) ? $lfinfo->completiondate : 0;
                $lsinfodata->upcomingdeadline = !empty($lfinfo->upcomingdeadline) ? $lfinfo->upcomingdeadline : 0;
                $lsinfodata->overduedeadline = !empty($lfinfo->upcomingdeadline) ? $lfinfo->upcomingdeadline : 0;                        
                $lsinfodata->user_costcenterid = (!empty($lfinfo->user_costcenterid) && ($lfinfo->user_costcenterid > 0)) ? $lfinfo->user_costcenterid : 0;
                $lsinfodata->user_departmentid = (!empty($lfinfo->user_departmentid) && ($lfinfo->user_departmentid > 0)) ? $lfinfo->user_departmentid : 0;
                $lsinfodata->costcenterid = (!empty($lfinfo->costcenterid) && ($lfinfo->costcenterid > 0))? $lfinfo->costcenterid : 0;
                $lsinfodata->departmentid = (!empty($lfinfo->departmentid) && ($lfinfo->departmentid > 0)) ? $lfinfo->departmentid : 0;

                $lsinfodata->user_subdepartment = (!empty($lfinfo->user_subdepartment) && ($lfinfo->user_subdepartment > 0)) ? $lfinfo->user_subdepartment : 0;
                $lsinfodata->subdepartment = (!empty($lfinfo->subdepartment)&&($lfinfo->subdepartment> 0)) ? $lfinfo->subdepartment : 0;    
                $lsinfodata->refid = $lfinfo->refid;

                $records1 = $DB->get_field('block_ls_learningformats', 'id', 
                        array('moduleid' => $lsinfodata->moduleid,
                            'learningformatid' => $lsinfodata->learningformatid,
                                'userid' => $lsinfodata->userid,
                                'refid' => $lsinfodata->refid));

                if (!empty($records1)) {
                    $lsinfodata->id = $records1;
                    $lsinfodata->moduleid = $lsinfodata->moduleid;
                    $lsinfodata->moduletype = $lsinfodata->moduletype;
                    $lsinfodata->learningformatid = $lsinfodata->learningformatid;
                    $lsinfodata->name = $lsinfodata->name;
                    $lsinfodata->userid = $lsinfodata->userid;
                    $lsinfodata->username = $lsinfodata->username;
                    $lsinfodata->enroldate = $lsinfodata->enroldate;
                    $lsinfodata->timecreated = $lsinfodata->timecreated;
                    $lsinfodata->role_assign_timemodified = $lsinfodata->role_assign_timemodified;
                    $lsinfodata->open_contentvendor = $lsinfodata->open_contentvendor;
                    $lsinfodata->completiondate = $lsinfodata->completiondate;
                    $lsinfodata->upcomingdeadline = $lsinfodata->upcomingdeadline;
                    $lsinfodata->overduedeadline = $lsinfodata->overduedeadline;
                    $lsinfodata->user_costcenterid = $lsinfodata->user_costcenterid;
                    $lsinfodata->user_departmentid = $lsinfodata->user_departmentid;
                    $lsinfodata->costcenterid = $lsinfodata->costcenterid;
                    $lsinfodata->departmentid = $lsinfodata->departmentid;
                    $lsinfodata->user_subdepartment = $lsinfodata->user_subdepartment;
                    $lsinfodata->subdepartment = $lsinfodata->subdepartment;
                    $lsinfodata->refid = $lsinfodata->refid;
                    $DB->update_record('block_ls_learningformats', $lsinfodata);
                } else {
                    $DB->insert_record('block_ls_learningformats',  $lsinfodata);
                }       
            }
            $enrolrecords = $DB->get_recordset_sql("SELECT * FROM {block_ls_learningformats} WHERE moduleid = 10"); 
            foreach ($enrolrecords as $enrolrecord) {
                $userstatus = $DB->get_field_sql("SELECT enrol_status FROM {local_classroom_attendance} WHERE CONCAT('#',id) = '".$enrolrecord->refid."'");
                if ($userstatus != 0){
                    $DB->delete_records('block_ls_learningformats', array('id' => $enrolrecord->id));
                }
            }
			set_config('learningformats', time() ,'block_learnerscript');
			print_r("Completed");
		}
	}
	public function examsinformation() {
		global $DB;
		$examsinformation = get_config('block_learnerscript', 'examsinformation');
		if (!empty($examsinformation)) {
			$sql = "SELECT DISTINCT ue.id, c.id AS examid, c.fullname AS examname, ue.userid AS userid, CONCAT(u.firstname, ' ', u.lastname) AS username, lcc1.id AS vendorid, lcc1.vendorname, ue.timecreated AS enroldate, cc.timecompleted AS completiondate, ue.completiondate AS deadline, u.open_costcenterid AS user_costcenterid, u.open_departmentid AS user_departmentid, c.open_costcenterid AS costcenterid, c.open_departmentid AS departmentid, c.timecreated AS timecreated, ra.timemodified AS usermodified, c.open_contentvendor AS open_contentvendor, c.open_subdepartment AS subdepartment, u.open_subdepartment AS user_subdepartment, (SELECT cfd.intvalue
						FROM {customfield_data} cfd  
						JOIN {customfield_field} cff ON cff.id = cfd.fieldid AND cff.name = 'EOL' 
						WHERE cfd.instanceid = c.id) AS upcomingeol, (SELECT UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(cfd1.timemodified) , interval cfd1.charvalue month))
                        FROM {customfield_data} cfd1
                        JOIN {customfield_field} cff1 ON cff1.id = cfd1.fieldid AND cff1.name = 'Valid for (months)'
                        WHERE cfd1.charvalue != '' AND DATE_ADD(FROM_UNIXTIME(cfd1.timemodified) , interval cfd1.charvalue month) BETWEEN CURDATE()
                        AND DATE_ADD(CURDATE(), INTERVAL 90 DAY) AND cfd1.instanceid = c.id) AS upcomingexpiry, ue.id AS refid
					FROM {user_enrolments} ue
					JOIN {enrol} e ON e.id = ue.enrolid 
					JOIN {role_assignments} ra ON ra.userid = ue.userid
					JOIN {context} ct ON ct.id = ra.contextid
					JOIN {role} rl ON rl.id = ra.roleid AND rl.shortname = 'employee'
					JOIN {user} u ON u.id = ue.userid AND u.confirmed = 1 AND u.deleted = 0 
					JOIN {course} c ON c.id = e.courseid AND c.id = ct.instanceid 
					LEFT JOIN {local_courses_venderslist} lcc1 ON lcc1.id = c.open_vendor
					JOIN {local_courses_learningformat} clf ON clf.id = c.open_learningformat AND clf.name = 'Exam' 
					LEFT JOIN {course_completions} cc ON cc.course = c.id AND cc.userid = ue.userid 
					WHERE 1 = 1 AND ct.contextlevel = 50 AND (ue.timecreated >= $examsinformation OR
					u.timemodified >= $examsinformation OR ue.timemodified >= $examsinformation OR c.timemodified >= $examsinformation OR cc.timecompleted >= $examsinformation OR ra.timemodified >= $examsinformation OR lcc1.timemodified >= $examsinformation OR e.timemodified >= $examsinformation) ";
			$examrecords = $DB->get_recordset_sql($sql, array());
			foreach ($examrecords as $examrecord) {
			 	$record = new stdClass;
			 	$record->examid = $examrecord->examid;
			 	$record->examname = $examrecord->examname;
			 	$record->vendorid = !empty($examrecord->vendorid) ? $examrecord->vendorid : 0;
			 	$record->vendorname = !empty($examrecord->vendorname) ? $examrecord->vendorname : '';
			 	$record->userid = $examrecord->userid;
			 	$record->username = $examrecord->username;
			 	$record->enroldate = $examrecord->enroldate;
			 	$record->completiondate = !empty($examrecord->completiondate) ? $examrecord->completiondate : 0;
			 	$record->deadline = !empty($examrecord->deadline) ? $examrecord->deadline : 0;
			 	$record->user_costcenterid = (!empty($examrecord->user_costcenterid) && ($examrecord->user_costcenterid > 0)) ? $examrecord->user_costcenterid : 0;
			 	$record->user_departmentid = (!empty($examrecord->user_departmentid) && ($examrecord->user_departmentid > 0)) ? $examrecord->user_departmentid : 0;
			 	$record->costcenterid = (!empty($examrecord->costcenterid) && ($examrecord->costcenterid > 0)) ? $examrecord->costcenterid : 0;
			 	$record->departmentid = (!empty($examrecord->departmentid) && ($examrecord->departmentid > 0) ) ? $examrecord->departmentid : 0;
			 	$record->timecreated = !empty($examrecord->timecreated) ? $examrecord->timecreated : 0;
			 	$record->usermodified = !empty($examrecord->usermodified) ? $examrecord->usermodified : 0;
			 	$record->open_contentvendor = !empty($examrecord->open_contentvendor) ? $examrecord->open_contentvendor : 0;
			 	
			 	$record->subdepartment = (!empty($examrecord->subdepartment) && ($examrecord->subdepartment > 0))? $examrecord->subdepartment : 0;
			 	$record->user_subdepartment = (!empty($examrecord->user_subdepartment) && ($examrecord->user_subdepartment > 0)) ? $examrecord->user_subdepartment: 0;

			 	$record->upcomingeol = !empty($examrecord->upcomingeol) ? $examrecord->upcomingeol : 0; 
			 	$record->upcomingexpiry = !empty($examrecord->upcomingexpiry) ? $examrecord->upcomingexpiry : 0; 
			 	$record->refid = !empty($examrecord->refid) ? ($examrecord->refid) : 0 ;
			 	$records1 = $DB->get_field('block_ls_exams', 'id', 
		                    array('examid' => $record->examid,
		                    	'vendorid' => $record->vendorid,
		                    		'userid' => $record->userid,
		                    		'refid' => $record->refid));
			 	if (!empty($records1)) {
		           $record->id = $records1; 
		           $record->examid = $record->examid; 
		           $record->examname = $record->examname;
		           $record->vendorid = $record->vendorid;
		           $record->vendorname = $record->vendorname;
		           $record->userid = $record->userid;
		           $record->username = $record->username;
		           $record->enroldate = $record->enroldate;
		           $record->completiondate = $record->completiondate;
		           $record->deadline = $record->deadline;
		           $record->user_costcenterid = $record->user_costcenterid;
		           $record->user_departmentid = $record->user_departmentid;
		           $record->costcenterid = $record->costcenterid;
		           $record->departmentid = $record->departmentid;
		           $record->timecreated = $record->timecreated;
		           $record->usermodified = $record->usermodified;
		           $record->open_contentvendor = $record->open_contentvendor;
		           $record->subdepartment = $record->subdepartment;
		           $record->user_subdepartment = $record->user_subdepartment;
		           $record->upcomingeol = $record->upcomingeol;
		           $record->upcomingexpiry = $record->upcomingexpiry;
		           $record->refid = $record->refid;
		           $DB->update_record('block_ls_exams', $record);
			 	} else {
			 		$DB->insert_record('block_ls_exams',  $record);
			 	}
			} 
			set_config('examsinformation', time(), 'block_learnerscript');
			print_r('Completed updating exams data');
		}
	} 
	public function certificatesinfo() {
		global $DB;
		$certificatesinfo = get_config('block_learnerscript', 'certificatesinfo');
		if (!empty($certificatesinfo)) {
			$sql = "SELECT DISTINCT lcu.id, lc.id AS certificateid, lc.name AS certificatename, lccv.id AS vendorid, lccv.vendorname AS vendorname, u.id AS userid, CONCAT(u.firstname, ' ', u.lastname) AS username, lcu.timecreated AS enroldate, lcu.completiondate AS completiondate, lcu.certdeadline AS deadline, u.open_costcenterid AS user_costcenterid, u.open_departmentid AS user_departmentid, lc.costcenter AS costcenterid, lc.department AS departmentid, lc.timecreated AS timecreated, lc.timemodified AS usermodified, lc.open_vendor AS open_contentvendor, lc.subdepartment AS subdepartment, u.open_subdepartment AS user_subdepartment,lcu.expirydate AS upcomingexpiry, lc.eol AS upcomingeol, lcu.id AS courseid
	                FROM {local_certification_users} lcu
					JOIN {local_certification} lc ON lc.id = lcu.certificationid
					LEFT JOIN {local_courses_venderslist} lccv ON lccv.id = lc.open_vendor
					JOIN {user} AS u ON u.id = lcu.userid 
					WHERE 1 = 1 AND (lcu.timecreated >= $certificatesinfo OR lcu.timemodified >= $certificatesinfo OR u.timemodified >= $certificatesinfo OR lc.timemodified >= $certificatesinfo OR lccv.timemodified >= $certificatesinfo) ";
			$certificaterecords = $DB->get_recordset_sql($sql);
			foreach ($certificaterecords as $certificaterecord) {
				if($certificaterecord->certificateid){
					$certrecord = new stdClass;
					$certrecord->certificateid = $certificaterecord->certificateid;
					$certrecord->certificatename = $certificaterecord->certificatename;
					$certrecord->vendorid = !empty($certificaterecord->vendorid) ? $certificaterecord->vendorid : 0;
					$certrecord->vendorname = !empty($certificaterecord->vendorname) ? $certificaterecord->vendorname : '';
					$certrecord->userid = $certificaterecord->userid;
					$certrecord->username = $certificaterecord->username;
					$certrecord->courseid = !empty($certificaterecord->courseid) ? $certificaterecord->courseid : 0;
					$certrecord->enroldate = $certificaterecord->enroldate;
					$certrecord->completiondate = !empty($certificaterecord->completiondate) ? $certificaterecord->completiondate : 0;
					$certrecord->deadline = !empty($certificaterecord->deadline) ? $certificaterecord->deadline : 0;
					$certrecord->upcomingexpiry = !empty($certificaterecord->upcomingexpiry) ? $certificaterecord->upcomingexpiry : 0;
					$certrecord->upcomingeol = !empty($certificaterecord->upcomingeol) ? $certificaterecord->upcomingeol : 0;

					$certrecord->user_costcenterid = (!empty($certificaterecord->user_costcenterid) && ($certificaterecord->user_costcenterid > 0)) ? $certificaterecord->user_costcenterid : 0;
					$certrecord->user_departmentid = (!empty($certificaterecord->user_departmentid) && ($certificaterecord->user_departmentid > 0)) ? $certificaterecord->user_departmentid : 0; 
					$certrecord->costcenterid = (!empty($certificaterecord->costcenterid) && ($certificaterecord->costcenterid > 0)) ? $certificaterecord->costcenterid : 0;
					$certrecord->departmentid = (!empty($certificaterecord->departmentid) && ($certificaterecord->departmentid > 0)) ? $certificaterecord->departmentid : 0;
					$certrecord->timecreated = !empty($certificaterecord->timecreated) ? $certificaterecord->timecreated : 0;
				 	$certrecord->usermodified = !empty($certificaterecord->usermodified) ? $certificaterecord->usermodified : 0;
				 	$certrecord->open_contentvendor = !empty($certificaterecord->open_contentvendor) ? $certificaterecord->open_contentvendor : 0;
				 	$certrecord->subdepartment = (!empty($certificaterecord->subdepartment) && ($certificaterecord->subdepartment > 0)) ? $certificaterecord->subdepartment : 0;
				 	$certrecord->user_subdepartment = (!empty($certificaterecord->user_subdepartment) && ($certificaterecord->user_subdepartment > 0)) ? $certificaterecord->user_subdepartment : 0; 

				 	$records1 = $DB->get_field('block_ls_certificates', 'id', 
		                    array('certificateid' => $certrecord->certificateid,
		                    	'vendorid' => $certrecord->vendorid,
		                    		'userid' => $certrecord->userid,
		                    		'courseid' => $certrecord->courseid));
				 	if (!empty($records1)) {
			            $certrecord->id = $records1;
			            $certrecord->certificateid = $certrecord->certificateid;
			            $certrecord->certificatename = $certrecord->certificatename;
			            $certrecord->vendorid = $certrecord->vendorid;
			            $certrecord->vendorname = $certrecord->vendorname;
			            $certrecord->userid = $certrecord->userid;
			            $certrecord->username = $certrecord->username;
			            $certrecord->courseid = $certrecord->courseid;
			            $certrecord->enroldate = $certrecord->enroldate;
			            $certrecord->completiondate = $certrecord->completiondate;
			            $certrecord->deadline = $certrecord->deadline;
			            $certrecord->upcomingexpiry = $certrecord->upcomingexpiry;
			            $certrecord->upcomingeol = $certrecord->upcomingeol;
			            $certrecord->user_costcenterid = $certrecord->user_costcenterid;
			            $certrecord->user_departmentid = $certrecord->user_departmentid;
			            $certrecord->costcenterid = $certrecord->costcenterid;
			            $certrecord->departmentid = $certrecord->departmentid;
			            $certrecord->timecreated = $certrecord->timecreated;
			            $certrecord->usermodified = $certrecord->usermodified;
			            $certrecord->open_contentvendor = $certrecord->open_contentvendor;
			            $certrecord->subdepartment = $certrecord->subdepartment;
			            $certrecord->user_subdepartment = $certrecord->user_subdepartment;
			            $DB->update_record('block_ls_certificates', $certrecord);
				 	} else {
			        	$DB->insert_record('block_ls_certificates',  $certrecord);
			        }
				}
			}
			set_config('certificatesinfo', time(), 'block_learnerscript');
			print_r('Completed updating certifications data');
		}
	}
}
