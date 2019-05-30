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


namespace theme_remui;

use user_picture;
use moodle_url;
use blog_listing;
use context_system;
use course_in_list;
use context_course;
use core_completion\progress;
use stdClass;
use html_writer;

include_once($CFG->dirroot.'/mod/forum/lib.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->libdir. '/coursecatlib.php');
require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/message/lib.php");
require_once($CFG->libdir. '/gradelib.php');
require_once($CFG->dirroot. '/grade/querylib.php');
require_once($CFG->dirroot.'/message/lib.php');


/**
 * General remui utility functions.
 *
 * Added to a class for the convenience of auto loading.
 *
 * @package   theme_remui
 * @copyright WisdmLabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utility
{
    public static $childCategories = array();

    // get user profile pic link
    public static function get_user_picture($userobject = null, $imgsize = 100)
    {
        global $USER, $PAGE;
        if (!$userobject) {
            $userobject = $USER;
        }

        $userimg = new user_picture($userobject);
        $userimg->size = $imgsize;
        return  $userimg->get_url($PAGE);
    }

    // get user forum posts count
    public static function get_user_forum_post_count($userobject = null)
    {
        global $USER;
        if (!$userobject) {
            $userobject = $USER;
        }

        $courses = forum_get_courses_user_posted_in($userobject);
        $userpostcount = forum_get_posts_by_user($userobject, $courses)->totalcount;
        $userpostlink = new moodle_url('/mod/forum/user.php?id=' . $userobject->id);

        return $userpostcount;
    }

    // get user blog count
    public static function get_user_blog_post_count($userobject = null)
    {
        global $USER, $DB, $CFG;
        if (!$userobject) {
            $userobject = $USER;
        }

        if (!empty($CFG->enableblogs)) {
            include_once($CFG->dirroot .'/blog/locallib.php');
        } else {
            return;
        }

        $blogobj = new blog_listing();
        if ($sqlarray = $blogobj->get_entry_fetch_sql(false, 'created DESC')) {
            $sqlarray['sql'] = "SELECT p.*, u.firstnamephonetic,u.lastnamephonetic,u.middlename,u.alternatename,
            u.firstname,u.lastname, u.email FROM {post} p, {user} u WHERE u.deleted = 0 AND p.userid = u.id AND
            (p.module = 'blog' OR p.module = 'blog_external') AND (p.userid = ?  OR p.publishstate = 'site' )
            AND u.id = ? ORDER BY created DESC";
            $sqlarray['params'] = array($USER->id, $userobject->id);
            $blogobj->entries = $DB->get_records_sql($sqlarray['sql'], $sqlarray['params']);
            $userblogcount = count($blogobj->entries);
        }

        return $userblogcount;
    }

    // get user blog count
    public static function get_user_contacts_count($userobject = null)
    {
        global $USER, $DB, $CFG;
        if (!$userobject) {
            $userobject = $USER;
        }

        $userblogcount = count($DB->get_records('message_contacts', array('userid'=>$userobject->id)));

        return $userblogcount;
    }

    // is user admin or manager
    public static function check_user_admin_cap($userobject = null)
    {
        global $USER;
        $has_capability = false;

        if (!$userobject) {
            $userobject = $USER;
        }
        if (is_siteadmin()) {
            $has_capability = true;
        }
        $context = context_system::instance();
        $roles = get_user_roles($context, $userobject->id, false);
        if (!$has_capability) {
            foreach ($roles as $role) {
                if ($role->roleid == 1 && $role->shortname == 'manager') {
                    $has_capability = true;
                    break;
                }
            }
        }

        return $has_capability;
    }

    /**
     * Set all the child categories of parent category
     * @param $category parent category id
     */
    public static function getChildrenCategories($category)
    {
        global $DB;
        $childCategories = $DB->get_records_sql('SELECT * FROM {course_categories} WHERE parent = ?', array($category));
        if (!empty($childCategories)) {
            foreach ($childCategories as $child) {
                array_push(self::$childCategories, $child->id);
                self::getChildrenCategories($child->id);
            }
        } else {
            return $category;
        }
    }

    /**
     * Return user's courses or all the courses
     *
     * Usually called to get usr's courese, or it could also be called to get all course.
     * This function will also be called whern search course is used.
     *
     * @param string $search course name to be search
     * @param int $category ids to be search of courses.
     * @param int $usercourses to return user's course which he/she enrolled into.
     * @param int $limitfrom course to be returned from these number onwards, like from course 5 .
     * @param int $limitto till this number course to be returned , like from course 10, then 5 course will be returned from 5 to 10.
     * @param int $showhidden include hidden courses in results.
     * @return array of course.
     */
    public static function get_courses(
        $totalcount = false,
        $search = null,
        $category = null,
        $limitfrom = 0,
        $limitto = 0,
        $mycourses = null
    ) {
        global $DB, $CFG, $USER, $OUTPUT;
        $count = 0;
        $coursesarray = array();
        $where = '';
        $sql_params = array(1);
        require_once($CFG->libdir. '/coursecatlib.php');
        require_once($CFG->dirroot.'/course/renderer.php');

        if (!empty($search)) {
            $where .= " AND fullname like ? OR shortname like ?";
            $sql_params[] = "%$search%";
            $sql_params[] = "%$search%";
        }

        if (!empty($category)) {
            self::getChildrenCategories($category);
            array_push(self::$childCategories, $category);
            if (!empty(self::$childCategories)) {
                $str = implode(", ", self::$childCategories);
                $where .= " AND category IN ($str)";
            } else {
                $where .= " AND category ='$category' ";
            }
        }

        if ($mycourses) {
            $coursesenrol = enrol_get_users_courses($USER->id);
            $enrolcoursearary = array();
            foreach ($coursesenrol as $enrol) {
                array_push($enrolcoursearary, $enrol->id);
            }
            $str = implode(", ", $enrolcoursearary);
            if ($str) {
                $where .= " AND c.id IN ($str) ";
            } else {
                $where .= " AND c.id IN (NULL) ";
            }
        }

        // get courses
        $fields = array('c.id',
                    'c.category',
                    'c.fullname',
                    'c.startdate',
                    'c.enddate',
                    'c.visible',
                    );
        // return count of total courses by getting limited data
        // if required
        if ($totalcount) {
            if (!self::check_user_admin_cap($USER)) {
                $where .= " AND visible = 1";
            }

            return count($DB->get_records_sql("SELECT c.id FROM {course} c where id != ? $where ORDER BY sortorder", $sql_params));
        } else {
            $courses = $DB->get_records_sql("SELECT ".implode($fields, ',')." FROM {course} c where id != ? $where ORDER BY sortorder", $sql_params, $limitfrom, $limitto);
        }

        // prepare courses array
        $chelper = new \coursecat_helper();
        foreach ($courses as $k => $course) {
            $course_in_list = new course_in_list($course);
            $context = context_course::instance($course->id);

            // for hidden courses, require visibility check
            if (isset($course->visible) && $course->visible <= 0) {
                if (!has_capability('moodle/course:viewhiddencourses', $context)) {
                    continue;
                }
            }

            if ($course->category == 0) {
                continue;
            }

            $coursesarray[$count]["courseid"] = $course->id;
            $coursesarray[$count]["coursename"] = strip_tags($chelper->get_course_formatted_name($course));
            $coursesarray[$count]["categoryname"] = $DB->get_record('course_categories', array('id'=>$course->category))->name;
            $coursesarray[$count]["visible"] = $course->visible;
            $coursesarray[$count]["courseurl"] = $CFG->wwwroot."/course/view.php?id=".$course->id;

            // This is to handle the version change
            // User enrollment link has changed for moodle version 3.4
            $version33 = "2017092100";
            $cur_version = $DB->get_record_sql('SELECT * FROM {config_plugins} WHERE plugin = ? AND name = ?', array('theme_remui', 'version'));
            $user_enrol_link = "/enrol/users.php?id=";
            if ($cur_version > $version33) {
                $user_enrol_link = "/user/index.php?id=";
            }
            $coursesarray[$count]["enrollusers"] = $CFG->wwwroot.$user_enrol_link.$course->id."&version=".$course->id;
            $coursesarray[$count]["editcourse"] = $CFG->wwwroot."/course/edit.php?id=".$course->id;
            $coursesarray[$count]["grader"] = $CFG->wwwroot."/grade/report/grader/index.php?id=".$course->id;
            $coursesarray[$count]["activity"] = $CFG->wwwroot."/report/outline/index.php?id=".$course->id;
            $coursesummary = strip_tags($chelper->get_course_formatted_summary(
                $course_in_list,
                array('overflowdiv' => false, 'noclean' => false, 'para' => false)
            ));
            $summarystring = strlen($coursesummary) > 100 ? substr($coursesummary, 0, 100)."..." : $coursesummary;
            $coursesarray[$count]["coursesummary"] = $summarystring;
            $coursesarray[$count]["coursestartdate"] = date('d M, Y', $course->startdate);

            // course enrollment icons
            if ($icons = enrol_get_course_info_icons($course)) {
                $iconhtml = '';
                foreach ($icons as $pix_icon) {
                    $iconhtml .= $OUTPUT->render($pix_icon);
                }
                $coursesarray[$count]["enrollmenticons"] = $iconhtml; // add icons in context
            }

            // course instructors
            $instructors = $course_in_list->get_course_contacts();
            foreach ($instructors as $key => $instructor) {
                $coursesarray[$count]["instructors"][] = array(
                                                        'name' => $instructor['username'],
                                                        'url'  => $CFG->wwwroot.'/user/profile.php?id='.$key,
                                                        'picture' => self::get_user_picture($DB->get_record('user', array('id' => $key)))
                                                        );
                break;
            }

            // course image
            foreach ($course_in_list->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $courseimage = file_encode_url(
                    "$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(),
                    !$isimage
                );
                if ($isimage) {
                    break;
                }
            }
            if (!empty($courseimage)) {
                $coursesarray[$count]["courseimage"] = $courseimage;
            } else {
                $coursesarray[$count]["courseimage"] = $OUTPUT->image_url('placeholder', 'theme');
            }
            $courseimage = '';

            //course completion info
            if (is_enrolled($context, $USER->id)) {
                $completion = new \completion_info($course);
                if ($completion->is_enabled()) {
                    $percentage = progress::get_course_progress_percentage($course, $USER->id);

                    if (!is_null($percentage)) {
                        $percentage = floor($percentage);
                        if ($percentage == 100) {
                            $coursesarray[$count]["coursecompleted"] = get_string('completed', 'theme_remui');
                        } elseif ($percentage > 0 && $percentage < 100) {
                            $coursesarray[$count]["courseinprogress"] = get_string('resume', 'theme_remui');
                            $coursesarray[$count]["percentage"]  = $percentage;
                            $modules = $completion->get_activities();
                            foreach ($modules as $module) {
                                $data = $completion->get_data($module, false, $USER->id);
                                if (!$data->completionstate) {
                                    // $coursesarray[$count]["lastaccessactivity"] = $CFG->wwwroot."/mod/".$module->modname."/view.php?id=".$module->id;
                                    $coursesarray[$count]["lastaccessactivity"] = $CFG->wwwroot."/course/view.php?id=".$course->id
                                    ."#section-".$module->sectionnum;
                                    break;
                                }
                            }
                        } else {
                            $coursesarray[$count]["coursetostart"] = get_string('start', 'theme_remui');
                        }
                    } else {
                        $coursesarray[$count]["coursetostart"] = get_string('start', 'theme_remui');
                    }
                }
            }
            $count++;
        }

        return $coursesarray;
    }

    // used in corusecategory layout
    // render category selector
    public static function get_course_category_selector($category = '', $categorysort = '', $search = '', $mycourses = '', $pageurl)
    {
        $default   = '';
        $site_asc  = '';
        $site_desc = '';

        if ($categorysort == 'SORT_ASC') {
            $site_asc  = 'selected';
        } elseif ($categorysort == 'SORT_DESC') {
            $site_desc = 'selected';
        } else {
            $default = 'selected';
        }

        $categories = \coursecat::make_categories_list();
        $categoryhtml = "<form method='get' action='{$pageurl}'>";

        if ($mycourses != '') {
            $categoryhtml .= "<input type='hidden' name='mycourses' value='{$mycourses}'>";
        }

        if ($search != '') {
            $categoryhtml .= "<input type='hidden' name='search' value='{$search}'>";
        }
        $categoryhtml .= "<div class='row'>";
        $categoryhtml .= "<div  class='form-group col-md-6'>";
        $categoryhtml .= "<label for='categoryselect' class='d-none'>".get_string('category', 'theme_remui')."</label> <select onchange='this.form.submit()' class='custom-select' id='categoryselect' name='categoryid' id='category'>
                <option value=''>".get_string('allcategories', 'theme_remui')."</option>";

        foreach ($categories as $key => $coursecategory) {
            if ($category == $key) {
                $categoryhtml .= "<option selected value='{$key}'>{$coursecategory}</option>";
            } else {
                $categoryhtml .= "<option value='{$key}'>{$coursecategory}</option>";
            }
        }
        $categoryhtml .= "</select>";
        $categoryhtml .= "</div>";
        $categoryhtml .= "<div  class='form-group col-md-6'>";
        $categoryhtml .= "<select onchange='this.form.submit()' id='categoryselect' class='h-40 custom-select' name='categorysort' id='categorysort'>";
        $categoryhtml .= "<option ".$default." value='default'>".get_string('sortdefault', 'theme_remui')."</option>";
        $categoryhtml .= "<option ".$site_asc." value='SORT_ASC'>".get_string('sortascending', 'theme_remui')."</option>";
        $categoryhtml .= "<option ".$site_desc." value='SORT_DESC'>".get_string('sortdescending', 'theme_remui')."</option>";
        $categoryhtml .= "</select></div></div></form>";

        return $categoryhtml;
    }

    // get category description by catgory id
    public static function get_category_description($category)
    {
        global $CFG;
        if (!empty($category)) {
            require_once($CFG->dirroot.'/course/renderer.php');
            $chelper = new \coursecat_helper();
            $coursecat = \coursecat::get($category);
            if ($description = $chelper->get_category_formatted_description($coursecat)) {
                return $description;
            }
        }

        return '';
    }

    // get user courses along with their course progress
    public static function get_users_courses_with_progress($userobject)
    {
        global $USER, $OUTPUT, $CFG;

        if (!$userobject) {
            $userobject = $USER;
        }

        require_once($CFG->dirroot.'/course/renderer.php');
        $chelper = new \coursecat_helper();

        $courses = enrol_get_users_courses($userobject->id, true, '*', 'visible DESC, fullname ASC, sortorder ASC');
        foreach ($courses as $course) {
            $course->fullname = strip_tags($chelper->get_course_formatted_name($course));
            // get course list instance
            if ($course instanceof stdClass) {
                require_once($CFG->libdir. '/coursecatlib.php');
                $courseobj = new \course_in_list($course);
            }

            $completion = new \completion_info($course);

            // First, let's make sure completion is enabled.
            if ($completion->is_enabled()) {
                $percentage = progress::get_course_progress_percentage($course, $userobject->id);

                if (!is_null($percentage)) {
                    $percentage = floor($percentage);
                }

                // add completion data in course object
                $course->completed = $completion->is_course_complete($userobject->id);
                $course->progress  = $percentage;
            }

            // update properties in object
            // if( $course->startdate ) {
            //     $course->startdate = date('d M, Y', $course->startdate);
            // }
            $course->link = $CFG->wwwroot."/course/view.php?id=".$course->id;

            // summary
            $course->summary = strip_tags($chelper->get_course_formatted_summary(
                $courseobj,
                array('overflowdiv' => false, 'noclean' => false, 'para' => false)
            ));

            // update course image in object
            foreach ($courseobj->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $courseimage = file_encode_url(
                    "$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(),
                    !$isimage
                );
                if ($isimage) {
                    break;
                }
            }
            if (empty($courseimage)) {
                $courseimage = $OUTPUT->image_url('placeholder', 'theme');
            }

            $course->courseimage = $courseimage;
            $courseimage = '';
        }
        return $courses;
    }

    /**
     * Returns list of courses of passed course category id.
     *
     * @param int $categoryid
     * @return array
     */
    public static function get_courses_by_category($categoryid)
    {
        global $DB;
        $query = "SELECT id, fullname, shortname from {course} where category = " . $categoryid;
        $courselist = $DB->get_records_sql($query);
        if ($courselist) {
            foreach ($courselist as $course) {
                $context = context_course::instance($course->id);
                $query = "select count(u.id) as count from  {role_assignments} as a, {user} as u where contextid=" . $context->id . " and roleid=5 and a.userid=u.id;";
                $count = $DB->get_records_sql($query);
                $count = key($count);
                $courselist[$course->id]->count = $count;
            }
            usort($courselist, function ($variable1, $variable2) {
                return $variable2->count - $variable1->count;
            });
            $labels = $data = $background_color = $hoverBackground_color = array();
            $colors = array('#2196f3', '#00bcd4', '#009688', '#4caf50', '#8bc34a', '#ffeb3b', '#ff9800', '#f44336', '#9c27b0', '#673ab7', '#3f51b5');
            $others = $othersCount = 0;
            foreach ($courselist as $index => $course) {
                if ($index > 9) {
                    $others = 1;
                    $othersCount += $course->count;
                } else {
                    array_push($labels, $course->shortname);
                    array_push($data, $course->count);
                    array_push($background_color, $colors[$index]);
                    array_push($hoverBackground_color, $colors[$index]);
                }
            }
            if ($others > 0) {
                array_push($labels, get_string('others', 'theme_remui'));
                array_push($data, $othersCount);
                array_push($background_color, $colors[10]);
                array_push($hoverBackground_color, $colors[10]);
            }
            return array('labels' => $labels, 'data' => $data, 'background_color' => $background_color, 'hoverBackground_color' => $hoverBackground_color);
        } else {
            return null;
        }
    }

    // get user profile pic link
    public static function get_user_image_link($userid, $imgsize)
    {
        global $USER;
        if (!$userid) {
            $userid = $USER->id;
        }
        global $DB, $PAGE;
        $user = $DB->get_record('user', array('id' => $userid));
        $userimg = new user_picture($user);
        $userimg->size = $imgsize;
        return  $userimg->get_url($PAGE);
    }

    // Get the recently added users
    public static function get_recent_user()
    {
        global  $DB;
        $userdata = array();
        $limitfrom = 0;
        $limitto = 8;
        $users = $DB->get_records_sql('SELECT u.* FROM {user} u  WHERE u.deleted = 0 AND id != 1 ORDER BY timecreated desc', array(1), $limitfrom, $limitto);
        $count = 0;
        foreach ($users as $value) {
            $date = date('d/m/Y', $value->timecreated);
            if ($date == date('d/m/Y')) {
                $date = get_string('today', 'theme_remui');
            } elseif ($date == date('d/m/Y', time() - (24 * 60 * 60))) {
                $date = get_string('yesterday', 'theme_remui');
            } else {
                $date = date('jS F Y', $value->timecreated);
            }
            $userdata[$count]['img'] = self::get_user_image_link($value->id, 100);
            $userdata[$count]['name'] = $value->firstname .' '.$value->lastname;
            $userdata[$count]['register_date'] = $date;
            $userdata[$count]['id'] = $value->id;
            $count++;
        }
        return $userdata;
    }

    // for quiz_stats block on dashboard
    public static function get_quiz_participation_data($courseid, $limit = 8)
    {
        global $DB;
        $sqlq = ("SELECT COUNT(DISTINCT u.id)
            FROM {course} c
            JOIN {context} ct ON c.id = ct.instanceid
            JOIN {role_assignments} ra ON ra.contextid = ct.id
            JOIN {user} u ON u.id = ra.userid
            JOIN {role} r ON r.id = ra.roleid
            WHERE c.id = ?");
        $totalcount = $DB->get_records_sql($sqlq, array($courseid));
        $totalcount = key($totalcount);
        $sqlq = ("SELECT SUBSTRING(q.name, 1, 20) labels , COUNT(DISTINCT qa.userid) attempts
            FROM {quiz} q
            LEFT JOIN {quiz_attempts} qa ON q.id = qa.quiz
            WHERE q.course = ?
            GROUP BY q.name
            ORDER BY attempts DESC
            LIMIT $limit");
        $quizdata = $DB->get_records_sql($sqlq, array($courseid));
        $chartdata = array();
        $index = 0;
        $chartdata['datasets'][0]['label'] = get_string('totalusersattemptedquiz', 'theme_remui');
        $chartdata['datasets'][1]['label'] = get_string('totalusersnotattemptedquiz', 'theme_remui');
        $chartdata['datasets'][0]['backgroundColor'] = "rgba(75, 192, 192, 0.2)";
        $chartdata['datasets'][1]['backgroundColor'] = "rgba(255, 99, 132, 0.2)";
        $chartdata['datasets'][0]['borderColor'] = "rgba(75, 192, 192, 1)";
        $chartdata['datasets'][1]['borderColor'] = "rgba(255,99,132,1)";
        $chartdata['datasets'][0]['borderWidth'] = 1;
        $chartdata['datasets'][1]['borderWidth'] = 1;
        foreach ($quizdata as $key => $quiz) {
            $chartdata['labels'][$index] = $key;
            $chartdata['datasets'][0]['data'][$index] = intval($quiz->attempts);
            $chartdata['datasets'][1]['data'][$index] = intval($totalcount - $quiz->attempts);
            if ($chartdata['datasets'][1]['data'][$index] < 0) {
                $chartdata['datasets'][1]['data'][$index] = 0;
            }
            // $quizdata[$key]->noattempts = $totalcount - $quiz->attempts;
            $index++;
        }
        return $chartdata;
    }

    /*
     * get course summary image
     */
    public static function get_course_image($course_in_list, $islist = false)
    {
        global $CFG, $OUTPUT;
        if (!$islist) {
            $course_in_list = new course_in_list($course_in_list);
        }

        // course image
        foreach ($course_in_list->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $courseimage = file_encode_url(
                "$CFG->wwwroot/pluginfile.php",
                '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                $file->get_filearea(). $file->get_filepath(). $file->get_filename(),
                !$isimage
            );
            if ($isimage) {
                break;
            }
        }
        if (!empty($courseimage)) {
            return $courseimage;
        } else {
            return $OUTPUT->image_url('placeholder', 'theme');
        }
    }

    /**
     * Update the User Profile details using ajax call.
     *
     * @param $fname, $lname, $emailid, $description, $city, $country
     * @return boolean, weather result are updated or not.
     */
    public static function save_user_profile_info($fname, $lname, /* $emailid, */ $description, $city, $country)
    {
        global $USER, $DB;
        $user = $DB->get_record('user', array('id' => $USER->id));
        $user->firstname = $fname;
        $user->lastname = $lname;
        //$user->email = urldecode($emailid);
        $user->description = $description;
        $user->city = $city;
        $user->country = $country;
        $result = $DB->update_record('user', $user);

        // Update Global Variable
        $USER->firstname = $fname;
        $USER->lastname = $lname;
        $USER->city = $city;
        $USER->country = $country;

        return $result;
    }

    /**
     * Return the recent blog.
     *
     * This function helps in retrieving the recent blog.
     *
     * @param int $start how many blog should be skipped if specified 0 no recent blog will be skipped.
     * @param int $blogcount number of blog to be return.
     * @param string $filearea file area
     * @return array $blog returns array of blog data.
     */

    public static function get_recent_blogs($start = 0, $blogcount = 10)
    {
        global $CFG;

        require_once($CFG->dirroot.'/blog/locallib.php');
        $bloglisting = new \blog_listing();

        $blogentries = $bloglisting->get_entries($start, $blogcount);

        foreach ($blogentries as $blogentry) {
            $blogsummary = strip_tags($blogentry->summary);
            $summarystring = strlen($blogsummary) > 150 ? substr($blogsummary, 0, 150)."..." : $blogsummary;
            $blogentry->summary = $summarystring;

            // created at
            $blogentry->createdat = date('d M, Y', $blogentry->created);

            // link
            $blogentry->link = $CFG->wwwroot.'/blog/index.php?entryid='.$blogentry->id;
        }
        return $blogentries;
    }

    /**
     * This function is used to get the data for either slider or static at a time.
     *
     * @return array of sliding data
     */
    public static function get_slider_data()
    {
        global $PAGE, $OUTPUT;

        $sliderdata = array();
        $sliderdata['isslider'] = false;
        $sliderdata['isimage']  = false;
        $sliderdata['isvideo']  = false;
        $sliderdata['slideinterval'] = false;

        if (\theme_remui\toolbox::get_setting('sliderautoplay') == '1') {
            $sliderdata['slideinterval'] =  \theme_remui\toolbox::get_setting('slideinterval');
        }

        $numberofslides =  \theme_remui\toolbox::get_setting('slidercount');

        // Get the content details either static or slider.
        $frontpagecontenttype =  \theme_remui\toolbox::get_setting('frontpageimagecontent');

        if ($frontpagecontenttype) { // Dynamic image slider.
            $sliderdata['isslider'] = true;
            if ($numberofslides >= 1) {
                for ($count = 1; $count <= $numberofslides; $count++) {
                    $sliderimageurl = \theme_remui\toolbox::setting_file_url('slideimage'.$count, 'slideimage'.$count);
                    if ($sliderimageurl == "" || $sliderimageurl == null) {
                        $sliderimageurl = \theme_remui\toolbox::image_url('slide', 'theme');
                    }
                    $sliderimagetext =  \theme_remui\toolbox::get_setting('slidertext'.$count);
                    $sliderimagelink =  \theme_remui\toolbox::get_setting('sliderurl'.$count);
                    $sliderbuttontext =  \theme_remui\toolbox::get_setting('sliderbuttontext'.$count);
                    if ($count == 1) {
                        $active = true;
                    } else {
                        $active = false;
                    }
                    $sliderdata['slides'][] = array(
                    'img' => $sliderimageurl,
                    'img_txt' => $sliderimagetext,
                    'btn_link' => $sliderimagelink,
                    'btn_txt' => $sliderbuttontext,
                    'active' => $active,
                    'count' => $count - 1);
                }
            }
        } elseif (!$frontpagecontenttype) { // Static data.
            // Get the static front page settings
            $sliderdata['addtxt'] =  \theme_remui\toolbox::get_setting('addtext');
            $contenttype =  \theme_remui\toolbox::get_setting('contenttype');
            if (!$contenttype) {
                $sliderdata['isvideo'] = true;
                $sliderdata['video'] =  \theme_remui\toolbox::get_setting('video');
                $sliderdata['videoalignment'] =  \theme_remui\toolbox::get_setting('frontpagevideoalignment');
            } elseif ($contenttype) {
                $sliderdata['isimage'] = true;
                $staticimage = \theme_remui\toolbox::setting_file_url('staticimage', 'staticimage');
                if ($staticimage == "" || $staticimage == null) {
                    $sliderdata['staticimage'] = \theme_remui\toolbox::image_url('slide', 'theme');
                } else {
                    $sliderdata['staticimage'] = $staticimage;
                }
            }
        }
        return $sliderdata;
    }

    /**
     * This function is used to get the data for testimonials in about us section.
     *
     * @return array of testimonial data
     */
    public static function get_testimonial_data()
    {
        global $PAGE, $OUTPUT;

        // return if acout us is disabled
        if (!\theme_remui\toolbox::get_setting('enablefrontpageaboutus')) {
            return false;
        }

        $testimonial_data = array(
                'both' => false,
                'about' => false,
                'test' => false
            );
        $testimonialcount =  \theme_remui\toolbox::get_setting('testimonialcount');

        if ($testimonialcount >= 1) {
            $testimonial_data['test'] = true;

            for ($count = 1; $count <= $testimonialcount; $count++) {
                $testimonialimageurl = \theme_remui\toolbox::setting_file_url('testimonialimage'.$count, 'testimonialimage'.$count);

                $testimonialname =  \theme_remui\toolbox::get_setting('testimonialname'.$count);
                $testimonialdesignation =  \theme_remui\toolbox::get_setting('testimonialdesignation'.$count);
                $testimonialtext =  \theme_remui\toolbox::get_setting('testimonialtext'.$count);
                if ($count == 1) {
                    $active = true;
                } else {
                    $active = false;
                }
                $testimonial_data['testimonials'][] = array(
                'image' => @$testimonialimageurl,
                'name' => $testimonialname,
                'designation' => $testimonialdesignation,
                'text' => $testimonialtext,
                'active' => $active,
                'count' => $count - 1);
            }
        }

        // about us data
        $testimonial_data['aboutus_heading'] = \theme_remui\toolbox::get_setting('frontpageaboutusheading');
        $testimonial_data['aboutus_desc'] = \theme_remui\toolbox::get_setting('frontpageaboutustext');

        if (!empty($testimonial_data['aboutus_heading']) || !empty($testimonial_data['aboutus_desc'])) {
            $testimonial_data['about'] = true;
        }
        if ($testimonial_data['test'] && $testimonial_data['about']) {
            $testimonial_data['both'] = true;
        }

        return $testimonial_data;
    }

    /**
     * This function is used to get the data for footer section.
     *
     * @return array of footer sections data
     */
    public static function get_footer_data($social = false)
    {
        $footer = array();
        $colcount = 0;
        for ($i=0; $i < 4; $i++) {
            if ($i == 0) {
                $footer['social'] = array(
                    'facebook' => \theme_remui\toolbox::get_setting('facebooksetting'),
                    'twitter'  => \theme_remui\toolbox::get_setting('twittersetting'),
                    'linkedin' => \theme_remui\toolbox::get_setting('linkedinsetting'),
                    'gplus'    => \theme_remui\toolbox::get_setting('gplussetting'),
                    'youtube'  => \theme_remui\toolbox::get_setting('youtubesetting'),
                    'instagram'=> \theme_remui\toolbox::get_setting('instagramsetting'),
                    'pinterest'=> \theme_remui\toolbox::get_setting('pinterestsetting')
                );
                $footer['social'] = array_filter($footer['social']); // remove empty elements
                if (!empty($footer['social'])) {
                    $colcount++;
                }
            } else {
                // skip footer content if only social
                if ($social) {
                    continue;
                }

                $title = \theme_remui\toolbox::get_setting('footercolumn'.$i.'title');
                $content = \theme_remui\toolbox::get_setting('footercolumn'.$i.'customhtml');
                if (!empty($title) || !empty($content)) {
                    $footer['sections'][] = array(
                        'title' => $title,
                        'content' => $content
                    );
                    $colcount++;
                }
            }
        }

        // skip footer content if only social
        if (!$social) {
            $footer['bottomtext'] = \theme_remui\toolbox::get_setting('footerbottomtext');
            $footer['bottomlink'] = \theme_remui\toolbox::get_setting('footerbottomlink');
            $footer['poweredby']  = \theme_remui\toolbox::get_setting('poweredbyedwiser');
            // to handle number of columns in footer row
            //$colcount = count($footer['social']) + count($footer['sections']);
            $classes = 'col-12 ';
            if ($colcount == 4) {
                $classes .= "col-sm-6 col-lg-3";
            } elseif ($colcount == 3) {
                $classes .= "col-sm-6 col-lg-4";
            } elseif ($colcount == 2) {
                $classes .= "col-sm-6";
            }

            $footer['classes'] = $classes;
        }
        //print_r($footer);
        return $footer;
    }

    /**
     * This function is used to get upcoming events.
     *
     * @return array of upcoming events
     */
    public static function get_events()
    {
        global $CFG,$PAGE;

        require_once($CFG->dirroot.'/calendar/lib.php');
        $courseid = $PAGE->course->id;
        $categoryid = ($PAGE->context->contextlevel === CONTEXT_COURSECAT) ? $PAGE->category->id : null;
        $calendar = \calendar_information::create(time(), $courseid, $categoryid);

        list($data, $template) = calendar_get_view($calendar, 'upcoming_mini');
        return $data->events;
    }

    /**
     * The messagearea messages parameters.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function data_for_messagearea_messages_parameters()
    {
        return new external_function_parameters(
            array(
                'currentuserid' => new external_value(PARAM_INT, 'The current user\'s id'),
                'otheruserid' => new external_value(PARAM_INT, 'The other user\'s id'),
                'limitfrom' => new external_value(PARAM_INT, 'Limit from', VALUE_DEFAULT, 0),
                'limitnum' => new external_value(PARAM_INT, 'Limit number', VALUE_DEFAULT, 0),
                'newest' => new external_value(PARAM_BOOL, 'Newest first?', VALUE_DEFAULT, false),
                'timefrom' => new external_value(
                    PARAM_INT,
                    'The timestamp from which the messages were created',
                    VALUE_DEFAULT,
                    0
                ),
            )
        );
    }

    /**
     * Get messagearea messages.
     *
     * @param int $currentuserid The current user's id
     * @param int $otheruserid The other user's id
     * @param int $limitfrom
     * @param int $limitnum
     * @param boolean $newest
     * @return stdClass
     * @throws moodle_exception
     * @since 3.2
     */
    public static function data_for_messagearea_messages($currentuserid, $otheruserid, $limitfrom = 0, $limitnum = 0, $newest = false, $timefrom = 0)
    {
        global $CFG, $PAGE, $USER;

        // Check if messaging is enabled.
        if (empty($CFG->messaging)) {
            throw new moodle_exception('disabled', 'message');
        }

        $systemcontext = context_system::instance();

        $params = array(
            'currentuserid' => $currentuserid,
            'otheruserid' => $otheruserid,
            'limitfrom' => $limitfrom,
            'limitnum' => $limitnum,
            'newest' => $newest,
            'timefrom' => $timefrom,
        );

        // REQUIRED, but commented, because not working
        // \lib\externallib\external_api::validate_parameters(self::data_for_messagearea_messages_parameters(), $params);
        // \lib\externallib\external_api::validate_context($systemcontext);

        if (($USER->id != $currentuserid) && !has_capability('moodle/site:readallmessages', $systemcontext)) {
            throw new moodle_exception(get_string('you_do_not_have_permission_to_perform_this_action', 'theme_remui'));
        }

        if ($newest) {
            $sort = 'timecreated DESC';
        } else {
            $sort = 'timecreated ASC';
        }

        // We need to enforce a one second delay on messages to avoid race conditions of current
        // messages still being sent.
        //
        // There is a chance that we could request messages before the current time's
        // second has elapsed and while other messages are being sent in that same second. In which
        // case those messages will be lost.
        //
        // Instead we ignore the current time in the result set to ensure that second is allowed to finish.
        if (!empty($timefrom)) {
            $timeto = time() - 1;
        } else {
            $timeto = 0;
        }

        // No requesting messages from the current time, as stated above.
        if ($timefrom == time()) {
            $messages = [];
        } else {
            $messages = \core_message\api::get_messages(
                $currentuserid,
                $otheruserid,
                $limitfrom,
                $limitnum,
                $sort,
                $timefrom,
                $timeto
            );
        }

        $messages = new \core_message\output\messagearea\messages($currentuserid, $otheruserid, $messages);

        $renderer = $PAGE->get_renderer('core_message');
        return $messages->export_for_template($renderer);
    }

    // get activity navigation
    public static function get_activity_list()
    {
        global $COURSE, $PAGE;

        // return if no cm id
        if (!isset($PAGE->cm->id)) {
            return;
        }

        $modinfo = get_fast_modinfo($COURSE);
        $sections_data = $modinfo->sections;
        $excluded_mods = array('label');
        $count = 0; // to print section count in sidebar
        $courserenderer = $PAGE->get_renderer('core', 'course');
        $sections = array();

        foreach ($modinfo->get_section_info_all() as $mod => $value) {
            // return if sections does not have activities or section is hidden to current user
            if (!array_key_exists($mod, $modinfo->sections) || !$value->uservisible) {
                continue;
            }
            $section_name = get_section_name($COURSE, $value);

            // check if current section is being viewed
            $open_section = '';
            if (in_array($PAGE->cm->id, $sections_data[$mod])) {
                $open_section = 'open active';
            }

            $sections[$count]['name'] = $section_name;
            $sections[$count]['open'] = $open_section;
            $sections[$count]['count'] = $count;

            // activities
            foreach ($sections_data[$mod] as $activity_id) {
                $activity = $modinfo->get_cm($activity_id);
                $classes = '';
                $completioninfo = new \completion_info($COURSE);
                $activity_completion = $courserenderer->course_section_cm_completion($COURSE, $completioninfo, $activity, array());

                if (!in_array($activity->modname, $excluded_mods)) {
                    // check if current activity
                    $active = ' ';
                    if ($PAGE->cm->id == $activity_id) {
                        $active = 'active ';
                    }

                    $completion = $completioninfo->is_enabled($activity);
                    if ($completion == COMPLETION_TRACKING_NONE) {
                        $classes = '';
                    } else {
                        $completiondata = $completioninfo->get_data($activity, true);
                        switch ($completiondata->completionstate) {
                            case COMPLETION_INCOMPLETE:
                                $classes = 'incomplete';
                                break;
                            case COMPLETION_COMPLETE:
                                $classes = 'complete';
                                break;
                            case COMPLETION_COMPLETE_PASS:
                                $classes = 'complete';
                                break;
                            case COMPLETION_COMPLETE_FAIL:
                                $classes = 'fail';
                                break;
                        }
                    }

                    $sections[$count]['activity_list'][] = array(
                      'active' => $active,
                      'title'  => $courserenderer->course_section_cm_name_title($activity, array()),
                      'classes' => $classes
                      );
                }
            }
            $count++;
        }

        return $sections;
    }

    public static function quickmessage($contactid, $message)
    {
        global $USER, $DB;
        $otheruserid = $contactid;
        $otheruserobj = $DB->get_record('user', array('id' => $otheruserid));
        $messagebody = $message;
        if (!empty($message) && !empty($otheruserobj)) {
            message_post_message($USER, $otheruserobj, $messagebody, FORMAT_MOODLE);
            return 'success';
        } else {
            return 'failed';
        }
    }

    /**
     * Moodle does not provide a helper function to generate limit sql (it's baked into get_records_sql).
     * This function is useful - e.g. improving performance of UNION statements.
     * Note, it will return empty strings for unsupported databases.
     *
     * @param int $from
     * @param int $to
     *
     * @return string
     */
    public static function limit_sql($from, $num)
    {
        global $DB;
        switch ($DB->get_dbfamily()) {
            case 'mysql':
                $sql = "LIMIT $from, $num";
                break;
            case 'postgres':
                $sql = "LIMIT $num OFFSET $from";
                break;
            case 'mssql':
            case 'oracle':
            default:
                // Not supported.
                $sql = '';
        }
        return $sql;
    }

    /**
     * Some moodle functions don't work correctly with specific userids and this provides a hacky workaround.
     *
     * Temporarily swaps global USER variable.
     * @param bool|stdClass|int $userorid
     */
    public static function swap_global_user($userorid = false)
    {
        global $USER;
        static $origuser = [];
        $user = self::get_user($userorid);
        if ($userorid !== false) {
            $origuser[] = $USER;
            $USER = $user;
        } else {
            $USER = array_pop($origuser);
        }
    }

    /**
     * Returns user object by passing user id.
     *
     * @param int $userid.
     * @return object user
     */
    public static function get_user($userorid = false)
    {
        global $USER, $DB;

        if ($userorid === false) {
            return $USER;
        }

        if (is_object($userorid)) {
            return $userorid;
        } elseif (is_number($userorid)) {
            if (intval($userorid) === $USER->id) {
                $user = $USER;
            } else {
                $user = $DB->get_record('user', ['id' => $userorid]);
            }
        } else {
            throw new coding_exception(get_string('parametermustbeobjectorintegerorstring', 'theme_remui', $userorid));
        }

        return $user;
    }

    /**
     * Get recent forum activity for all accessible forums across all courses.
     * @param bool|int|stdclass $userorid
     * @param int $limit
     * @return array
     * @throws \coding_exception
     */
    public static function recent_forum_activity($userorid = false, $limit = 10, $since = null)
    {
        global $CFG, $DB;

        if (file_exists($CFG->dirroot.'/mod/hsuforum')) {
            require_once($CFG->dirroot.'/mod/hsuforum/lib.php');
        }

        $user = self::get_user($userorid);
        if (!$user) {
            return [];
        }

        if ($since === null) {
            $since = time() - (12 * WEEKSECS);
        }

        // Get all relevant forum ids for SQL in statement.
        // We use the post limit for the number of forums we are interested in too -
        // as they are ordered by most recent post.
        $userforums = new \theme_remui\user_forums($user, $limit);
        $forumids = $userforums->forumids();
        $forumidsallgroups = $userforums->forumidsallgroups();
        $hsuforumids = $userforums->hsuforumids();
        $hsuforumidsallgroups = $userforums->hsuforumidsallgroups();

        if (empty($forumids) && empty($hsuforumids)) {
            return [];
        }

        $sqls = [];
        $params = [];

        // if ($limit > 0) {
        //     $limitsql = self::limit_sql(0, $limit); // Note, this is here for performance optimisations only.
        // } else {
        //     $limitsql = '';
        // }
        $limitsql = '';

        if (!empty($forumids)) {
            list($finsql, $finparams) = $DB->get_in_or_equal($forumids, SQL_PARAMS_NAMED, 'fina');
            $params = $finparams;
            $params = array_merge(
                $params,
                [
                     'sepgps1a' => SEPARATEGROUPS,
                     'sepgps2a' => SEPARATEGROUPS,
                     'user1a'   => $user->id,
                     'user2a'   => $user->id

                 ]
            );

            $fgpsql = '';
            if (!empty($forumidsallgroups)) {
                // Where a forum has a group mode of SEPARATEGROUPS we need a list of those forums where the current
                // user has the ability to access all groups.
                // This will be used in SQL later on to ensure they can see things in any groups.
                list($fgpsql, $fgpparams) = $DB->get_in_or_equal($forumidsallgroups, SQL_PARAMS_NAMED, 'allgpsa');
                $fgpsql = ' OR f1.id '.$fgpsql;
                $params = array_merge($params, $fgpparams);
            }

            $params['user2a'] = $user->id;

            $sqls[] = "(SELECT ".$DB->sql_concat("'F'", 'fp1.id')." AS id, 'forum' AS type, fp1.id AS postid,
                               fd1.forum, fp1.discussion, fp1.parent, fp1.userid, fp1.modified, fp1.subject,
                               fp1.message, 0 AS reveal, cm1.id AS cmid,
                               0 AS forumanonymous, f1.course, f1.name AS forumname,
                               u1.firstnamephonetic, u1.lastnamephonetic, u1.middlename, u1.alternatename, u1.firstname,
                               u1.lastname, u1.picture, u1.imagealt, u1.email,
                               c.shortname AS courseshortname, c.fullname AS coursefullname
                          FROM {forum_posts} fp1
                          JOIN {user} u1 ON u1.id = fp1.userid
                          JOIN {forum_discussions} fd1 ON fd1.id = fp1.discussion
                          JOIN {forum} f1 ON f1.id = fd1.forum AND f1.id $finsql
                          JOIN {course_modules} cm1 ON cm1.instance = f1.id
                          JOIN {modules} m1 ON m1.name = 'forum' AND cm1.module = m1.id
                          JOIN {course} c ON c.id = f1.course
                          LEFT JOIN {groups_members} gm1
                            ON cm1.groupmode = :sepgps1a
                           AND gm1.groupid = fd1.groupid
                           AND gm1.userid = :user1a
                         WHERE (cm1.groupmode <> :sepgps2a OR (gm1.userid IS NOT NULL $fgpsql))
                           AND fp1.userid <> :user2a
                           AND fp1.modified > $since
                        )
                        ORDER BY fp1.modified DESC
                               $limitsql
                         ";
            // TODO - when moodle gets private reply (anonymous) forums, we need to handle this here.
        }

        if (!empty($hsuforumids)) {
            list($afinsql, $afinparams) = $DB->get_in_or_equal($hsuforumids, SQL_PARAMS_NAMED, 'finb');
            $params = array_merge($params, $afinparams);
            $params = array_merge(
                $params,
                [
                                      'sepgps1b' => SEPARATEGROUPS,
                                      'sepgps2b' => SEPARATEGROUPS,
                                      'user1b'   => $user->id,
                                      'user2b'   => $user->id,
                                      'user3b'   => $user->id,
                                      'user4b'   => $user->id
                                  ]
            );

            $afgpsql = '';
            if (!empty($hsuforumidsallgroups)) {
                // Where a forum has a group mode of SEPARATEGROUPS we need a list of those forums where the current
                // user has the ability to access all groups.
                // This will be used in SQL later on to ensure they can see things in any groups.
                list($afgpsql, $afgpparams) = $DB->get_in_or_equal($hsuforumidsallgroups, SQL_PARAMS_NAMED, 'allgpsb');
                $afgpsql = ' OR f2.id '.$afgpsql;
                $params = array_merge($params, $afgpparams);
            }

            $sqls[] = "(SELECT ".$DB->sql_concat("'A'", 'fp2.id')." AS id, 'hsuforum' AS type, fp2.id AS postid,
                               fd2.forum, fp2.discussion, fp2.parent, fp2.userid, fp2.modified, fp2.subject,
                               fp2.message, fp2.reveal, cm2.id AS cmid,
                               f2.anonymous AS forumanonymous, f2.course, f2.name AS forumname,
                               u2.firstnamephonetic, u2.lastnamephonetic, u2.middlename, u2.alternatename, u2.firstname,
                               u2.lastname, u2.picture, u2.imagealt, u2.email,
                               c.shortname AS courseshortname, c.fullname AS coursefullname
                          FROM {hsuforum_posts} fp2
                          JOIN {user} u2 ON u2.id = fp2.userid
                          JOIN {hsuforum_discussions} fd2 ON fd2.id = fp2.discussion
                          JOIN {hsuforum} f2 ON f2.id = fd2.forum AND f2.id $afinsql
                          JOIN {course_modules} cm2 ON cm2.instance = f2.id
                          JOIN {modules} m2 ON m2.name = 'hsuforum' AND cm2.module = m2.id
                          JOIN {course} c ON c.id = f2.course
                          LEFT JOIN {groups_members} gm2
                            ON cm2.groupmode = :sepgps1b
                           AND gm2.groupid = fd2.groupid
                           AND gm2.userid = :user1b
                         WHERE (cm2.groupmode <> :sepgps2b OR (gm2.userid IS NOT NULL $afgpsql))
                           AND (fp2.privatereply = 0 OR fp2.privatereply = :user2b OR fp2.userid = :user3b)
                           AND fp2.userid <> :user4b
                           AND fp2.modified > $since
                      ORDER BY fp2.modified DESC
                               $limitsql
                        )
                         ";
        }

        $sql = '-- remui sql'. "\n".implode("\n".' UNION ALL '."\n", $sqls);
        if (count($sqls)>1) {
            $sql .= "\n".' ORDER BY modified DESC';
        }
        $posts = $DB->get_records_sql($sql, $params, 0, $limit);

        $activities = [];

        $discussionTopics = [];
        $topics = [];
        $count=-1;
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $postuser = (object)[
                    'id' => $post->userid,
                    'firstnamephonetic' => $post->firstnamephonetic,
                    'lastnamephonetic' => $post->lastnamephonetic,
                    'middlename' => $post->middlename,
                    'alternatename' => $post->alternatename,
                    'firstname' => $post->firstname,
                    'lastname' => $post->lastname,
                    'picture' => $post->picture,
                    'imagealt' => $post->imagealt,
                    'email' => $post->email
                ];

                // Update the user object with user profile photo
                $postuser->profilepicture = self::get_user_picture($postuser, 15);

                if ($post->type === 'hsuforum') {
                    $postuser = hsuforum_anonymize_user($postuser, (object)array(
                        'id' => $post->forum,
                        'course' => $post->course,
                        'anonymous' => $post->forumanonymous
                    ), $post);
                }

                if (!in_array($post->cmid, $topics)) {
                    $activities[] = (object)[
                        'type' => $post->type,
                        'cmid' => $post->cmid,
                        'name' => $post->subject,
                        'courseshortname' => $post->courseshortname,
                        'coursefullname' => $post->coursefullname,
                        'forumname' => $post->forumname,
                        'sectionnum' => null,
                        'timestamp' => $post->modified,
                        'content' => (object)[
                            'id' => $post->postid,
                            'discussion' => $post->discussion,
                            'subject' => $post->subject,
                            'parent' => $post->parent
                        ],
                        'user' => $postuser
                    ];
                    $topics[] = $post->cmid;
                    $count++;
                    $activities[$count]->replies = 1;
                    $activities[$count]->recentuser = [$postuser];
                } else {
                    $activities[$count]->replies = $activities[$count]->replies + 1;
                    if (!in_array($postuser, $activities[$count]->recentuser) && count($activities[$count]->recentuser) <= 2) {
                        array_push($activities[$count]->recentuser, $postuser);
                    }
                }
            }
        }
        return $activities;
    }

    /**
     * Get items which have been graded.
     *
     * @param bool $onlyactive - only show grades in courses actively enrolled on if true.
     * @return string
     * @throws \coding_exception
     */
    public static function graded()
    {
        $grades = self::events_graded();
        return $grades;
    }

    public static function grading()
    {
        global $USER, $PAGE;

        $grading = self::all_ungraded($USER->id);
        return $grading;
    }

    /**
     * Get everything graded from a specific date to the current date.
     *
     * @param bool $onlyactive - only show grades in courses actively enrolled on if true.
     * @param null|int $showfrom - timestamp to show grades from. Note if not set defaults to 1 month ago.
     * @return mixed
     */
    public static function events_graded()
    {
        global $DB, $USER;

        $params = [];
        $coursesql = '';
        $courses = enrol_get_my_courses();
        $courseids = array_keys($courses);
        $courseids[] = SITEID;
        list($coursesql, $params) = $DB->get_in_or_equal($courseids);
        $coursesql = 'AND gi.courseid '.$coursesql;

        $onemonthago = time() - (DAYSECS * 31);
        $showfrom = $onemonthago;

        $sql = "SELECT gg.*, gi.itemmodule, gi.iteminstance, gi.courseid, gi.itemtype
                  FROM {grade_grades} gg
                  JOIN {grade_items} gi
                    ON gg.itemid = gi.id $coursesql
                 WHERE gg.userid = ?
                   AND (gg.timemodified > ?
                    OR gg.timecreated > ?)
                   AND (gg.finalgrade IS NOT NULL
                    OR gg.rawgrade IS NOT NULL
                    OR gg.feedback IS NOT NULL)
                   AND gi.itemtype = 'mod'
                 ORDER BY timemodified DESC";

        $params = array_merge($params, [$USER->id, $showfrom, $showfrom]);
        $grades = $DB->get_records_sql($sql, $params, 0, 5);

        $eventdata = array();
        foreach ($grades as $grade) {
            $eventdata[] = $grade;
        }

        return $eventdata;
    }

    /**
     * Get all ungraded items.
     * @param int $userid
     * @return array
     */
    public static function all_ungraded($userid)
    {
        $courseids = self::gradeable_courseids($userid);

        if (empty($courseids)) {
            return array();
        }

        $mods = \core_plugin_manager::instance()->get_installed_plugins('mod');
        $mods = array_keys($mods);

        $grading = [];
        foreach ($mods as $mod) {
            $class = '\theme_remui\activity';
            $method = $mod.'_ungraded';
            if (method_exists($class, $method)) {
                $grading = array_merge($grading, call_user_func([$class, $method], $courseids));
            }
        }

        usort($grading, array('self', 'sort_graded'));

        return $grading;
    }

    /**
     * Get courses where user has the ability to view the gradebook.
     *
     * @param int $userid
     * @return array
     * @throws \coding_exception
     */
    public static function gradeable_courseids($userid)
    {
        $courses = enrol_get_all_users_courses($userid);
        $courseids = [];
        $capability = 'gradereport/grader:view';
        foreach ($courses as $course) {
            if (has_capability($capability, \context_course::instance($course->id), $userid)) {
                $courseids[] = $course->id;
            }
        }
        return $courseids;
    }

    /**
     * Sort function for ungraded items in the teachers personal menu.
     *
     * Compare on closetime, but fall back to openening time if not present.
     * Finally, sort by unique coursemodule id when the dates match.
     *
     * @return int
     */
    public static function sort_graded($left, $right)
    {
        if (empty($left->closetime)) {
            $lefttime = $left->opentime;
        } else {
            $lefttime = $left->closetime;
        }

        if (empty($right->closetime)) {
            $righttime = $right->opentime;
        } else {
            $righttime = $right->closetime;
        }

        if ($lefttime === $righttime) {
            if ($left->coursemoduleid === $right->coursemoduleid) {
                return 0;
            } elseif ($left->coursemoduleid < $right->coursemoduleid) {
                return -1;
            } else {
                return 1;
            }
        } elseif ($lefttime < $righttime) {
            return  -1;
        } else {
            return 1;
        }
    }

    // function to get the remote data from url
    public static function url_get_contents($url)
    {
        if (function_exists('curl_exec')) {
            $conn = curl_init($url);
            curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($conn, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
            $url_get_contents_data = (curl_exec($conn));
            curl_close($conn);
        } elseif (function_exists('file_get_contents')) {
            $url_get_contents_data = file_get_contents($url);
        } elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
            $handle = fopen($url, "r");
            $url_get_contents_data = stream_get_contents($handle);
        } else {
            $url_get_contents_data = false;
        }

        return $url_get_contents_data;
    }
  // send customer feedback to RemUI team.
    public static function sendfeedback($email = '', $fullname = '', $rating = '', $feedback = '')
    {
        global $USER;
        // prepare email
        $subject       = 'RemUI Customer Feedback';
        $email_content = '<h3>Customer Feedback</h3>';

        if (!empty($rating)) {
            $email_content     .= '<p><b>Rating: </b>'.$rating.'</p>';
        }

        if (!empty($fullname)) {
            $email_content     .= '<p><b>Customer Name: </b>'.$fullname.'</p>';
        }

        if (!empty($email)) {
            $email_content     .= '<p><b>Customer Email: </b>'.$email.'</p>';
        }

        if (!empty($feedback)) {
            $email_content     .= '<p><b>Feedback: </b>'.$feedback.'</p>';
        }

        $sent_mail = 0;
        $mail = get_mailer();

        $mail->From = $USER->email;
        $mail->Subject = $subject;
        $mail->Body    = $email_content;
        $mail->addCustomHeader('MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n");
        $mail->addAddress('gourav.govande@wisdmlabs.com', 'Wisdmlabs');
     
        if ($mail->send()) {
            $sent_mail = 1;
        }
        return array('sent' => $sent_mail);
    }

    public static function get_remui_announcemnets()
    {
        global $DB;
        $trans_expired = true;
        $announcements_data = array();
        // get existing data
        $trans_var = $DB->get_field_select('config_plugins', 'value', 'name = :name', array('name' => 'remui_announcements_trans'), IGNORE_MISSING);
        if ($trans_var) {
            $trans_var = unserialize($trans_var);
            if (is_array($trans_var) && time() > $trans_var[1] && $trans_var[1] > 0) {
                $trans_expired = true;
                try {
                    $DB->delete_records_select('config_plugins', 'name = :name', array('name' => 'remui_announcements_trans'));
                } catch (dml_exception $e) {
                    // keep catch empty if no record found
                }
            } else {
                return $trans_var[0]; // return announcements data if not expired
            }
        } else {
            $trans_expired = true;
        }
        if ($trans_expired) {
            // delete previous data and insert new data
            try {
                $DB->delete_records_select('config_plugins', 'name = :name', array('name' => 'remui_announcements_trans'));
            } catch (dml_exception $e) {
                // keep catch empty if no record found
            }

            $announcements_data = \theme_remui\utility::url_get_contents('http://remui.edwiser.org/remui_announcements.json');
            if (!empty($announcements_data)) {
                $announcements_data = json_decode($announcements_data, true); // decode the JSON into an associative array
                $time = time() + 60 * 60 * 24 * 5;
                // insert new announcements data
                $dataobject = new stdClass();
                $dataobject->plugin = 'theme_remui';
                $dataobject->name = 'remui_announcements_trans';
                $dataobject->value = serialize(array($announcements_data, $time));
                $DB->insert_record('config_plugins', $dataobject);

                return $announcements_data;
            }
        }
    }

    // check if update available by using json data
    public static function check_remui_update()
    {
        global $CFG;

        $response   = \theme_remui\utility::get_remui_announcemnets();
        $updateinfo = @$response['updateinfo'];
        $currentmoodle = substr($CFG->release, 0, 3);

        if (!empty($updateinfo) && isset($updateinfo[$currentmoodle])) {
            // get remui installed status
            $pm = \core_plugin_manager::instance();
            $currentremui = $pm->get_plugin_info('theme_remui')->release;

            if ($updateinfo[$currentmoodle]['version'] > $currentremui) {
                return 'available';
            }
        }
        return '';
    }

    // Function to get the data of analytics graph
    public static function get_analytics_for_courses($courseid)
    {
        global $USER;
        if ($courseid == 0) {
            return array();
        }
        // Get the list of users which are enrolled in the course
        $context = CONTEXT_COURSE::instance($courseid);
        $enrolledUsers = get_enrolled_users($context, 'mod/assignment:submit');

        // Get all the activities of the course which can be graded
        $gradeActivities = grade_get_gradable_activities($courseid);
        $qactivity = [];

        if (!empty($gradeActivities)) {
            foreach ($gradeActivities as $gradeActivity) {
                $attempt = 0;
                // Get all the grade items for the activity
                $gradeItemList = grade_get_grade_items_for_activity($gradeActivity, true);
                $gradeItem = reset($gradeItemList);

                // Get the last attempt grade value of logged in users
                $grade = new \grade_grade(array('itemid' => $gradeItem->id, 'userid' => $USER->id));
                if (isset($grade->rawgrade)) {
                    $average = intval($grade->rawgrade);
                    $attempt = 1;
                } else {
                    $average = 0;
                }

                // Get the average grade for the activity of last attempt of all enrolled users
                $sum = 0;
                $count = 0;
                foreach ($enrolledUsers as $user) {
                    $grade = new \grade_grade(array('itemid' => $gradeItem->id, 'userid' => $user->id));
                    if (isset($grade->rawgrade)) {
                        $sum += intval($grade->rawgrade);
                        $count++;
                    }
                }
                if ($count) {
                    $globalaverage = $sum/$count;
                } else {
                    $globalaverage = 0;
                }
                $qactivity[] = ['id' => $gradeActivity->id, 'name' => $gradeActivity->name, 'lastAttempt' => $average, 'globalAverage' => $globalaverage, 'attempt' => $attempt];
            }
        }

        $chartdata = array();
        $index = 0;
        $chartdata['datasets'][0]['label'] = 'Last Attempt';
        $chartdata['datasets'][1]['label'] =  'Global Average';
        $chartdata['datasets'][0]['backgroundColor'] = "#7dd3ae";
        $chartdata['datasets'][1]['backgroundColor'] = "#a58add";

        foreach ($qactivity as $activity) {
            $chartdata['labels'][$index] = $activity['name'];
            //$activity['lastAttempt']
            $chartdata['datasets'][0]['data'][$index] = $activity['lastAttempt'];
            $qactivity[$index]['lastAttempt'] = $chartdata['datasets'][0]['data'][$index];
            //$activity['globalAverage']
            $chartdata['datasets'][1]['data'][$index] = $activity['globalAverage'];

            if ($chartdata['datasets'][1]['data'][$index] < 0) {
                $chartdata['datasets'][1]['data'][$index] = 0;
            }
            $index++;
        }

        $highest = max(array_column($qactivity, 'lastAttempt'));
        $lowest = min(array_column($qactivity, 'lastAttempt'));
        if (count($qactivity)) {
            $average = intval(array_sum(array_column($qactivity, 'lastAttempt')) / count(array_column($qactivity, 'lastAttempt')));
        } else {
            $average = 0;
        }

        $maxs = array_keys(array_column($qactivity, 'lastAttempt'), $highest);
        $mins = array_keys(array_column($qactivity, 'lastAttempt'), $lowest);

        $maxactivityname = "";
        $minactivityname = "";

        foreach ($maxs as $max) {
            if ($qactivity[$max]['attempt'] == 1) {
                $maxactivityname .= $qactivity[$max]['name'] .", ";
            }
        }

        foreach ($mins as $min) {
            if ($qactivity[$min]['attempt'] == 1) {
                $minactivityname .= $qactivity[$min]['name'] .", ";
            }
        }


        $chartdata['highest'] = $highest;
        $chartdata['lowest'] = $lowest;
        $chartdata['average'] = $average;
        $chartdata['maxactivityname'] = rtrim($maxactivityname, ", ");
        $chartdata['minactivityname'] = rtrim($minactivityname, ", ");

        return $chartdata;
    }

    // Function to generate create a course link
    public static function getCreateCourseLink()
    {
        global $DB, $CFG;
        $categories = $DB->get_records_sql("SELECT * from {course_categories}");
        $createcourselink = "";
        if (!empty($categories)) {
            $firstCategory = reset($categories);
            $createcourselink = $CFG->wwwroot. '/course/edit.php?category='.$firstCategory->id;
        }
        return $createcourselink;
    }

    public static function get_courses_view_toggler()
    {
        global $CFG;

        $view = get_user_preferences('course_view_state');
        if (empty($view)) {
            $view = set_user_preference('course_view_state', 'grid');
            $view = 'grid';
        }

        if ($view == 'grid') {
            $grid = html_writer::start_tag('a', array('class' => 'grid_btn btn active togglebtn', 'title' => 'Grid view'));
            $list = html_writer::start_tag('a', array( 'class' => 'list_btn btn togglebtn', 'title' => 'List view'));
        } else {
            $grid = html_writer::start_tag('a', array('class' => 'grid_btn btn  togglebtn', 'title' => 'Grid view'));
            $list = html_writer::start_tag('a', array( 'class' => 'list_btn btn  togglebtn active', 'title' => 'List view'));
        }
        $content = '<div class="d-flex float-right">';
        $content .= $grid;
        $content .= '<i class="fa fa-th" aria-hidden="true"></i>';
        $content .= html_writer::end_tag('a');

        $content .= $list;
        $content .= '<i class="fa fa-list" aria-hidden="true"></i>';
        $content .= html_writer::end_tag('a');
        $content .= '</div>';
        return $content;
    }

    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_'.$k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval, 0, -1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) {
                    $ret[$k] = $array[$k];
                }
                $ret[$k][$col] = $array[$k][$col];
            }
        }

        $final_array = array();
        foreach ($ret as $key => $value) {
            $final_array[] = $value;
        }

        return $final_array;
    }

    // This will return the course object with progress percentages
    // $courseid = is the course id
    // returns stdClass object which consists of id, fullname, shortname, category, format,
    // startdata, enddate, timecreated, percentage, number of enrolled students.
    public static function get_course_progress($courseid)
    {
        global $DB, $USER;
        $percentage = 0;
        $course_progress = new stdClass();
        $course = get_course($courseid);

        $coursecontext = context_course::instance($courseid);
        $students = get_role_users(5, $coursecontext);

        foreach ($students as $studentid => $student) {
            $percentage += progress::get_course_progress_percentage($course, $student->id);
        }

        $course_progress->id = $course->id;
        $course_progress->fullname  = $course->fullname;
        $course_progress->shortname = $course->shortname;
        $course_progress->category  = $course->category;
        $course_progress->format    = $course->format;
        $course_progress->startdate = date("Y M, d", substr($course->startdate, 0, 10));
        $course_progress->enddate   = date("Y M, d", substr($course->enddate, 0, 10));
        $course_progress->timecreated = $course->timecreated;

        $course_progress->percentage = 0;

        if (0 != count($students)) {
            $course_progress->percentage  =  ceil(round($percentage / count($students), 2));
        } else {
            $course_progress->NoEnrollment = 'NoEnrollment';
        }

        $course_progress->enrolledStudents = count($students);

        return  $course_progress;
    }

    // Send message to user
    public static function send_message_to_user($studentid, $student_message)
    {
        global $USER, $DB, $SITE;

        $admin_user = $DB->get_record('user', array('id' => $USER->id), '*', MUST_EXIST);
        $user_object = $DB->get_record('user', array('id' => $studentid), '*', MUST_EXIST);

        $message = new \core\message\message();
        $message->courseid = $SITE->id;
        $message->component = 'moodle';
        $message->name = 'instantmessage';
        $message->userfrom = $admin_user;
        $message->userto = $user_object;
        $message->subject = 'message subject 1';
        $message->fullmessage = 'message body';
        $message->fullmessageformat = FORMAT_MARKDOWN;
        $message->fullmessagehtml = '<p>message body</p>';
        $message->smallmessage = $student_message;
        $message->notification = '0';
        $message->contexturl = '';
        $message->contexturlname = 'Context name';
        $message->replyto = "random@example.com";
        $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
        $message->set_additional_content('email', $content);
        $messageid = message_send($message);
        return $messageid;
    }

    // This will return the course progress table in html form
    public static function get_student_progress_view($courseid)
    {
        ob_start();

        $out    = '';
        $course = get_course($courseid);
        ?>
        
        <div class="panel panel-default panel-info mb-20">
        <div class="row col-12 mx-0">
            <div class="teacherdash col-xs-12 col-sm-12 col-md-10 mx-0 px-0 text-justify">
                <h3 class="my-15"><?php echo(trim($course->fullname));?></h3>
            </div>
            <div class="dashmenu col-xs-12 col-sm-12 col-md-2 px-0 text-right">
                <a class="toggle-desc panel-action px-15 icon fa-plus font-size-18 py-15" data-toggle="collapse" aria-hidden="true"></a>
               
                <a class="panel-action icon fa fa-times font-size-18 py-15" data-toggle="panel-close" aria-hidden="true" id="courserevertbtn"></a>
            </div>
        </div>
        <div class="panel-body px-15 py-5 text-justify collapse">
            <p><?php echo(trim(strip_tags($course->summary)));?></p>
        </div>
        </div>
        
        <!-- Modal Div -->
        <div class="modal fade modal-success " id="exampleModalSuccess" aria-hidden="true" aria-labelledby="exampleModalSuccess" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-center h-p100">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Message</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="studentid" id="messageidhidden">
                <textarea class="form-control" rows="5" id="messagearea"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-message" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary send-message" >Send</button>
            </div>
        </div>
        </div>
        </div>

        <table class="table table-hover dataTable table-striped w-full dtr-inline responsive nowrap" id="wdmCourseProgressTable" role="grid" aria-describedby="exampleTableTools_info" cellspacing="0" width="100%">
        <thead >
        <tr>
        <th class="pl-10">#</th>
        <th class="pl-10"><?php echo(get_string('name', 'theme_remui'))?></th>
        <th class="pl-10"><?php echo(get_string('status', 'theme_remui'))?></th>
        <th class="pl-10"><?php echo(get_string('progress', 'theme_remui'))?></th>
        </tr>
        </thead>
        <tbody>
        <?php
       
            $coursecontext = context_course::instance($courseid);
            $students      = get_role_users(5, $coursecontext);
            $student_cnt   = 0;

        foreach ($students as $studentid => $student) {
            ?>
            <tr>
                <td><?php echo(++$student_cnt)?></td>
                <td><?php echo($student->firstname.' '.$student->lastname)?>
                <i class="icon fa fa-envelope text-success custom-message float-right" name="colorChosen" data-target="#exampleModalSuccess" data-toggle="modal" data-studentid="'.$student->id.'" style="cursor:pointer;"></i></td>
                <?php $lastAccess = utility::get_last_course_access_time($courseid, $studentid);?>
                <td><?php echo($lastAccess->time) ?></td>

                <?php
                $progress = (int)progress::get_course_progress_percentage($course, $student->id);
                if (empty($progress)) {
                    $progress = 0;
                }
                $progress_class = $progress > 70? 'progress-bar-success': ($progress >30? 'progress-bar-warning':'progress-bar-danger');
                ?>
                <td>
                    <div class="pie-progress pie-progress-xs" data-plugin="pieProgress" data-valuemax="100" data-barcolor="#57c7d4" data-size="100" data-barsize="10" data-goal="86" 
                    aria-valuenow="<?php echo($progress);?>" role="progressbar" style="max-width: 35px!important;">
                    <div class="pie-progress-content ml-50" style="z-index:2;">
                    <div class="pie-progress-number"><?php echo($progress.'%')?></div>
                    </div>
                    </div>
                </td>
            </tr>

        <?php } ?>

        </tbody>
        </table>
    
        <?php
        return ob_get_clean();
    }

    public static function get_last_course_access_time($courseid, $studentid)
    {
        global $DB;
        $lastaccess =  new stdClass();

        $lastaccess->time = 'Never Accessed';
        $lastaccess->class = 'text-danger';

        $curr_time = time();

        $record = $DB->get_field('user_lastaccess', 'timeaccess', array('userid' => $studentid, 'courseid' => $courseid), IGNORE_MISSING);

        if (!empty($record)) {
            $lastaccess->time ='Last Active on '.date("d M, Y", substr($record, 0, 10));
            $lastaccess->class = ($curr_time - $record)>259200? 'text-danger':(($curr_time - $record)>129600? 'text-warning':'text-success');
        }
        return $lastaccess;
    }

    // Get recent courses accessed by user
    public static function get_recent_accessed_courses($limit)
    {
        global $USER, $DB, $CFG;
        if ($DB->get_dbfamily() == 'mssql') {
            $sql = 'SELECT TOP '.$limit.' ul.courseid, c.fullname
                   FROM {user_lastaccess} ul
                   JOIN {course} c ON c.id = ul.courseid
                   WHERE userid = ?
                   ORDER BY timeaccess
                   DESC';
        } else {
            $sql = 'SELECT ul.courseid, c.fullname
                   FROM {user_lastaccess} ul
                   JOIN {course} c ON c.id = ul.courseid
                   WHERE userid = ?
                   ORDER BY timeaccess
                   DESC LIMIT '.$limit;
        }
        $params = array('userid'=> $USER->id);
        $courses = $DB->get_records_sql($sql, $params);
        if ($courses) {
            return $courses;
        }
        return array();
    }
}
