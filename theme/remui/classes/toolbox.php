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
 * This is built using the bootstrapbase template to allow for new theme's using
 * Moodle's new Bootstrap theme engine
 *
 * @package   theme_remui
 * @copyright Copyright (c) 2016 WisdmLabs. (http://www.wisdmlabs.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui;

class toolbox {

    protected $corerenderer = null;
    protected static $instance;

    private function __construct() {
    }

    public static function get_instance() {
        if (!is_object(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * Sets the core_renderer class instance so that when purging all caches and 'theme_xxx_process_css' etc.
     * the settings are correct.
     * @param class core_renderer $core Child object of core_renderer class.
     */
    static public function set_core_renderer($core) {
        $us = self::get_instance();
        // Set only once from the initial calling lib.php process_css function so that subsequent parent calls do not override it.
        // Must happen before parents.
        if (null === $us->corerenderer) {
            $us->corerenderer = $core;
        }
    }
      /**
     * Finds the given setting in the theme from the themes' configuration object.
     * @param string $setting Setting name.
     * @param string $format false|'format_text'|'format_html'.
     * @param theme_config $theme null|theme_config object.
     * @return any false|value of setting.
     */
    static public function get_setting($setting, $format = false) {
        $us = self::check_corerenderer();

        $settingvalue = $us->get_setting($setting);

        global $CFG;
        require_once($CFG->dirroot . '/lib/weblib.php');
        if (empty($settingvalue)) {
            return false;
        } else if (!$format) {
            return $settingvalue;
        } else if ($format === 'format_text') {
            return format_text($settingvalue, FORMAT_PLAIN);
        } else if ($format === 'format_html') {
            return format_text($settingvalue, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
        } else if ($format === 'format_file_url') {
            return self::setting_file_url($setting, $setting);
        } else {
            return format_string($settingvalue);
        }
    }
    static public function setting_file_url($setting, $filearea) {
        $us = self::check_corerenderer();

        return $us->setting_file_url($setting, $filearea);
    }

    static public function image_url($imagename, $component) {
        $us = self::check_corerenderer();
        return $us->image_url($imagename, $component);
    }

    static private function check_corerenderer() {
        $us = self::get_instance();
        if (empty($us->corerenderer)) {
            // Use $OUTPUT unless is not a Remui or child core_renderer which can happen on theme switch.
            global $OUTPUT;
            if (property_exists($OUTPUT, 'remui')) {
                $us->corerenderer = $OUTPUT;
            } else {
                // Use $PAGE->theme->name as will be accurate than $CFG->theme when using URL theme changes.
                // Core 'allowthemechangeonurl' setting.
                global $PAGE;
                $corerenderer = null;
                try {
                    $corerenderer = $PAGE->get_renderer('theme_'.$PAGE->theme->name, 'core');
                } catch (\coding_exception $ce) {
                    // Specialised renderer may not exist in theme.  This is not a coding fault.  We just need to cope.
                    $corerenderer = null;
                }
                // Fallback check.
                if (($corerenderer != null) && (property_exists($corerenderer, 'remui'))) {
                    $us->corerenderer = $corerenderer;
                } else {
                    // Probably during theme switch, '$CFG->theme' will be accurrate.
                    global $CFG;
                    try {
                        $corerenderer = $PAGE->get_renderer('theme_'.$CFG->theme, 'core');
                    } catch (\coding_exception $ce) {
                        // Specialised renderer may not exist in theme.  This is not a coding fault.  We just need to cope.
                        $corerenderer = null;
                    }
                    if (($corerenderer != null) && (property_exists($corerenderer, 'remui'))) {
                        $us->corerenderer = $corerenderer;
                    } else {
                        // Last resort.  Hopefully will be fine on next page load for Child themes.
                        // However '***_process_css' in lib.php will be fine as it sets the correct renderer.
                        $us->corerenderer = $PAGE->get_renderer('theme_remui', 'core');
                    }
                }
            }
        }
        return $us->corerenderer;
    }

    static public function set_font($css, $fontname) {
        $fontfamilytag = '[[setting:fontfamily]]';
        $familyreplacement = $fontname;
        
        $css = str_replace($fontfamilytag, $familyreplacement, $css);        

        return $css;
    }

    static public function set_color($css, $themecolor, $tag, $defaultcolour) {
        if (!($themecolor)) {
            $replacement = $defaultcolour;
        } else {
            $replacement = $themecolor;
        }
        
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_customcss($css, $customcss) {
        $tag = '[[setting:customcss]]';
        $replacement = $customcss;
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_logo($css, $logo) {
        $tag = '[[setting:logo]]';
        if (!($logo)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\''.$logo.'\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_logoheight($css, $logoheight) {
        $tag = '[[setting:logoheight]]';
        if (!($logoheight)) {
            $replacement = '65px';
        } else {
            $replacement = $logoheight;
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

   

    // /**
    //  * States if the browser is not IE9 or less.
    //  */
    // static public function not_lte_ie9() {
    //     $properties = self::ie_properties();
    //     if (!is_array($properties)) {
    //         return true;
    //     }
    //     // We have properties, it is a version of IE, so is it greater than 9?
    //     return ($properties['version'] > 9.0);
    // }

    // /**
    //  * States if the browser is IE9 or less.
    //  */
    // static public function lte_ie9() {
    //     $properties = self::ie_properties();
    //     if (!is_array($properties)) {
    //         return false;
    //     }
    //     // We have properties, it is a version of IE, so is it greater than 9?
    //     return ($properties['version'] <= 9.0);
    // }

    // /**
    //  * States if the browser is IE by returning properties, otherwise false.
    //  */
    // static protected function ie_properties() {
    //     $properties = \core_useragent::check_ie_properties(); // In /lib/classes/useragent.php.
    //     if (!is_array($properties)) {
    //         return false;
    //     } else {
    //         return $properties;
    //     }
    // }
}
