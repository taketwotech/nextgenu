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
 * Trait - format section
 * Code that is shared between course_format_topic_renderer.php and course_format_weeks_renderer.php
 * Used for section outputs.
 *
 * @package   theme_remui
 * @copyright Copyright (c) 2016 WisdmLabs. (http://www.wisdmlabs.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\output;

use html_writer;
use moodle_page;
use context_course;
use completion_info;
use moodle_url;
use stdClass;
use course_in_list;

require_once($CFG->libdir. '/coursecatlib.php');

trait format_section_trait
{
    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course)
    {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course)
    {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section, false));
    }

    /**
     * Output the html for a multiple section page
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused)
    {
        global $PAGE, $OUTPUT;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        $context = context_course::instance($course->id);

        echo $this->output->heading($this->page_title(), 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $this->start_section_list();

        $numsections = course_get_format($course)->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                // 0-section is displayed a little different then the others
                if ($thissection->summary || !empty($modinfo->sections[0]) || $PAGE->user_is_editing()) {
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $this->courserenderer->course_first_section_cm_list($course, $thissection, 0);
                    echo $this->courserenderer->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $numsections) {
                // activities inside this section are 'orphaned', this section will be printed as 'stealth' below
                continue;
            }
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display.
            $showsection = $thissection->uservisible ||
                    ($thissection->visible && !$thissection->available &&
                    !empty($thissection->availableinfo));
            if (!$showsection) {
                // If the hiddensections option is set to 'show hidden sections in collapsed
                // form', then display the hidden section message - UNLESS the section is
                // hidden by the availability system, which is set to hide the reason.
                if (!$course->hiddensections && $thissection->available) {
                    echo $this->section_hidden($section, $course->id);
                }

                continue;
            }

            if (!$PAGE->user_is_editing() && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    $cm_list = $this->courserenderer->course_section_cm_list($course, $thissection, 0);

                    // show activity list panel only if there are activities
                    if (!empty($cm_list)) {
                        // echo '<div class="panel activity-list">
                        //     <div class="panel-heading" role="tab">
                        //         <a class="panel-title px-30 pt-0" data-toggle="collapse" href="#sectionwrapper-'.$section.'" aria-expanded="true" aria-controls="sectionwrapper-'.$section.'">
                        //             '.get_string('sectionactivities', 'theme_remui').'<i class="fa-angle-up float-right"></i>
                        //         </a>
                        //     </div>

                        //     <div class="panel-collapse collapse show" id="sectionwrapper-'.$section.'" aria-labelledby="sectionwrapper-'.$section.'" role="tabpanel" aria-expanded="true">
                        //       <div class="panel-body p-0">';

                        echo '<div class="panel activity-list">
                                  <div class="panel-collapse collapse show" id="sectionwrapper-'.$section.'" aria-labelledby="sectionwrapper-'.$section.'" role="tabpanel" aria-expanded="true">
                              <div class="panel-body p-0">';

                        echo $cm_list;

                        echo '</div>
                            </div>
                        </div>';
                    }

                    echo $this->courserenderer->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($PAGE->user_is_editing() && has_capability('moodle/course:update', $context)) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections || empty($modinfo->sections[$section])) {
                    // this is not stealth section or it is empty
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->courserenderer->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $this->end_section_list();

            echo $this->change_number_sections($course, 0);
        } else {
            echo $this->end_section_list();
        }
    }

    /**
     * Output the html for a single section page .
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection)
    {
        global $PAGE, $OUTPUT;

        $modinfo = get_fast_modinfo($course);
        $course = course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $this->start_section_list();
                echo $this->section_hidden($displaysection, $course->id);
                echo $this->end_section_list();
            }
            // Can't view this section.
            return;
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary || !empty($modinfo->sections[0]) || $PAGE->user_is_editing()) {
            echo $this->start_section_list();
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->courserenderer->course_first_section_cm_list($course, $thissection, $displaysection);
            echo $this->courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $this->end_section_list();
        }

        // Start single-section div
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle mb-20', 'style' => 'width: 90%;margin-left:auto;margin-right: auto;'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectiontitle .= '<div class="clearfix"></div>';
        // Title attributes
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        //$sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections..
        echo $this->start_section_list();

        echo $this->section_header($thissection, $course, true, $displaysection);
        // Show completion help icon.
        $completioninfo = new completion_info($course);
        echo $completioninfo->display_help_icon();

        $cm_list = $this->courserenderer->course_section_cm_list($course, $thissection, 0);
        // show activity list panel only if there are activities
        if (!empty($cm_list)) {
            echo '<div class="panel activity-list">
                <div class="panel-heading" role="tab">
                    <a class="panel-title px-30 pt-0" data-toggle="collapse" href="#sectionwrapper-'.$displaysection.'" aria-expanded="true" aria-controls="sectionwrapper-'.$displaysection.'">
                        '.get_string('sectionactivities', 'theme_remui').'<i class="fa-angle-up float-right"></i>
                    </a>
                </div>

                <div class="panel-collapse collapse show" id="sectionwrapper-'.$displaysection.'" aria-labelledby="sectionwrapper-'.$displaysection.'" role="tabpanel" aria-expanded="true">
                  <div class="panel-body p-0">';

            echo $cm_list;

            echo '</div>
                </div>
            </div>';
        }

        echo $this->courserenderer->course_section_add_cm_control($course, $displaysection, 0);

        echo $this->section_footer();
        echo $this->end_section_list();

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom', 'style' => 'width: 90%;margin-left:auto;margin-right: auto;'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= '<div class="clearfix"></div>';
        $sectionbottomnav .= html_writer::tag(
            'div',
            $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align')
        );
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null)
    {
        if ($section->section == 0) {
            $o = $this->first_section_header($section, $course, $onsectionpage, $sectionreturn = null);
        } else {
            $o = $this->regular_section_header($section, $course, $onsectionpage, $sectionreturn = null);
        }

        return $o;
    }

    /**
     * Extract first image from html
     *
     * @param string $html (must be well formed)
     * @return array | bool (false)
     */
    public static function extract_first_image($html)
    {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true); // Required for HTML5.
        $doc->loadHTML($html);
        libxml_clear_errors(); // Required for HTML5.
        $imagetags = $doc->getElementsByTagName('img');
        if ($imagetags->item(0)) {
            $src = $imagetags->item(0)->getAttribute('src');
            $alt = $imagetags->item(0)->getAttribute('alt');
            return array('src' => $src, 'alt' => $alt);
        } else {
            return false;
        }
    }

    protected function first_section_header($section, $course, $onsectionpage, $sectionreturn = null)
    {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = ' mb-25';
        $coverimage = \theme_remui\utility::get_course_image($course);
        // Title with completion help icon.
        $completioninfo = new completion_info($course);

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        // $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $leftside = html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $rightside = html_writer::tag('div', $rightcontent, array('class' => 'right side'));

        echo $completioninfo->display_help_icon();
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'first-section-li section main clearfix'.$sectionstyle, 'role'=>'region',
            'aria-label'=> get_section_name($course, $section)));

        $imgpos = get_config('theme_remui', 'coursedescimage');

        // course cover image
        $coursesummary = $this->format_summary_text($section);
        $imagesrc = '';
        $imagealt = '';
        if ($imgpos) {
            if (!empty($coursesummary)) {
                $coverimage = $this->extract_first_image($coursesummary);
                if ($coverimage) {
                    $imagesrc = $coverimage['src'];
                    $imagealt = $coverimage['alt'];
                }
                $coursesummary = preg_replace('/<p[^>]*><\\/p[^>]*>/', '', preg_replace('/<img[\S\s]*?>/i', '', $coursesummary, 1), 1);
            }
        } else {
            $imagesrc = $coverimage;
        }
        // To remove the first image from the html as we are adding it(first image) to the media-left class

        $o .= "<div class='p-10 course-cover-image pb-30' style='background-image: linear-gradient(to right, rgba(14, 35, 53, 0.68), rgba(14, 35, 53, 0.68)), url(".$imagesrc.");' alt='".$imagealt."''>
        $leftside
        $rightside
        <div class='text-white'>
            <div class='page-title text-center my-15'>".get_section_name($course, $section)."</div>
            <div class='font-size-14 summary text-justify' style='max-width: 800px; margin: 0 auto;'>$coursesummary</div>
        </div>
        </div>";

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }

        $o .= $this->section_availability($section);

        return $o;
    }

    protected function regular_section_header($section, $course, $onsectionpage, $sectionreturn = null)
    {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle .= ' current';
            }
        }

        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'card section main clearfix'.$sectionstyle, 'role'=>'region',
            'style' => 'width: 90%; margin-left: auto; margin-right:auto;',
            'aria-label'=> get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $leftside = html_writer::tag('div', $leftcontent, array('class' => 'left side float-left'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $rightside = html_writer::tag('div', $rightcontent, array('class' => 'right side float-right'));

        $o .= html_writer::start_tag('div', array('class' => 'content card-block p-30', 'style' => 'clear:both;'));

        $o .= '<div class="row d-block">';
        $o .= $rightside;
        $o .= $leftside;
        $o .= '</div>';

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        // $classes = '';
        // if ($hasnamenotsecpg || $hasnamesecpg) {
        //     $classes = '';
        // }

        $classes = ' card-title';
        //$sectionname = html_writer::tag('span', $this->section_title($section, $course));
        // $o.= $this->output->heading($this->section_title($section, $course), 4, 'sectionname' . $classes);
        $cm_list = $this->courserenderer->course_section_cm_list($course, $section, 0);
        $temp  = '<a class="p-0" data-toggle="collapse" href="#sectionwrapper-'.$section->section.'" aria-expanded="true" aria-controls="sectionwrapper-'.$section->section.'">'.get_section_name($section->course, $section).'</a>';
        if (!empty($cm_list)) {
            $temp  = '<a class="panel-title p-0" data-toggle="collapse" href="#sectionwrapper-'.$section->section.'" aria-expanded="true" aria-controls="sectionwrapper-'.$section->section.'">'.get_section_name($section->course, $section).'<i class="fa-angle-up float-right"></i></a>';
        }


        $o .= $this->output->heading($temp, 4, 'sectionname' . $classes);

        $o .= $this->section_availability($section);

        $o .= html_writer::start_tag('div', array('class' => 'summary card-text'));
        $o .= $this->format_summary_text($section);
        $o .= html_writer::end_tag('div');

        $o .= '</div>';

        $o .= '<div class="card-footer p-0 card-footer-transparent text-muted">';

        return $o;
    }

    /**
     * Displays availability information for the section (hidden, not available unles, etc.)
     *
     * @param section_info $section
     * @return string
     */
    public function section_availability($section)
    {
        $context = context_course::instance($section->course);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);
        return html_writer::div($this->section_availability_message($section, $canviewhidden), 'section_availability badge badge-pill badge-info mb-10');
    }

    /**
     * Generate a summary of a section for display on the 'coruse index page'
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods)
    {
        $classattr = 'card section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } elseif (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => $classattr, 'role'=>'region', 'aria-label'=> $title,
            'style' => 'width: 90%; margin-left: auto; margin-right:auto;'));

        $o .= html_writer::tag('div', '', array('class' => 'left side  pt-5 px-20'));
        $o .= html_writer::tag('div', '', array('class' => 'right side pt-5 px-20'));
        $o .= html_writer::start_tag('div', array('class' => 'content card-block p-30', 'style' => 'clear:both;'));

        if ($section->uservisible) {
            $title = html_writer::tag(
                'a',
                $title,
                array('href' => course_get_url($course, $section->section), 'class' => $linkclasses)
            );
        }
        $o .= $this->output->heading($title, 4, 'card-title section-title');

        $o.= html_writer::start_tag('div', array('class' => 'summarytext card-text'));
        $o.= $this->format_summary_text($section);
        $o.= html_writer::end_tag('div');

        $o .= html_writer::end_tag('div'); // close content div

        $activity_summary = $this->section_activity_summary($section, $course, null);
        if ($activity_summary) {
            $o .= '<div class="card-footer card-footer-transparent card-footer-bordered text-muted">';
            $o .= $activity_summary;
            $o .= html_writer::end_tag('div');
        }

        $o .= $this->section_availability($section);

        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate the display of the footer part of a section
     *
     * @return string HTML to output.
     */
    protected function section_footer()
    {
        $o = html_writer::end_tag('div');
        $o.= html_writer::end_tag('li');

        return $o;
    }
}
