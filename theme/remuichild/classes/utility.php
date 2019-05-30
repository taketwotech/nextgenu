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


namespace theme_remuichild;

use user_picture;
use moodle_url;
use blog_listing;
use context_system;
use course_in_list;
use context_course;
use core_completion\progress;
use stdClass;
use html_writer;

// include_once($CFG->dirroot.'/mod/forum/lib.php');
// require_once($CFG->dirroot.'/calendar/lib.php');
// require_once($CFG->libdir. '/coursecatlib.php');
// require_once("$CFG->libdir/externallib.php");
// require_once($CFG->dirroot . "/message/lib.php");
// require_once($CFG->libdir. '/gradelib.php');
// require_once($CFG->dirroot. '/grade/querylib.php');
// require_once($CFG->dirroot.'/message/lib.php');
require_once($CFG->dirroot . "/mod/book/lib.php");
require_once($CFG->dirroot . "/mod/book/locallib.php");
class utility
{
    public static function get_activity_list()
    {
        global $COURSE, $PAGE;

        // return if no cm id
        if (!isset($PAGE->cm->id)) {
            return;
        }

        $modinfo = get_fast_modinfo($COURSE);
        // var_dump(sizeof($modinfo->get_section_info_all()));
        $sections_data = $modinfo->sections;
        $excluded_mods = array('label');
        $count = 0; // to print section count in sidebar
        $courserenderer = $PAGE->get_renderer('core', 'course');
        $sections = array();
        $temp = array_values(get_array_of_activities($COURSE->id));
        $chapter_id = optional_param('chapterid', 0 , PARAM_INT);
        // var_dump($chapter_id);
        // exit;
        // var_dump(array_values($temp));
        // exit;
        // var_dump($PAGE->url);
        // exit;
        $actkey = 0;
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
                // var_dump($activity->modname);
                $chapter_list = array();
                if($activity->modname == "book") {
                    
                    // $pageurl->param('test',0);
                    // var_dump($pageurl);
                    // var_dump($PAGE->url);
                    // exit;
                    $chapters = book_preload_chapters($temp[$actkey]);
                    $chapter_list = array();
                    // var_dump($chapters);
                    foreach ($chapters as $chkey => $chapter) {
                        $subchapter_item = array();
                        if(!$chapter->subchapter) {
                            // var_dump($chapter);
                            $pageurl = new moodle_url($PAGE->url);
                            // $chapter_title = "<a href=". $pageurl->param('chapterid', $chapter->id) .">";
                            // var_dump($chapter);
                            // exit;
                            $pageurl->param('chapterid', $chapter->id);
                            $chapter_list_item['chapter_title'] = "<a class='permalink-section' href=". $pageurl ."><i class='fa fa-file-text fix-misplaced' aria-hidden='true'></i> " . $chapter->title;
                            
                            
                            $chapter_list_item['chapter_classes'] = " layer-2";
                            // var_dump($chapter->subchapters);
                            if($chapter->id == $chapter_id || in_array($chapter_id, $chapter->subchapters)) {
                                $chapter_list_item['chapter_classes'] .= " active open";
                                $chapter_list_item['chapter_title'] = "<a class='permalink-section' href='javascript:void(0)'><i class='fa fa-file-text fix-misplaced' aria-hidden='true'></i> " . $chapter->title;
                            }
                            if($chapter->subchapters) {
                                $chapter_list_item['chapter_classes'] .= " has-sub";
                                $chapter_list_item['chapter_title'] .= "<span class='site-menu-arrow'></span>";
                            }
                            $chapter_list_item['chapter_title'] .= "</a>";
                            $chapter_list[$chapter->id] = $chapter_list_item;
                            // var_dump($chapter_list_item);
                            // if($chapter->subchapters) {
                            //     $chapter_list_item['subchapters'] = array();
                            //     foreach ($chapter->subchapters as $subchapter) {
                            //         $subpageurl = new moodle_url($PAGE->url);
                            //         $subpageurl->param('chapterid', $subchapter);
                            //         $subchapter_item = 
                            //         $subchapter_item['sub_chapter_title'] = "<a href=". $pageurl .">" . $chapter->title . "</a>";
                            //     }
                            // }
                        }
                        else if($chapter->subchapter) {
                            $subpageurl = new moodle_url($PAGE->url);
                            $subpageurl->param('chapterid', $chapter->id);
                            // $subchapter_item = 
                            $subchapter_item['sub_chapter_title'] = "<a class='permalink-section' href=". $subpageurl .">" . $chapter->title . "</a>";
                            $subchapter_item['sub_chapter_classes'] = " layer-3";
                            if($chapter->id == $chapter_id) {
                                $subchapter_item['sub_chapter_classes'] .= " active";
                                $subchapter_item['sub_chapter_title'] = "<a class='permalink-section' href='javascript:void(0)'>" . $chapter->title . "</a>";
                            }
                            $chapter_list[$chapter->parent]['subchapters'][] = $subchapter_item;
                        }
                    }
                }
                
                $classes = '';
                $completioninfo = new \completion_info($COURSE);
                $activity_completion = $courserenderer->course_section_cm_completion($COURSE, $completioninfo, $activity, array());

                if (!in_array($activity->modname, $excluded_mods)) {
                    // check if current activity
                    $active = ' ';
                    if ($PAGE->cm->id == $activity_id) {
                        $active = 'open active ';
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
                    // $classes .= " has-sub";

                    $sections[$count]['activity_list'][] = array(
                      'active' => $active,
                      'title'  => $courserenderer->course_section_cm_name_title($activity, array()),
                      'classes' => $classes,
                      'chapter_list' => array_values($chapter_list)
                      );
                }
                $actkey++;
            }

            $count++;
        }
        // var_dump($sections[1]['activity_list'][1]['chapter_list'][1]);
        // exit;

        return $sections;
    }
}