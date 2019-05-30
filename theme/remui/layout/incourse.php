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
 * @package   theme_remui
 * @copyright WisdmLabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once('common.php');

// prepare activity sidebar context
$isactivitypage = false;
if (isset($PAGE->cm->id) && $COURSE->id != 1) {
    $isactivitypage = true;
}
$templatecontext['isactivitypage'] = $isactivitypage;
$templatecontext['courseurl'] = course_get_url($COURSE->id);
$templatecontext['activitysections'] = \theme_remui\utility::get_activity_list();

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
// Activities Navigation Previous Next


echo $OUTPUT->render_from_template('theme_remui/incourse', $templatecontext);
