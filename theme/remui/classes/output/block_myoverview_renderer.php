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
 * Overriden block settings renderer.
 *
 * @package    theme_remui
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\output;

defined('MOODLE_INTERNAL') || die();
use renderable;
use core_completion\progress;

require_once($CFG->dirroot . '/blocks/myoverview/classes/output/renderer.php');
require_once($CFG->dirroot . '/lib/coursecatlib.php');

/**
 * Overriden block myoverview renderer.
 *
 * @package    theme_remui
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myoverview_renderer extends \block_myoverview\output\renderer
{
    /**
     * Return the main content for the block overview.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_main(\block_myoverview\output\main $main)
    {
        global $USER;
        $incoursecount = 0;
        $futurecoursecount = 0;
        $duecoursecount = 0;

        $data = $main->export_for_template($this);
        if (isset($data['coursesview']['inprogress'])) {
            $coursesInProgress = $data['coursesview']['inprogress'];
            // Setting Images for InProgress Courses
            $coursepages = $coursesInProgress['pages'];
            $totalcourses = self::setCourseImage($coursepages);
            $incoursecount = count($totalcourses);
            $totalcourses = self::setExtraCourseProperties($totalcourses);
            $data['coursesview']['inprogress']['pages'][0]['courses'] = $totalcourses;
        }

        if (isset($data['coursesview']['future'])) {
            $coursesInFuture = $data['coursesview']['future'];
            //Setting Images for InFuture Courses
            $coursepages = $coursesInFuture['pages'];
            $totalcourses = self::setCourseImage($coursepages);
            $futurecoursecount = count($totalcourses);
            $totalcourses = self::setExtraCourseProperties($totalcourses);
            $data['coursesview']['future']['pages'][0]['courses'] = $totalcourses;
        }

        if (isset($data['coursesview']['past'])) {
            $coursesInPast = $data['coursesview']['past'];
            //Setting Images for Past Courses
            $coursepages = $coursesInPast['pages'];
            $totalcourses = self::setCourseImage($coursepages);
            $duecoursecount = count($totalcourses);
            $totalcourses = self::setExtraCourseProperties($totalcourses);
            $data['coursesview']['past']['pages'][0]['courses'] = $totalcourses;
        }

        // Welcome Section Data
        // Get Enrolled Course Count
        $courses = enrol_get_all_users_courses($USER->id, true);
        $activityCount = 0;
        $courseProgress = 0;
        $activitiesProgress = 0;
        $completionCourses = 0;
        $completionActivities = 0;

        foreach ($courses as $course) {
            $completion = new \completion_info($course);
            if ($completion->is_enabled()) {
                $percentage = progress::get_course_progress_percentage($course, $USER->id);
                if (!empty($percentage)) {
                    $courseProgress += floor($percentage);
                }
                $completionCourses++;
            }

            // Get all the actvities of the courses
            $allActivities = get_array_of_activities($course->id);
            $activityCount += count($allActivities);

            // Get completed activities percentage
            $modules = $completion->get_activities();
            foreach ($modules as $module) {
                $moduledata = $completion->get_data($module, false, $USER->id);
                $activitiesProgress += $moduledata->completionstate == COMPLETION_INCOMPLETE ? 0 : 1;
                $completionActivities++;
            }
        }
        $data['username'] = $USER->firstname;
        $data['totalcourses'] = count($courses);
        if ($completionCourses !== 0) {
            $data['courseprogress'] = floor($courseProgress/$completionCourses);
        } else {
            $data['courseprogress'] = 0;
        }
        if ($completionActivities !== 0) {
            $data['activitiesprogress'] = floor(($activitiesProgress/$completionActivities) * 100);
        } else {
            $data['activitiesprogress'] = 0;
        }
        $data['totalactivities'] = $activityCount;
        $data['incoursecount'] = $incoursecount;
        $data['futurecount'] = $futurecoursecount;
        $data['duecount'] = $duecoursecount;
        $latestdashboard = \theme_remui\toolbox::get_setting('enabledashboard');
        if ($latestdashboard) {
            $data['latestdashboard'] = 1;
        }
        return $this->render_from_template('block_myoverview/main', $data);
    }

    public static function setCourseImage($coursepages)
    {
        global $CFG, $OUTPUT;
        $totalcourses = [];
        foreach ($coursepages as $coursepage) {
            foreach ($coursepage['courses'] as $course) {
                array_push($totalcourses, $course);
                $course1 = new \course_in_list($course);
                // background image
                $course->imageurl = $OUTPUT->image_url('placeholder', 'theme');
                foreach ($course1->get_course_overviewfiles() as $file) {
                    $isimage = $file->is_valid_image();
                    $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php", '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                                            $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                    $course->imageurl = $courseimage;
                }
            }
        }
        return $totalcourses;
    }

    // This function will set the properties important for slick slider display
    // This function changes the following properties
    // 1) Color of each tile
    // 2) Name to be displayed will be less than 35 characters only
    // 3) Water level for percentage

    public static function setExtraCourseProperties($totalcourses)
    {
        $color = array('#f2a654','#fe6768','#57c7d4','#56c19a','#526069  ','#46657d');

        foreach ($totalcourses as $key => $value) {
            // set the color for each card
            $array_index = $key % count($color);
            $totalcourses[$key]->color = $color[$array_index];

            // set the text limit to course name value
            $totalcourses[$key]->fullnamedisplay = (strlen($totalcourses[$key]->fullnamedisplay) > 35) ? substr($totalcourses[$key]->fullnamedisplay, 0, 35).'...' : $totalcourses[$key]->fullnamedisplay;

            // set the water level in percentage
            if (!empty($value->progress)) {
                $totalcourses[$key]->waterPercentage = 100-$value->progress;
            }
        }
        return $totalcourses;
    }
}
