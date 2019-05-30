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
require_once($CFG->libdir . '/behat/lib.php');

// var_dump("remui child");
// exit;
user_preference_allow_ajax_update('menubar_state', PARAM_ALPHA);
user_preference_allow_ajax_update('aside_right_state', PARAM_ALPHA);
user_preference_allow_ajax_update('course_view_state', PARAM_ALPHA);
$isfolded = 0;
// check if sidebar is fold or unfold & aside right state
if (isloggedin()) {
    $menubar_state = get_user_preferences('menubar_state', 'unfold');
    if ($menubar_state == 'fold') {
        $isfolded = 1;
    }
    $aside_right_state = get_user_preferences('aside_right_state', '');
} else {
    $menubar_state = 'fold';
    $isfolded = 1;
    $aside_right_state = '';
}
//$menubar_state = 'unfold';
$blockshtml = $OUTPUT->blocks('side-pre', array(), 'aside');
$hasblocks  = strpos($blockshtml, 'data-block=') !== false;

$extraclasses = [];
$extraclasses [] = 'site-menubar-'.$menubar_state.' site-menubar-fold-alt site-menubar-keep '. $aside_right_state;

if ($hasblocks) {
    $extraclasses [] = 'page-aside-fixed page-aside-right';
}

if ($PAGE->pagelayout == 'mypublic') {
    $extraclasses [] = ' page-profile ';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'cansignup' => (!empty($CFG->registerauth) && (!isloggedin() || isguestuser())),
    'signupurl' => new moodle_url('/login/signup.php'),
    'footerdata' => \theme_remui\utility::get_footer_data(),
    'isfolded'    => $isfolded
];

// for all partials
$templatecontext['navbarinverse'] = \theme_remui\toolbox::get_setting('navbarinverse');
$templatecontext['sitecolor']  = \theme_remui\toolbox::get_setting('sitecolor');
$templatecontext['sidebarcolor'] = \theme_remui\toolbox::get_setting('sidebarcolor');

$templatecontext['is_siteadmin'] = is_siteadmin();
$templatecontext['user_is_editing'] = $this->page->user_is_editing();
$templatecontext['usercanmanage'] = \theme_remui\utility::check_user_admin_cap();

$templatecontext['flatnavigation'] = flatnav_icon_support($PAGE->flatnav);
// var_dump($templatecontext['flatnavigation'][14]);
// exit;
$templatecontext['coursecreationlink'] = \theme_remui\utility::getCreateCourseLink();
if ($hasblocks) {
    $templatecontext['hasblock'] = true;
}

if (isloggedin() && \theme_remui\toolbox::get_setting('enablerecentcourses')) {
    $courses = \theme_remui\utility::get_recent_accessed_courses(5);
    $finalarr = array();
    foreach ($courses as $key => $course) {
        $templatecontext['hasrecent'] = true;
        $finalarr[] = array('id'=>$course->courseid, 'fullname'=>$course->fullname);
    }
    $templatecontext['recentcourses'] = $finalarr;
}

$templatecontext['enabledictionary'] = \theme_remui\toolbox::get_setting('enabledictionary');
