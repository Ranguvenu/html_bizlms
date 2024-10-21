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
 * @subpackage local_customform
 */
require_once(dirname(__FILE__) . '/../../config.php');
global $CFG;
require_once($CFG->dirroot . '/lib/moodlelib.php');

function local_custom_category_output_fragment_new_custom_category_form($args) {
    global $CFG, $DB;
    $args = (object) $args;
    $context = $args->context;
    $repositoryid = $args->repositoryid;
    $parentcatid = $args->parentcatid;
    $o = '';
    $formdata = [];
    $querylib = new \local_custom_category\querylib();
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        if (is_object($serialiseddata)) {
            $serialiseddata = serialize($serialiseddata);
        }
        parse_str($serialiseddata, $formdata);
    }
    if ($args->repositoryid > 0) {
        $heading = get_string('updatecuscategory', 'local_custom_category');
        $data = $querylib->category_record(array('id' => $repositoryid));
    }

    $mform = new local_custom_category\form\custom_category_form(null, array('id' => $args->repositoryid, 'editoroptions' => $editoroptions, 'open_costcenterid' => $data->costcenterid, 'parentid' => $data->parentid, 'parentcatid' => $parentcatid), 'post', '', null, true, $formdata);
    if ($data) {
        $data->name = $data->fullname;
        $data->parentid = $data->parentid ? $data->parentid : 'Top';
        $data->open_costcenterid = $data->costcenterid;
        $mform->set_data($data);
    }

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o .= ob_get_contents();
    ob_end_clean();
    return $o;
}

function local_custom_category_masterinfo() {
    global $CFG, $PAGE, $OUTPUT, $DB, $USER;
    $costcenterid = explode('/', $USER->open_path)[1];
    $categorycontext = (new \local_courses\lib\accesslib())::get_module_context();
    $categoryquerylib = new \local_custom_category\querylib();
    $content = '';
    if (has_capability('local/custom_category:view_custom_category', $categorycontext) || is_siteadmin()) {
        $totalcourse_category = $categoryquerylib->category_count(array('costcenterid' => $costcenterid));

        if ($totalcourse_category > 0) {
            $cat = '(' . $totalcourse_category . ')';
        }
        $templatedata = array();
        $templatedata['show'] = true;
        $templatedata['count'] = $cat;
        $templatedata['link'] = $CFG->wwwroot . '/local/custom_category/index.php';
        $templatedata['stringname'] = get_string('category', 'block_masterinfo');
        $templatedata['icon'] = '<i class="fa fa-cubes"></i>';

        $content = $OUTPUT->render_from_template('block_masterinfo/masterinfo', $templatedata);
    }
    return array('3' => $content);
}

/**
 * To set the menu.
 *
 * @return array
 */
function local_custom_category_leftmenunode() {

    $categorycontext = (new \local_costcenter\lib\accesslib())::get_module_context();
    $categorynodes = '';
    if (has_capability('local/custom_category:manage_custom_category', $categorycontext) || is_siteadmin()) {
        $categorynodes .= html_writer::start_tag('li', array('id' => 'id_leftmenu_categories', 'class' => 'pull-left user_nav_div categories usernavdep'));
        $categories_url = new moodle_url('/local/custom_category/index.php');
        $categories = html_writer::link($categories_url, '<i class="fa fa-line-chart" style="font-size:12px"></i><span class="user_navigation_link_text">' . get_string('performance_categories', 'local_custom_category') . '</span>', array('class' => 'user_navigation_link'));
        $categorynodes .= $categories;
        $categorynodes .= html_writer::end_tag('li');
    }

    return array('4' => $categorynodes);
}

//////For display on index page//////////
function custom_category_details($tablelimits, $filtervalues) {
    global $DB, $USER, $CFG;
    $systemcontext = (new \local_custom_category\lib\accesslib())::get_module_context();
    $querylib = new \local_custom_category\querylib();
    $countsql = "SELECT count(lcc.id) FROM {local_custom_category} AS lcc WHERE 1 ";
    $selectsql = "SELECT lcc.*, lc.fullname as organisationname
        FROM {local_custom_category} AS lcc
        JOIN {local_costcenter} AS lc ON lc.id = lcc.costcenterid
        WHERE 1 ";
    if ($tablelimits->parentcatid > 0) {
        $concatsql = " AND lcc.parentid = " . $tablelimits->parentcatid;
    } else {
        $concatsql = " AND lcc.parentid = 0 ";
    }
    $queryparam = array();
    if (!is_siteadmin()) {
        $costcenterid = explode("/", $USER->open_path);
        $concatsql .= " AND lcc.costcenterid= :usercostcenter ";
        $queryparam['usercostcenter'] = $costcenterid[1];
    }
    if (isset($filtervalues->search_query) && trim($filtervalues->search_query) != '') {
        $concatsql .= " AND (lcc.fullname LIKE :search1 )";
        $queryparam['search1'] = '%' . trim($filtervalues->search_query) . '%';
    }
    $count = $DB->count_records_sql($countsql . $concatsql, $queryparam);
    $concatsql .= " order by lcc.id desc";
    $records = $DB->get_records_sql($selectsql . $concatsql, $queryparam, $tablelimits->start, $tablelimits->length);

    $list = array();
    $data = array();

    if ($records) {
        foreach ($records as $c) {
            $list = array();
            $categoryparent = $querylib->category_exist(array('parentid' => $c->id));
            $parent = $querylib->category_field('fullname', array('id' => $c->parentid));
            $childcount = $querylib->category_child_count(array('parentid' => $c->id));
            $list['custom_category_name'] = $c->fullname;
            $list['organisationname'] = $c->organisationname;
            $list['custom_category_id'] = $c->id;
            $list['shortname'] = $c->shortname;
            $list['type'] = $c->type;
            $list['parent'] = $parent ? $parent : 'N/A';
            $list['childcount'] = $childcount ? $childcount : 0;
            $list['wwwroot'] = $CFG->dirroot . '/local/custom_category/category.php?';
            $list['childs'] = $tablelimits->parentcatid > 0 ? $tablelimits->parentcatid : 0;
            if ($categoryparent) {
                $list['categoryexist'] = $categoryparent;
            }
            $data[] = $list;
        }
    }
    return array('count' => $count, 'data' => $data);
}
