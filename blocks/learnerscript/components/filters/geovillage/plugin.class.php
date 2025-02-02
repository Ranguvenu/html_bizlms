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
use block_learnerscript\local\pluginbase;

class plugin_geovillage extends pluginbase {

    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->placeholder = true;
        $this->maxlength = 0;
        $this->filtertype = 'custom'; 
        if (!empty($this->reportclass->basicparams)) {
            foreach ($this->reportclass->basicparams as $basicparam) {
                if ($basicparam['name'] == 'geovillage') {
                    $this->filtertype = 'basic';
                }
            }
        }
        $this->fullname = get_string('filtergeovillage', 'block_learnerscript');
        $this->reporttypes = array('sql','coursesoverview');
    }

    public function summary($data) {
        return get_string('filtergeovillage_summary', 'block_learnerscript');
    }

    public function execute($finalelements, $data) {

        $filterusers = optional_param('filter_geovillage', 0, PARAM_RAW);
        if (!$filterusers) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return array($filterusers);
        } else {
            if (preg_match("/%%FILTER_GEOVILLAGE:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filterusers;
                return str_replace('%%FILTER_GEOVILLAGE:' . $output[1] . '%%', $replace,
                    $finalelements);
            }
        }
        return $finalelements;
    }

    public function filter_data($selectoption = true, $request){
        global $DB;
        $condition = '';
        if(!is_siteadmin()){
            $condition = (new \local_costcenter\lib\accesslib())::get_costcenter_path_field_concatsql($columnname='lc.path');
        }
        $sql = " SELECT lv.id, lv.village_name
                    FROM {local_village} AS lv
                    JOIN {local_costcenter} AS lc ON lc.id = lv.costcenterid
                    WHERE 1=1 {$condition}
                    ORDER BY lv.id ASC ";

        $geovillages = $DB->get_records_sql_menu($sql);
        $geovillages =array_replace(array(0=>get_string('selectvillage', 'usersprofilefields_village')),$geovillages);
        ksort($geovillages);
        return $geovillages;
    }

    public function selected_filter($selected, $request = array()) {
        $filterdata = $this->filter_data(false, $request);
        return $filterdata[$selected];
    }

    public function print_filter(&$mform) {
        $selectoption = true;
        $request = array_merge($_POST, $_GET);
        $geovillages = $this->filter_data(false, $request);
        if ((!$this->placeholder || $this->filtertype == 'basic') && COUNT($geovillages) > 1) {
            unset($geovillages[0]);
        }
        $select = $mform->addElement('select', 'filter_geovillage', null,
        $geovillages,
        array('data-select2' => true,
              'data-maximum-selection-length' => $this->maxlength,
              'data-action' => 'filtergeovillage',
              'data-instanceid' => $this->reportclass->config->id));
        $select->setHiddenLabel(true);
        $mform->setType('filter_geovillage', PARAM_INT);

    }
}