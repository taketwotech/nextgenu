<?php

defined('MOODLE_INTERNAL') || die();

include_once($CFG->dirroot . '/course/lib.php');
include_once($CFG->dirroot . '/lib/navigationlib.php');

/*function load_section_activities(flat_navigation_node $sectionnode, $sectionnumber, array $activities = null, $course = null) {
        global $CFG, $SITE;
        // A static counter for JS function naming
        static $legacyonclickcounter = 0;

        $activitynodes = array();
        if (empty($activities)) {
            return $activitynodes;
        }

        if (!is_object($course)) {
            $activity = reset($activities);
            $courseid = $activity->course;
        } else {
            $courseid = $course->id;
        }
        $showactivities = ($courseid != $SITE->id || !empty($CFG->navshowfrontpagemods));

        foreach ($activities as $activity) {
            if ($activity->section != $sectionnumber) {
                continue;
            }
            if ($activity->icon) {
                $icon = new pix_icon($activity->icon, get_string('modulename', $activity->modname), $activity->iconcomponent);
            } else {
                $icon = new pix_icon('icon', get_string('modulename', $activity->modname), $activity->modname);
            }

            // Prepare the default name and url for the node
            $activityname = format_string($activity->name, true, array('context' => context_module::instance($activity->id)));
            $action = new moodle_url($activity->url);

            // Check if the onclick property is set (puke!)
            if (!empty($activity->onclick)) {
                // Increment the counter so that we have a unique number.
                $legacyonclickcounter++;
                // Generate the function name we will use
                $functionname = 'legacy_activity_onclick_handler_'.$legacyonclickcounter;
                $propogrationhandler = '';
                // Check if we need to cancel propogation. Remember inline onclick
                // events would return false if they wanted to prevent propogation and the
                // default action.
                if (strpos($activity->onclick, 'return false')) {
                    $propogrationhandler = 'e.halt();';
                }
                // Decode the onclick - it has already been encoded for display (puke)
                $onclick = htmlspecialchars_decode($activity->onclick, ENT_QUOTES);
                // Build the JS function the click event will call
                $jscode = "function {$functionname}(e) { $propogrationhandler $onclick }";
                // $this->page->requires->js_amd_inline($jscode);
                // Override the default url with the new action link
                $action = new action_link($action, $activityname, new component_action('click', $functionname));
            }

            $activitynode = $sectionnode->add($activityname, $action, navigation_node::TYPE_ACTIVITY, null, $activity->id, $icon);
            $activitynode->title(get_string('modulename', $activity->modname));
            $activitynode->hidden = $activity->hidden;
            $activitynode->display = $showactivities && $activity->display;
            $activitynode->nodetype = $activity->nodetype;
            $activitynodes[$activity->id] = $activitynode;
        }

        return $activitynodes;
    }*/

// function theme_remuichild_flatnav_icon_support($flatnav)
// {
//     global $CFG, $USER, $PAGE;
//     // Getting strings for privatefiles & competencies, because their keys are numeric in $PAGE-flatnav
//     $pf   = get_string('privatefiles');
//     $cmpt = get_string('competencies', 'core_competency');
//     $flatnav_new = array();
//     $home_count  = 0;
//     $coursecount = 0;
//     // $sectioncount = 0;
//     // var_dump($flatnav);
//     // exit;
//     foreach ($flatnav as $key => $value) {
//         // var_dump($value);
//         $key = $coursecount++;
//         $flatnav_new[$key] = $value;
//         switch ($value->key) {
//             case 'myhome':
//                 $flatnav_new[$key]->remuiicon = 'fa-dashboard';
//                 break;
//             case 'home':
//                 $flatnav_new[$key]->remuiicon = 'fa-home';
//                 if ($home_count == 1) {
//                     $flatnav_new[$key]->remuiicon = 'fa-dashboard';
//                 }
//                 $home_count++;
//                 break;
//             case 'calendar':
//                 $flatnav_new[$key]->remuiicon = 'fa-calendar';
//                 break;
//             case 'mycourses':
//                 $mycoursekey = $key;    // Store a key value to check if mycourses available
//                 $flatnav_new[$key]->remuiicon = 'fa-archive';
//                 $flatnav_new[$key]->action    = $CFG->wwwroot . "/course/index.php?mycourses=1";
//                 if ($PAGE->pagelayout == 'coursecategory' && optional_param('mycourses', null, PARAM_TEXT)) {
//                     $flatnav_new[$key]->isactive = true;
//                 }
//                 break;
//             case 'sitesettings':
//                 $flatnav_new[$key]->remuiicon = 'fa-cog';
//                 if ($PAGE->pagelayout == 'admin') {
//                     $flatnav_new[$key]->isactive = true;
//                 }
//                 break;
//             case 'addblock':
//                 $flatnav_new[$key]->remuiicon = 'fa-plus-circle ';
//                 break;
//             case 'badgesview':
//                 $flatnav_new[$key]->remuiicon = 'fa-bookmark';
//                 break;
//             case 'participants':
//                 $flatnav_new[$key]->remuiicon = 'fa-users';
//                 break;
//             case 'grades':
//                 $flatnav_new[$key]->remuiicon = 'fa-star';
//                 break;
//             case 'coursehome':
//                 $flatnav_new[$key]->remuiicon = 'fa-archive';
//                 // $flatnav_new[$key]->action    = $CFG->wwwroot . "/course/index.php?mycourses=1";
//                 // $flatnav_new[$key]->coursetopics = 1;
//                 // var_dump($flatnav_new[$key]);
//                 // exit;
//                 // $flatnav_new[$key]->children = new navigation_node_collection();
//                 // $sectionkey = $key;
//                 break;
//             default:
//                 // Check Whether the link has course id number
//                 if (is_numeric($value->key)) {
//                     // Check for course type i.e. is it 20?
//                     if ($flatnav_new[$key]->type == 20) {
//                         $mycourses[] = $value;
//                         unset($flatnav_new[$key]);
//                         $coursecount--;
//                         // var_dump($mycourses);
//                         // exit;
//                         break;
//                     }
//                 }

//                 // Check if node is a section
//                 // if ($value->is_section()) {
//                 //     // var_dump($value->is_section());
//                 //     // exit;
//                 //     $sections[] = $value;
//                 //     // var_dump($value->action);
//                 //     unset($flatnav_new[$key]);
//                 //     $coursecount--;
//                 //     break;
//                 // }
//                 $flatnav_new[$key]->remuiicon = 'fa-folder';
//                 // $flatnav_new[$key]->get_indent = true;
//                 // if (strpos($flatnav_new[$key]->action, 'section')) {
//                 //     // $flatnav_new[$key]->set_parent($flatnav_new[$sectionkey]);
//                 //     // var_dump($flatnav_new[$key]);
//                 //     // $flatnav_new[$sectionkey]->children->add($flatnav_new[$key]);
                    
//                 //     unset($flatnav_new[$key]);

//                 // }
//                 if (!strpos($flatnav_new[$key]->action, 'section')) {
//                     // var_dump($flatnav_new[$key]->action);
//                     // unset($flatnav_new[$key]);
//                     $flatnav_new[$key]->hidable = true;
//                 }
                
//                 break;
//         }
//         switch ($value->text) {
//             case $pf:
//                 $flatnav_new[$key]->remuiicon = 'fa-paste';
//                 break;
//             case $cmpt:
//                 $flatnav_new[$key]->remuiicon = 'fa-check-circle';
//                 break;
//         }
//     }
//     if (!empty($mycourses)) {
//         $flatnav_new[$mycoursekey]->ismycourses = true;
//         $flatnav_new[$mycoursekey]->mycourses   = $mycourses;
//         if (count($mycourses) == 10) {
//             $flatnav_new[$mycoursekey]->hasmore = true;
//         }
//     }
//     // if (!empty($sections)) {
//     //     // var_dump($sections);
//     //     // exit;
//     //     $flatnav_new[$sectionkey]->hassection = true;
//     //     $flatnav_new[$sectionkey]->sections   = $sections;
        
//     //     // if (count($mycourses) == 10) {
//     //     //     $flatnav_new[$sectionkey]->hasmore = true;
//     //     // }
//     // }
//     // var_dump($flatnav_new[$sectionkey]);
//     return $flatnav_new;
// }

// function theme_remuichild_activities_navigation_previous_next($pagelayout = null, $id = null, $course = null)
// {
//     $anpn = "";
//     if ($type = get_config('theme_remui', 'activitynextpreviousbutton')) {
//         global $USER, $CFG;
//         if ($pagelayout == 'incourse') {
//             $prev = $next = $count = 0;
//             $prevlink = $nextlink = $first = $last = $nextname = $prevname = '';
//             $visible;

//             $allactivities = get_array_of_activities($course->id);

//             foreach ($allactivities as $activity) {
//                 if ($activity->mod == 'book') {
//                     $activity->chapters = book_preload_chapters($activity);
//                     // var_dump($activity);
//                     // exit;
//                 }
//                 if (isset($activity->deletioninprogress) && $activity->deletioninprogress == 1) {
//                     continue;
//                 }
//                 if ($activity->visible == 1 || $USER->id == 2) {
//                     $visible = 1;
//                     $count++;
//                 } else {
//                     $visible = 0;
//                 }
//                 if ($visible == 1) {
//                     if (!$first) {
//                         $first = $activity->cm;
//                     }
//                     if ($next == 1) {
//                         $nextlink = $CFG->wwwroot.'/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
//                         $nextname = $activity->name;
//                         $next = 0;
//                     }
//                     if ($id == $activity->cm) {
//                         $prev = $next = 1;
//                     }
//                     if ($prev == 0) {
//                         $prevlink = $CFG->wwwroot.'/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
//                         $prevname = $activity->name;
//                     }
//                     $last = $activity->cm;
//                 }
//             }

//             if ($count > 1) {
//                 switch ($type) {
//                     case 1:
//                         $prev = get_string('activityprev', 'theme_remui');
//                         $next = get_string('activitynext', 'theme_remui');
//                         break;
//                     case 2:
//                         $prev = '<< '.$prevname;
//                         $next = $nextname . ' >>';
//                         break;
//                 }

//                 $anpn = "<div class='pad row' style='clear:both'><div class='col-lg-12 px-45'>";
//                 if ($id == $first) {
//                     $anpn .= "<div class='pull-right'><a href='".$nextlink."' class ='btn btn-primary' title='".$nextname."'>".$next."</a></div>";
//                 } elseif ($id == $last) {
//                     $anpn .= "<div class='pull-left'><a href='".$prevlink."' class ='btn btn-primary' title='".$prevname."'>".$prev."</a></div>";
//                 } else {
//                     $anpn .= "<div class='pull-left'><a href='".$prevlink."' class ='btn btn-primary' title='".$prevname."'>".$prev."</a></div><div class='pull-right'><a href='".$nextlink."' class ='btn btn-primary' title='".$nextname."'>".$next."</a></div>";
//                 }
//                 $anpn .= "</div></div>";
//             } else {
//                 $anpn = "";
//             }
//         } else {
//             $anpn = "";
//         }
//     }
//     return $anpn;
// }