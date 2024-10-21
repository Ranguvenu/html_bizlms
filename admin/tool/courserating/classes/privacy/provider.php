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

namespace tool_courserating\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use tool_courserating\api;
use tool_courserating\local\models\rating;
use tool_courserating\task\reindex;

/**
 * Privacy provider
 *
 * @package     tool_courserating
 * @copyright   2022 Marina Glancy <marina.glancy@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * get_metadata
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'tool_courserating_rating',
            [
                'id' => 'privacy:metadata:tool_courserating_rating:id',
                'componentid' => 'privacy:metadata:tool_courserating_rating:cohortid',
                'userid' => 'privacy:metadata:tool_courserating_rating:userid',
                'rating' => 'privacy:metadata:tool_courserating_rating:rating',
                'review' => 'privacy:metadata:tool_courserating_rating:review',
                'hasreview' => 'privacy:metadata:tool_courserating_rating:hasreview',
                'timecreated' => 'privacy:metadata:tool_courserating_rating:timecreated',
                'timemodified' => 'privacy:metadata:tool_courserating_rating:timemodified',
            ],
            'privacy:metadata:tool_courserating_rating'
        );

        $collection->add_database_table(
            'tool_courserating_flag',
            [
                'id' => 'privacy:metadata:tool_courserating_flag:id',
                'ratingid' => 'privacy:metadata:tool_courserating_flag:ratingid',
                'userid' => 'privacy:metadata:tool_courserating_flag:userid',
                'reasoncode' => 'privacy:metadata:tool_courserating:reasoncode',
                'reason' => 'privacy:metadata:tool_courserating:reason',
                'timecreated' => 'privacy:metadata:tool_courserating:timecreated',
                'timemodified' => 'privacy:metadata:tool_courserating:timemodified',
            ],
            'privacy:metadata:tool_courserating_flag'
        );

        return $collection;
    }

    /**
     * get_contexts_for_userid
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Own ratings.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {tool_courserating_rating} r
                   JOIN {context} ctx
                       ON ctx.instanceid = r.componentid AND ctx.contextlevel = :coursecontext
                 WHERE r.userid = :userid";

        $params = [
            'userid'        => $userid,
            'coursecontext' => CONTEXT_COURSE,
        ];

        $contextlist->add_from_sql($sql, $params);

        // Flags for other ratings.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {tool_courserating_rating} r
                  JOIN {tool_courserating_flag} f ON f.ratingid = r.id
                   JOIN {context} ctx
                       ON ctx.instanceid = r.componentid AND ctx.contextlevel = :coursecontext
                 WHERE f.userid = :userid";

        $params = [
            'userid'        => $userid,
            'coursecontext' => CONTEXT_COURSE,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * export_user_data
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $componentids = [];
        foreach ($contextlist->get_contexts() as $context) {
            $componentids[] = $context->instanceid;
        }

        if (empty($componentids)) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        list($coursesql, $courseparams) = $DB->get_in_or_equal($componentids, SQL_PARAMS_NAMED);

        // Retrieve the tool_courserating_rating records created for the user.
        $sql = "SELECT r.id, r.userid, r.componentid, r.rating, r.review, r.hasreview, r.timecreated, r.timemodified,
                       c.shortname,
                       c.fullname
                  FROM {tool_courserating_rating} r
                  JOIN {course} c ON c.id = r.componentid
                 WHERE r.userid = :userid
                       AND c.id {$coursesql}";

        $params = ['userid' => $userid] + $courseparams;

        $ratings = $DB->get_records_sql($sql, $params);

        foreach ($ratings as $rating) {
            $subcontext = [
                get_string('pluginname', 'tool_courserating'),
                $rating->shortname,
            ];

            $data = (object) [
                'shortname' => $rating->shortname,
                'fullname' => $rating->fullname,
                'rating' => $rating->rating,
                'review' => $rating->review,
                'hasreview' => $rating->hasreview,
                'userid' => transform::user($rating->userid),
                'timecreated' => transform::datetime($rating->timecreated),
                'timemodified' => transform::datetime($rating->timemodified),
            ];

            $context = \context_course::instance($rating->componentid);
            writer::with_context($context)->export_data($subcontext, $data);
        }

        // TODO export flags.
    }

    /**
     * delete_data_for_all_users_in_context
     *
     * @param \context $context
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        if ($context->contextlevel == CONTEXT_COURSE) {
            api::delete_all_data_for_course($context->instanceid);
        }
    }

    /**
     * delete_data_for_user
     *
     * @param approved_contextlist $contextlist
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $componentids = [];
        foreach ($contextlist->get_contexts() as $context) {
            $componentids[] = $context->instanceid;
        }
        $userid = $contextlist->get_user()->id;
        self::delete_data_for_user_in_courses($userid, $componentids);
    }

    /**
     * delete_data_for_user_in_courses
     *
     * @param int $userid
     * @param array $componentids
     */
    protected static function delete_data_for_user_in_courses(int $userid, array $componentids) {
        global $DB;
        if (!$userid || empty($componentids)) {
            return;
        }
        [$sql, $params] = $DB->get_in_or_equal($componentids, SQL_PARAMS_NAMED);
        $sqlrating = 'SELECT componentid FROM {tool_courserating_rating} WHERE componentid '.$sql.' AND userid = :userid';
        $params['userid'] = $userid;
        $sqlflags = 'SELECT f.id FROM {tool_courserating_flag} f JOIN {tool_courserating_rating} r ON f.ratingid = r.id
            WHERE r.componentid '.$sql.' AND f.userid = :userid';
        $flags = $DB->get_fieldset_sql($sqlflags, $params);
        if ($flags) {
            [$sqlf, $pf] = $DB->get_in_or_equal($flags);
            $DB->execute('DELETE FROM {tool_courserating_flag} WHERE id '.$sqlf, $pf);
        }
        $affectedcourses = $DB->get_fieldset_sql($sqlrating, $params);
        foreach ($affectedcourses as $cid) {
            $DB->delete_records(rating::TABLE, ['userid' => $userid, 'componentid' => $cid]);
            reindex::schedule_course($cid);
        }
    }

    /**
     * get_users_in_context
     *
     * @param userlist $userlist
     * @return void
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $sql = "SELECT DISTINCT r.userid
                  FROM {tool_courserating_rating} r
                 WHERE r.componentid = :componentid";
        $params = ['componentid' => $context->instanceid];

        $userlist->add_from_sql('userid', $sql, $params);

        $sql = "SELECT DISTINCT f.userid
                  FROM {tool_courserating_rating} r
                  JOIN {tool_courserating_flag} f ON f.ratingid = r.id
                 WHERE r.componentid = :componentid";
        $params = ['componentid' => $context->instanceid];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * delete_data_for_users
     *
     * @param approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        if ($userlist->get_context()->contextlevel != CONTEXT_COURSE) {
            return;
        }
        $componentid = $userlist->get_context()->instanceid;
        foreach ($userlist->get_userids() as $userid) {
            self::delete_data_for_user_in_courses($userid, [$componentid]);
        }
    }
}
