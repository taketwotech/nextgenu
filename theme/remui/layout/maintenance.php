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
 * A maintenance layout for the Edwiser RemUI theme.
 *
 * @package   theme_remui
 * @copyright WisdmLabs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$extraclasses = [];
$extraclasses[] = 'page-maintenance layout-full';
$bodyattributes = $OUTPUT->body_attributes($extraclasses);

$templatecontext = [
    // We cannot pass the context to format_string, this layout can be used during
    // installation. At that stage database tables do not exist yet.
    'sitename' => format_string($SITE->shortname, true, ["escape" => false]),
    'output' => $OUTPUT,
    'bodyattributes' => $bodyattributes,
    'footerdata' => \theme_remui\utility::get_footer_data(1)
];

echo $OUTPUT->render_from_template('theme_remui/maintenance', $templatecontext);
