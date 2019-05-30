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
 * Edwiser RemUI AJAX handler
 *
 * @package   theme_remui
 * @copyright WisdmLabs
 */

use theme_remui\controller\remui_kernel;
use theme_remui\controller\remui_router;

// define ajax script based on action value
$action    = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRIPPED);
if (!isset($action) || empty($action)) {
    return;
}

// only make ajax true if action has ajax in name
$actionpattern = '/_ajax$/i';
if (preg_match($actionpattern, $action)) {
    define('AJAX_SCRIPT', true);
    define('NO_DEBUG_DISPLAY', true);
}

// include Moodle config
// This code is to run or include file at developer end
// It is because we use symlink for theme from local_gitrepo
if (!@include_once(__DIR__.'/../../config.php')) {
    include_once('/var/www/remui.local/html/v35/config.php');
}

$systemcontext = context_system::instance();

$contextid = optional_param('contextid', $systemcontext->id, PARAM_INT);

list($context, $course, $cm) = get_context_info_array($contextid);

$nologinactions = ['get_loginstatus', 'read_page']; // Actions which do not require login checks.
if (!in_array($action, $nologinactions)) {
    $courseactions = ['get_media', 'get_page'];
    if (in_array($action, $courseactions)) {
        require_login($course, false, $cm, false, true);
    } else {
        require_login();
    }
}

/** @var $PAGE moodle_page */
$PAGE->set_context($context);
if ($course !== null) {
    $PAGE->set_course($course);
}
$PAGE->set_url('/theme/remui/request_handler.php', array('action' => $action, 'contextid' => $context->id));

if ($cm !== null) {
    $PAGE->set_cm($cm);
}

$router = new remui_router();

// Add controllers automatically.
$controllerdir = __DIR__.'/classes/controller';
$contfiles = scandir($controllerdir);
foreach ($contfiles as $contfile) {
    // include controllers
    $pattern = '/_controller.php$/i';
    if (preg_match($pattern, $contfile)) {
        $classname = '\\theme_remui\\controller\\'.str_ireplace('.php', '', $contfile);
        if (class_exists($classname)) {
            $rc = new ReflectionClass($classname);
            if ($rc->isSubclassOf('\\theme_remui\\controller\\controller_abstract')) {
                $router->add_controller(new $classname());
            }
        }
    }
}

$kernel = new remui_kernel($router);
$kernel->handle($action);
