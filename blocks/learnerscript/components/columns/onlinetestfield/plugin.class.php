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
use block_learnerscript\local\reportbase;
class plugin_onlinetestfield extends pluginbase {

    public function init() {
        $this->fullname = get_string('onlinetestfield', 'block_learnerscript');
        $this->type = 'advanced';
        $this->form = true;
        $this->reporttypes = array();
    }

    public function summary($data) {
        return format_string($data->columname);
    }

    public function colformat($data) {
        $align = (isset($data->align)) ? $data->align : '';
        $size = (isset($data->size)) ? $data->size : '';
        $wrap = (isset($data->wrap)) ? $data->wrap : '';
        return array($align, $size, $wrap);
    }

    // Data -> Plugin configuration data.
    // Row -> Complete onlinetestinfo obj.,
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB;

        $sql = "SELECT c.*,c.fullname as name, gi.gradepass, gi.grademax 
                FROM {course} c
                JOIN {quiz} q ON c.id = q.course
                JOIN {grade_items} gi ON gi.iteminstance = q.id AND itemtype = 'mod'
                                        AND itemmodule = 'quiz'
                WHERE c.id = :onlinetestid AND c.visible = 1 AND c.open_coursetype = :type 
                AND open_module = :module ";
        list($zero, $org, $ctr, $bu, $cu, $territory) = explode("/",$row->open_path);
        $onlinetestrecord = $DB->get_record_sql($sql, array('onlinetestid'=>$row->courseid,'module' => 'online_exams','type'=>1));

        switch ($data->column) {
            case 'onlinetestname':
                $onlinetestrecord->{$data->column} = $onlinetestrecord->name;
                break;
            case 'passgrade':
                $onlinetestrecord->{$data->column} = round($onlinetestrecord->gradepass, 2);
            break;
            case 'maxgrade':
                $onlinetestrecord->{$data->column} = round($onlinetestrecord->grademax, 2);
            break;
            case 'quizopendate':
                $onlinetestrecord->{$data->column} = ($onlinetestrecord->timeopen) ? date('d-M-Y', $onlinetestrecord->timeopen) : 'NA';
            break;                
            case 'quizclosedate':
                $onlinetestrecord->{$data->column} = ($onlinetestrecord->timeclose) ? date('d-M-Y', $onlinetestrecord->timeclose) : 'NA';
                break;
            case 'points':
                $onlinetestrecord->{$data->column} = ($onlinetestrecord->open_points) ? $onlinetestrecord->open_points : 'NA';
                break;                
            case get_string('onlinetest_org', 'local_costcenter'):
                $onlinetestrecord->{$data->column} = $DB->get_field('local_costcenter', 'fullname', array('id' =>$org));
                break;
            case get_string('onlinetest_dept', 'local_costcenter'):
                if($onlinetestrecord->departmentid){
                    $onlinetestrecord->{$data->column} = $DB->get_field('local_costcenter', 'fullname', array('id' =>$ctr));
                }else{
                   $onlinetestrecord->{$data->column} = get_string('all'); 
                }
                break;
            default:
                $onlinetestrecord->{$data->column} = $onlinetestrecord->{$data->column};
            break;
        }
       return (isset($onlinetestrecord->{$data->column})) ? $onlinetestrecord->{$data->column} : '';
    }
}
