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
require_once($CFG->dirroot. '/course/renderer.php');
require_once($CFG->libdir. '/gradelib.php');
require_once($CFG->dirroot. '/grade/querylib.php');

global $DB, $USER;

// add_notes
$courses = get_courses();
unset($courses[1]);

$chelper = new \coursecat_helper();

foreach ($courses as $courseid => $course) {
    $coursecontext = context_course::instance($course->id);
    // Check whether user has capability to edit notes.
    $hascapability = has_capability('moodle/notes:manage', $coursecontext);
    if (! $hascapability) {
        unset($courses[$courseid]);
    }
}
if ($courses) {
    $templatecontext['has_courses'] = true;
    $templatecontext['courses'] = array_values($courses);
}
// end_add_notes

// enrolled_users_state & latest members
if (is_siteadmin()) {
    // enrolled_users_state
    require_once($CFG->libdir. '/coursecatlib.php');
    $categorylist = coursecat::make_categories_list();
    $inquery = implode(", ", array_keys($categorylist));
    $sqlq = 'SELECT DISTINCT category from {course} where category IN (' . $inquery . ')';
    $catres = $DB->get_records_sql($sqlq);
    if ($catres) {
        $templatecontext['hascategory'] = true;
        $count = 0;
        foreach ($catres as $key => $value) {
            $category[$count] = new stdClass;
            $category[$count]->key = $key;
            $category[$count]->categoryname = $categorylist[$key];
            $count++;
        }
        $templatecontext['category'] = $category;
    }
    // end_enrolled_users_state

    // latest members
    $templatecontext['latest_members'] = \theme_remui\utility::get_recent_user();
    $templatecontext['profile_url'] = new moodle_url('/user/profile.php?id');
    $templatecontext['user_profiles'] = new moodle_url('/admin/user.php');
    // end_latest members
}
// end_enrolled_users_state & latest members

// quiz_stats
$sqlq = ("SELECT DISTINCT q.course courseid, c.shortname shortname, c.fullname fullname FROM {quiz} q JOIN {course} c ON q.course = c.id");
$courses_for_quiz = $DB->get_records_sql($sqlq);
foreach ($courses_for_quiz as $course) {
    $context = context_course::instance($course->courseid);
    if (!has_capability('mod/quiz:preview', $context)) {
        unset($courses_for_quiz[$course->courseid]);
    }
}
if ($courses_for_quiz) {
    $templatecontext['has_courses_for_quiz'] = true;
    $templatecontext['courses_for_quiz'] = array_values($courses_for_quiz);
}
// end_quiz_stats

// for recent_active_forum & recent_assignments

$templatecontext['usercanmanage'] = \theme_remui\utility::check_user_admin_cap();

// recent_active_forum
$templatecontext['recentforums'] = \theme_remui\utility::recent_forum_activity(false, 5);
if (!empty($templatecontext['recentforums'])) {
    $templatecontext['hasrecentforums'] = 1;
} else {
    $templatecontext['hasrecentforums'] = 0;
}
// end_recent_active_forum

//analytics_overview
$courses = enrol_get_all_users_courses($USER->id);
$qcourse = [];
foreach ($courses as $course) {
    $course->fullname = strip_tags($chelper->get_course_formatted_name($course));
    $gradeActivities = grade_get_gradable_activities($course->id);
    if (!empty($gradeActivities)) {
        $qcourse[] = ['id' => $course->id, 'name' => $course->fullname];
    }
}
$templatecontext['quizcourse'] = $qcourse;

if (count($qcourse)) {
    $templatecontext['hasanalytics'] = 1;
} else {
    $templatecontext['hasanalytics'] = 0;
}

// end analytics overview


// recent_assignments
$recentassignments = \theme_remui\utility::grading();
if ($recentassignments) {
    $templatecontext['hasrecentassignments'] = true;
    $i = 0;
    foreach ($recentassignments as $ungraded) {
        $modinfo = get_fast_modinfo($ungraded->course);
        $course = $modinfo->get_course();
        $cm = $modinfo->get_cm($ungraded->coursemoduleid);

        $array[0] = new stdClass;
        $array[0]->cm_url = $cm->url;
        $array[0]->cm_name = $cm->name;
        $array[0]->course_fullname = strip_tags($chelper->get_course_formatted_name($course));

        if (++$i == 5) {
            break;
        }
    }
    $templatecontext['recentassignments'] = $array;
} else {
    $grades = \theme_remui\utility::graded();
    if (!empty($grades)) {
        $templatecontext['hasrecentfeedback'] = true;
        $i = 0;
        foreach ($grades as $grade) {
            $modinfo = get_fast_modinfo($grade->courseid);
            $course = $modinfo->get_course();

            $modtype = $grade->itemmodule;
            $cm = $modinfo->instances[$modtype][$grade->iteminstance];

            $coursecontext = \context_course::instance($grade->courseid);
            $canviewhiddengrade = has_capability('moodle/grade:viewhidden', $coursecontext);
            $url = new \moodle_url('/grade/report/user/index.php', ['id' => $grade->courseid]);
            if (in_array($modtype, ['quiz', 'assign']) && (!empty($grade->rawgrade) || !empty($grade->feedback))) {
                $url = $cm->url;
            }

            $gradetitle = "$course->fullname / $cm->name";
            $releasedon = isset($grade->timemodified) ? $grade->timemodified : $grade->timecreated;
            $grade = new \grade_grade(array('itemid' => $grade->itemid, 'userid' => $USER->id));
            if (!$grade->is_hidden() || $canviewhiddengrade) {
                $array[$i] = new stdClass;
                $array[$i]->courseurl = new moodle_url('/course/view.php?id=' . $grade->grade_item->courseid);
                $array[$i]->course_shortname = $course->shortname;
                $array[$i]->assignurl = $cm->url;
                $array[$i]->grade_itemname = $grade->grade_item->itemname;
                $array[$i]->grade_rawgrade = intval($grade->rawgrade);
                $array[$i]->grade_rawgrademax = intval($grade->rawgrademax);
                $array[$i]->timemodified = $grade->timemodified;
            }
        }
        $templatecontext['recentfeedback'] = $array;
    }
}
// end_recent_assignments

// Teacher View Dashboard
$mycourses = enrol_get_users_courses($USER->id);
$course_progress = array();
$course_count = 0;
$isTeacher = false;
foreach ($mycourses as $courseid => $course) {
    $coursecontext = context_course::instance($course->id);
    $roles = get_user_roles($coursecontext, $USER->id, true);
    foreach ($roles as $roleid => $role) {
        if ($role->roleid == 1 || $role->roleid == 2 || $role->roleid == 3 || $role->roleid == 4) {
            $isTeacher = true;
            $temp = \theme_remui\utility::get_course_progress($course->id);
            $temp->backColor = 'alternate-row';
            $temp->index = ++$course_count;
            $course_progress[] = $temp;
            break;
        }
    }
}
if ($isTeacher) {
    $templatecontext['isTeacher'] = $isTeacher;
}
    $templatecontext['course_progress'] = $course_progress;

echo $OUTPUT->render_from_template('theme_remui/mydashboard', $templatecontext);

$PAGE->requires->strings_for_js(array('selectastudent', 'total', 'nousersenrolledincourse', 'selectcoursetodisplayusers', 'enrolleduserstats', 'quizstats'), 'theme_remui');
