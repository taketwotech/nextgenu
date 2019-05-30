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
 * A secure layout for the boost theme.
 *
 * @package   theme_boost
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//user_preference_allow_ajax_update('menubar_state', PARAM_ALPHA);
user_preference_allow_ajax_update('aside_right_state', PARAM_ALPHA);

// check if sidebar is fold or unfold & aside right state
if (isloggedin()) {
    //$menubar_state = get_user_preferences('menubar_state', 'unfold');
    $aside_right_state = get_user_preferences('aside_right_state', '');
} else {
   //$menubar_state = 'fold';
    $aside_right_state = '';
}

$blockshtml = $OUTPUT->blocks('side-pre', array(), 'aside');
$hasblocks  = strpos($blockshtml, 'data-block=') !== false;

$extraclasses = [];
$extraclasses [] = $aside_right_state;

if($hasblocks) {
    $extraclasses [] = 'page-aside-fixed page-aside-right';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'footerdata' => \theme_remui\utility::get_footer_data()
];

// for all partials
$templatecontext['navbarinverse'] = \theme_remui\toolbox::get_setting('navbarinverse');
$templatecontext['sitecolor']  = \theme_remui\toolbox::get_setting('sitecolor');

echo $OUTPUT->render_from_template('theme_remui/secure', $templatecontext);

