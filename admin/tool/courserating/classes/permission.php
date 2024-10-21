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

namespace tool_courserating;

use required_capability_exception;
use tool_courserating\local\models\rating;

/**
 * Permission checks
 *
 * @package     tool_courserating
 * @copyright   2022 Marina Glancy <marina.glancy@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission {

    /**
     * User can view rating for the course (ratings are enabled for this course)
     *
     * @param int $componentid
     * @return bool
     */
    //public $compnames;
    public static function can_view_ratings(int $componentid,string $componentname): bool {
        global $DB,$USER,$PAGE,$OUTPUT;
        $userid = $USER->id;
        if (helper::get_course_rating_mode($componentid) == constants::RATEBY_NOONE) {
            return false;
        }
        
        switch($componentname){
          case "local_courses":

              $course = get_course($componentid);

              $context = \context_course::instance($componentid);

              $return = \core_course_category::can_view_course_info($course) ||
                is_enrolled($context, $USER, '', true);
          break; 
          case "local_learningplan":
          $sql = "SELECT lc.id, lc.name, lc.description
                  FROM {local_learningplan} AS lc
                  JOIN {local_learningplan_user} AS lcu ON lcu.planid = lc.id
                  WHERE userid = :userid AND lc.id = :componentid";
                 $isenrolled = $DB->get_record_sql($sql, array('userid' => $userid,'componentid' => $componentid));
                $categorycontext = (new \local_learningplan\lib\accesslib())::get_module_context($componentid);
                $categorycontext = $categorycontext ? 1 : 0;
                $isenrolled = $isenrolled ? 1 : 0;
                $return = $categorycontext ? $categorycontext : $isenrolled;
          break;
          case "local_program":
          $sql = "SELECT lc.id, lc.name, lc.description
                  FROM {local_program} AS lc
                  JOIN {local_program_users} AS lcu ON lcu.programid = lc.id
                  WHERE userid = :userid AND lc.id = :componentid AND lc.status IN (1, 4)";
                 $isenrolled = $DB->get_record_sql($sql, array('userid' => $userid,'componentid' => $componentid));
                $categorycontext = (new \local_program\lib\accesslib())::get_module_context($componentid);
                $categorycontext = $categorycontext ? 1 : 0;
                $isenrolled = $isenrolled ? 1 : 0;
                $return = $categorycontext ? $categorycontext : $isenrolled;
          break;
          case "local_classroom":
               $sql = "SELECT lc.id, lc.name, lc.description
                  FROM {local_classroom} AS lc
                  JOIN {local_classroom_users} AS lcu ON lcu.classroomid = lc.id
                  WHERE userid = :userid AND lc.id = :componentid AND lc.status IN (1, 4)";
                 $isenrolled = $DB->get_record_sql($sql, array('userid' => $userid,'componentid' => $componentid));
                $categorycontext = (new \local_program\lib\accesslib())::get_module_context($componentid);
                $categorycontext = $categorycontext ? 1 : 0;
                $isenrolled = $isenrolled ? 1 : 0;
                $return = $categorycontext ? $categorycontext : $isenrolled;
          break;
        }
     
        return $return;
    }

    /**
     * Can the current user add a rating for the specified course
     *
     * Example of checking last access:
     *     $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', ['userid' => $USER->id, 'componentid' => $componentid]);
     *
     * @param int $componentid
     * @return bool
     * @throws \coding_exception
     */
    public static function can_add_rating(int $componentid,string $componentname=null): bool {
        global $CFG, $USER;
        switch($componentname){
            case "local_courses";
                 if (!has_capability('tool/courserating:rate', \context_course::instance($componentid))) {
                   return false;
                 }
            break;
            // case "local_program";
            //     $categorycontext = (new \local_program\lib\accesslib())::get_module_context($componentid);
            //          if (!has_capability('tool/courserating:rate', $categorycontext)) {
            //            return false;
            //          }
            // break;
            //  case "local_classroom";
            //     $categorycontext = (new \local_classroom\lib\accesslib())::get_module_context($componentid);
            //          if (!has_capability('tool/courserating:rate', $categorycontext)) {
            //            return false;
            //          }
            // break;
            //  case "local_learningplan";
            //     $categorycontext = (new \local_learningplan\lib\accesslib())::get_module_context($componentid);
            //          if (!has_capability('tool/courserating:rate', $categorycontext)) {
            //            return false;
            //          }
            // break;
        }
       
        $courseratingmode = helper::get_course_rating_mode($componentid);
        if ($courseratingmode == constants::RATEBY_NOONE) {
            return false;
        }
        if ($courseratingmode == constants::RATEBY_COMPLETED) {
            require_once($CFG->dirroot.'/completion/completion_completion.php');
            // The course is supposed to be marked as completed at $timeend.
            $ccompletion = new \completion_completion(['userid' => $USER->id, 'course' => $componentid]);
            return $ccompletion->is_complete();
        }
        return true;
    }

    /**
     * Does current user have capability to delete ratings
     *
     * @param int $ratingid
     * @param int|null $componentid
     * @return bool
     */
    public static function can_delete_rating(int $ratingid, ?int $componentid = null): bool {
        if (!$componentid) {
            $componentid = (new rating($ratingid))->get('componentid');
         }   
         $component = (new rating($ratingid))->get('component');
        
         $context = ($component == 'local_courses' ) ? \context_course::instance($componentid) : \context_system::instance();
         if($component =="local_courses"){
          return has_capability('tool/courserating:delete', $context);
         }{
            return true;
         }
        
    }

    /**
     * Can current user flag the rating
     *
     * @param int $ratingid
     * @param int|null $componentid course id if known (saves a DB query)
     * @return bool
     */
    public static function can_flag_rating(int $ratingid, ?int $componentid = null): bool {
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        if (!$componentid) {
            $componentid = (new rating($ratingid))->get('componentid');
        }
         if (!$component) {
            $component = (new rating($ratingid))->get('component');
        }
        return self::can_view_ratings($componentid,$component);
    }

    /**
     * User can view the 'Course ratings' item in the course administration
     *
     * @param int $componentid
     * @return bool
     */
    public static function can_view_report(int $componentid): bool {
        if (!helper::course_ratings_enabled_anywhere()) {
            return false;
        }
        $context = \context_course::instance($componentid);
        return has_capability('tool/courserating:reports', $context);
    }

    /**
     * Check that user can view rating or throw exception
     *
     * @param int $componentid
     * @throws \moodle_exception
     */
    public static function require_can_view_ratings(int $componentid,string $componentname): void {
        if (!self::can_view_ratings($componentid,$componentname)) {
            throw new \moodle_exception('cannotview', 'tool_courserating');
        }
    }

    /**
     * Check that user can add/change rating or throw exception
     *
     * @param int $componentid
     * @throws \moodle_exception
     */
    public static function require_can_add_rating(int $componentid): void {
        if (!self::can_add_rating($componentid)) {
            throw new \moodle_exception('cannotrate', 'tool_courserating');
        }
    }

    /**
     * Check that user can delete rating or throw exception
     *
     * @param int $ratingid
     * @param int|null $componentid
     * @throws required_capability_exception
     */
    public static function require_can_delete_rating(int $ratingid, ?int $componentid = null): void {
        if (!$componentid) {
            $componentid = (new rating($ratingid))->get('componentid');
        }
        if (!self::can_delete_rating($ratingid, $componentid)) {
            throw new required_capability_exception(\context_course::instance($componentid),
                'tool/courserating:delete', 'nopermissions', '');
        }
    }

    /**
     * Check that user can flag rating or throw exception
     *
     * @param int $ratingid
     * @param int|null $componentid
     * @throws \moodle_exception
     */
    public static function require_can_flag_rating(int $ratingid, ?int $componentid = null): void {
        if (!self::can_flag_rating($ratingid, $componentid)) {
            throw new \moodle_exception('cannotview', 'tool_courserating');
        }
    }

    /**
     * Check that user can view rating or throw exception
     *
     * @param int $componentid
     * @throws \moodle_exception
     */
    public static function require_can_view_reports(int $componentid): void {
        if (!\tool_courserating\helper::course_ratings_enabled_anywhere()) {
            // TODO create a new string, maybe link to settings for admins?
            throw new \moodle_exception('ratebynoone', 'tool_courserating');
        }
        if (!self::can_view_report($componentid)) {
            throw new required_capability_exception(\context_course::instance($componentid),
                'tool/courserating:reports', 'nopermissions', '');
        }
    }
}
