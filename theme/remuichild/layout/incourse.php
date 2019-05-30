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

/**
 * A two column layout for the Edwiser RemUI theme.
 *
 * @package   theme_remuichild
 * @copyright WisdmLabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('common.php');
require_once($CFG->dirroot . "/mod/book/lib.php");
require_once($CFG->dirroot . "/mod/book/locallib.php");
require_once($CFG->dirroot . "/theme/remuichild/classes/utility.php");
// require_once($CFG->libdir. '/coursecatlib.php');

// global $COURSE;
// $modinfo = get_fast_modinfo($COURSE);
// var_dump($modinfo);
// exit;

// prepare activity sidebar context
$isactivitypage = false;
if (isset($PAGE->cm->id) && $COURSE->id != 1) {
    $isactivitypage = true;
}
$templatecontext['isactivitypage'] = $isactivitypage;
$templatecontext['courseurl'] = course_get_url($COURSE->id);
$templatecontext['activitysections'] = \theme_remuichild\utility::get_activity_list();
// $temp = get_array_of_activities($COURSE->id);

// var_dump($temp);
// var_dump($templatecontext['activitysections']);
// exit;
// foreach ($temp as $key => $activitylist) {
//     // foreach($activitylist['activity_list'] as $activity) {
//         // var_dump($activitylist);
//         if($activitylist->mod == "book") {
//             $chapters = book_preload_chapters($activitylist);
//             var_dump($chapters);
//         }
//     // }
// }
// foreach ($temp as $actkey => $activity) {
//     foreach ($templatecontext['activitysections'] as $seckey => $sec) {
//     // var_dump($activity->mod);
//     // exit;
//         if(!empty($sec['activity_list'])) {
//             foreach ($sec['activity_list'] as $secactivitykey => $secactivity) {
//                 // var_dump($secactivity);
//                 if(isset($secactivity['title']) && strpos($secactivity['title'], 'book/view.php')) {
//                     $chapters = book_preload_chapters($activity);
//                     // var_dump($chapters);
//                     // $templatecontext['activitysections'][$seckey]['activity_list'][$secactivitykey]['chapters'] = book_preload_chapters($activity);
//                 }
//             }
//         }
//         // if($seckey == $activity->section) {
//         //     var_dump($activity);
//         //     $chapters = book_preload_chapters($activity);
//         // }
//     }
// }
// var_dump($templatecontext['activitysections'][4]);


// foreach ($templatecontext['activitysections'] as $seckey => $sec) {
//     var_dump($sec['activity_list']);
//     // exit;

// }
// exit;
$flatnavigation = flatnav_icon_support($PAGE->flatnav);
foreach ($flatnavigation as $navs) {
    if ($navs->key == 'addblock') {
        $templatecontext['addblock'] = $navs;
        break;
    }
}

// Activities Navigation Previous Next
if (!preg_match('#/course/view.php#', $PAGE->url)) {
    if ($COURSE->format != 'singleactivity') {
        if (isset($PAGE->cm->id)) {
            $activityid = $PAGE->cm->id;
        } else {
            $activityid = optional_param('id', -1, PARAM_INT);
        }
        if ($activityid != -1) {
            $templatecontext['prevnextnav'] = activities_navigation_previous_next($PAGE->pagelayout, $activityid, $COURSE);
        }
    }
}
// var_dump($templatecontext['prevnextnav']);
// exit;
// Activities Navigation Previous Next


echo $OUTPUT->render_from_template('theme_remui/incourse', $templatecontext);


