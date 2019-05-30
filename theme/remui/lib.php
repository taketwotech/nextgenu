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
 * Theme functions.
 *
 * @package    theme_remui
 * @copyright  WisdmLabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


if (isset($_POST['applysitewidecolor'])) {
    remui_clear_cache();
}

// handle license status change on form submit
$l_controller = new \theme_remui\controller\license_controller();
$l_controller->addData();

/**
 * CSS Processor
 *
 * @param string $css
 * @param theme_config $theme
 * @return string
 */
function theme_remui_process_css($css, $theme)
{
    global $PAGE, $OUTPUT;
    $outputus = $PAGE->get_renderer('theme_remui', 'core');
    \theme_remui\toolbox::set_core_renderer($outputus);

    // set login background
    $tag = '[[setting:login_bg]]';
    $loginbg = \theme_remui\toolbox::setting_file_url('loginsettingpic', 'loginsettingpic');
    if (empty($loginbg)) {
        $loginbg = \theme_remui\toolbox::image_url('login_bg', 'theme');
    }
    $css = str_replace($tag, $loginbg, $css);

    // Set the signup panel text color
    $signuptextcolor = \theme_remui\toolbox::get_setting('signuptextcolor');
    $css = \theme_remui\toolbox::set_color($css, $signuptextcolor, "'[[setting:signuptextcolor]]'", '#fff');

    // Get the theme font from setting and apply it in CSS
    if (\theme_remui\toolbox::get_setting('fontselect') === "2") {
        $fontname = ucwords(\theme_remui\toolbox::get_setting('fontname'));
    }
    if (empty($fontname)) {
        $fontname = 'Open Sans';
    }

    $css = \theme_remui\toolbox::set_font($css, $fontname);

    // Set custom CSS.
    $customcss = \theme_remui\toolbox::get_setting('customcss');
    $css = \theme_remui\toolbox::set_customcss($css, $customcss);

    // custom color sitewide
    $colorhex = \theme_remui\toolbox::get_setting('sitecolorhex');
    if (empty($colorhex)) {
        $colorhex = '#62a8ea';
    } else {
        $colorhex = '#'.$colorhex;
    }

    $colorobj = new \theme_remui\Color($colorhex);
    if ($colorhex !== '#62a8ea') {
        $css = str_replace('#62a8ea', $colorhex, $css);
        $css = str_replace('#89bceb', '#'.$colorobj->darken(3), $css);
        $css = str_replace('#4397e6', '#'.$colorobj->darken(5), $css);
        $css = str_replace('#e8f1f8', '#'.$colorobj->lighten(32), $css);
        $css = str_replace('rgba(53, 131, 202, .07)', '#'.$colorobj->lighten(32), $css);
        $css = str_replace('rgba(53, 131, 202, .04)', '#'.$colorobj->lighten(34), $css);
    }

    return $css;
}

// clear theme cache on click 'apply sitewide color'
function remui_clear_cache()
{
    theme_reset_all_caches();
}

function flatnav_icon_support($flatnav)
{
    global $CFG, $USER, $PAGE;
    // Getting strings for privatefiles & competencies, because their keys are numeric in $PAGE-flatnav
    $pf   = get_string('privatefiles');
    $cmpt = get_string('competencies', 'core_competency');
    $flatnav_new = array();
    $home_count  = 0;
    $coursecount = 0;
    foreach ($flatnav as $key => $value) {
        $key = $coursecount++;
        $flatnav_new[$key] = $value;
        switch ($value->key) {
            case 'myhome':
                $flatnav_new[$key]->remuiicon = 'fa-dashboard';
                break;
            case 'home':
                $flatnav_new[$key]->remuiicon = 'fa-home';
                if ($home_count == 1) {
                    $flatnav_new[$key]->remuiicon = 'fa-dashboard';
                }
                $home_count++;
                break;
            case 'calendar':
                $flatnav_new[$key]->remuiicon = 'fa-calendar';
                break;
            case 'mycourses':
                $mycoursekey = $key;    // Store a key value to check if mycourses available
                $flatnav_new[$key]->remuiicon = 'fa-archive';
                $flatnav_new[$key]->action    = $CFG->wwwroot . "/course/index.php?mycourses=1";
                if ($PAGE->pagelayout == 'coursecategory' && optional_param('mycourses', null, PARAM_TEXT)) {
                    $flatnav_new[$key]->isactive = true;
                }
                break;
            case 'sitesettings':
                $flatnav_new[$key]->remuiicon = 'fa-cog';
                if ($PAGE->pagelayout == 'admin') {
                    $flatnav_new[$key]->isactive = true;
                }
                break;
            case 'addblock':
                $flatnav_new[$key]->remuiicon = 'fa-plus-circle ';
                break;
            case 'badgesview':
                $flatnav_new[$key]->remuiicon = 'fa-bookmark';
                break;
            case 'participants':
                $flatnav_new[$key]->remuiicon = 'fa-users';
                break;
            case 'grades':
                $flatnav_new[$key]->remuiicon = 'fa-star';
                break;
            case 'coursehome':
                $flatnav_new[$key]->remuiicon = 'fa-archive';
                break;
            default:
                // Check Whether the link has course id number
                if (is_numeric($value->key)) {
                    // Check for course type i.e. is it 20?
                    if ($flatnav_new[$key]->type == 20) {
                        $mycourses[] = $value;
                        unset($flatnav_new[$key]);
                        $coursecount--;
                        break;
                    }
                }
                $flatnav_new[$key]->remuiicon = 'fa-folder';
                if (!strpos($flatnav_new[$key]->action, 'section')) {
                    $flatnav_new[$key]->hidable = true;
                }
                
                break;
        }
        switch ($value->text) {
            case $pf:
                $flatnav_new[$key]->remuiicon = 'fa-paste';
                break;
            case $cmpt:
                $flatnav_new[$key]->remuiicon = 'fa-check-circle';
                break;
        }
    }
    if (!empty($mycourses)) {
        $flatnav_new[$mycoursekey]->ismycourses = true;
        $flatnav_new[$mycoursekey]->mycourses   = $mycourses;
        if (count($mycourses) == 10) {
            $flatnav_new[$mycoursekey]->hasmore = true;
        }
    }
    return $flatnav_new;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_remui_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    static $theme;
    $course = $course;
    $cm = $cm;
    if (empty($theme)) {
        $theme = theme_config::load('remui');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'frontpageaboutusimage') {
            return $theme->setting_file_serve('frontpageaboutusimage', $args, $forcedownload, $options);
        } elseif ($filearea === 'loginsettingpic') {
            return $theme->setting_file_serve('loginsettingpic', $args, $forcedownload, $options);
        } elseif ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } elseif ($filearea === 'logomini') {
            return $theme->setting_file_serve('logomini', $args, $forcedownload, $options);
        } elseif (preg_match("/^(slideimage|testimonialimage|frontpageblockimage)[1-5]/", $filearea)) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } elseif ($filearea === 'faviconurl') {
            return $theme->setting_file_serve('faviconurl', $args, $forcedownload, $options);
        } elseif ($filearea === 'staticimage') {
            return $theme->setting_file_serve('staticimage', $args, $forcedownload, $options);
        } elseif ($filearea === 'layoutimage') {
            return $theme->setting_file_serve('layoutimage', $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

// Activities Navigation Previous Next
function activities_navigation_previous_next($pagelayout = null, $id = null, $course = null)
{
    $anpn = "";
    if ($type = get_config('theme_remui', 'activitynextpreviousbutton')) {
        global $USER, $CFG;
        if ($pagelayout == 'incourse') {
            $prev = $next = $count = 0;
            $prevlink = $nextlink = $first = $last = $nextname = $prevname = '';
            $visible;

            $allactivities = get_array_of_activities($course->id);

            foreach ($allactivities as $activity) {
                if (isset($activity->deletioninprogress) && $activity->deletioninprogress == 1) {
                    continue;
                }
                if ($activity->visible == 1 || $USER->id == 2) {
                    $visible = 1;
                    $count++;
                } else {
                    $visible = 0;
                }
                if ($visible == 1) {
                    if (!$first) {
                        $first = $activity->cm;
                    }
                    if ($next == 1) {
                        $nextlink = $CFG->wwwroot.'/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
                        $nextname = $activity->name;
                        $next = 0;
                    }
                    if ($id == $activity->cm) {
                        $prev = $next = 1;
                    }
                    if ($prev == 0) {
                        $prevlink = $CFG->wwwroot.'/mod/'.$activity->mod.'/view.php?id='.$activity->cm;
                        $prevname = $activity->name;
                    }
                    $last = $activity->cm;
                }
            }

            if ($count > 1) {
                switch ($type) {
                    case 1:
                        $prev = get_string('activityprev', 'theme_remui');
                        $next = get_string('activitynext', 'theme_remui');
                        break;
                    case 2:
                        $prev = '<< '.$prevname;
                        $next = $nextname . ' >>';
                        break;
                }

                $anpn = "<div class='pad row' style='clear:both'><div class='col-lg-12 px-45'>";
                if ($id == $first) {
                    $anpn .= "<div class='pull-right'><a href='".$nextlink."' class ='btn btn-primary' title='".$nextname."'>".$next."</a></div>";
                } elseif ($id == $last) {
                    $anpn .= "<div class='pull-left'><a href='".$prevlink."' class ='btn btn-primary' title='".$prevname."'>".$prev."</a></div>";
                } else {
                    $anpn .= "<div class='pull-left'><a href='".$prevlink."' class ='btn btn-primary' title='".$prevname."'>".$prev."</a></div><div class='pull-right'><a href='".$nextlink."' class ='btn btn-primary' title='".$nextname."'>".$next."</a></div>";
                }
                $anpn .= "</div></div>";
            } else {
                $anpn = "";
            }
        } else {
            $anpn = "";
        }
    }
    return $anpn;
}
