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

namespace theme_remui\output;

use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use core_text;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use paging_bar;
use context_course;
use pix_icon;
use action_menu_filler;
use context_system;
use moodle_page;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_remui
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends remui_renderer
{

    /** @var custom_menu_item language The language menu if created */
    use core_renderer_toolbox;
    protected $language = null;
    protected $themeconfig;
    protected $left;
    protected $remui = null; // Used for determining if this is a Essential or child of renderer.

    /**
     * Constructor
     *
     * @param moodle_page $page the page we are doing output for.
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target)
    {
        parent::__construct($page, $target);
        $this->themeconfig = array(\theme_config::load('remui'));
    }

    /**
     * Outputs the opening section of a box.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes An array of other attributes to give the box.
     * @return string the HTML to output.
     */
    public function box_start($classes = 'generalbox', $id = null, $attributes = array())
    {
        if (is_array($classes)) {
            $classes = implode(' ', $classes);
        }
        return parent::box_start($classes . '', $id, $attributes);
    }

        /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header()
    {
        global $PAGE;

        $html = html_writer::start_tag('header', array('id' => 'page-header', 'class' => 'page-header'));

        $html .= $this->context_header();

        $html .= html_writer::tag('div', $this->course_header(), array('id' => 'course-header'));

        $html .= html_writer::end_tag('header');
        return $html;
    }

    // show license or update notice
    public function showLicenseNotice()
    {
        // get license data from license controller
        $lcontroller = new \theme_remui\controller\license_controller();
        $getlidatafromdb = $lcontroller->getDataFromDb();
        $l_alert = '';
        if (isloggedin() && !isguestuser()) {
            if ('available' != $getlidatafromdb) {
                $l_alert .= '<div class="alert alert-danger text-center license-nag bg-red-800 text-white">
                <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">×</button>';

                if (is_siteadmin()) {
                    $l_alert .= '<strong>'.get_string('licensenotactiveadmin', 'theme_remui').'</strong>';
                } else {
                    $l_alert .= get_string('licensenotactive', 'theme_remui');
                }

                $l_alert .= '</div>';
            } elseif ('available' == $getlidatafromdb) {
                $licensekeyactivate = \theme_remui\toolbox::get_setting('licensekeyactivate');

                if (isset($licensekeyactivate) && !empty($licensekeyactivate)) {
                    $l_alert .= '<div class="alert alert-success text-center license-nag bg-green-600 text-white ">
                        <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">×</button>';
                    $l_alert .= get_string('licensekeyactivated', 'theme_remui');
                    $l_alert .= '</div>';
                } else {
                    // show update notice if license is active and update is available
                    $available  = \theme_remui\utility::check_remui_update();
                    if (is_siteadmin() && $available == 'available') {
                        $l_alert .= '<div class="alert alert-info text-center license-nag update-nag bg-info text-white">
                                <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">×</button>';
                        $l_alert .= get_string('newupdatemessage', 'theme_remui');
                        $l_alert .= '</div>';
                    }
                }
            }

            return $l_alert;
        }
    }

    public function render_site_announcement()
    {
        $enableannouncement = \theme_remui\toolbox::get_setting('enableannouncement');
        $announcement = '';
        if ($enableannouncement) {
            $type = \theme_remui\toolbox::get_setting('announcementtype');
            $message = \theme_remui\toolbox::get_setting('announcementtext');

            $announcement .= "<div class='alert alert-{$type} dark text-center rounded-0'>";
            $announcement .= $message;
            $announcement .= "</div>";
        }

        return $announcement;
    }

    /**
     * Gets HTML for the page heading.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $tag The tag to encase the heading in. h1 by default.
     * @return string HTML.
     */
    public function page_heading($tag = 'h1')
    {
        return html_writer::tag($tag, $this->page->heading, array('class' => 'page-title'));
    }

    /**
     * The standard tags that should be included in the <head> tag
     * including a meta description for the front page
     *
     * @return string HTML fragment.
     */
    public function standard_head_html()
    {
        global $SITE, $PAGE;

        $output = parent::standard_head_html();
        if ($PAGE->pagelayout == 'frontpage') {
            $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
            if (!empty($summary)) {
                $output .= "<meta name=\"description\" content=\"$summary\" />\n";
            }
        }

        // Get the theme font from setting
        $fontname = ucwords(\theme_remui\toolbox::get_setting('fontname', 'Roboto'));
        if (empty($fontname)) {
            $fontname = 'Open Sans';
        }
        $output .= "<link href='https://fonts.googleapis.com/css?family=$fontname:300,400,500,600,700,300italic' rel='stylesheet' type='text/css'>";

        // add google analytics code
        $ga_js_async = "<!-- Google Analytics --><script>window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;ga('create', 'UA-CODE-X', 'auto');ga('send', 'pageview');</script><script async src='https://www.google-analytics.com/analytics.js'></script><!-- End Google Analytics -->";

        $ga_tracking_code = trim(\theme_remui\toolbox::get_setting('googleanalytics'));
        if (!empty($ga_tracking_code)) {
            $output .= str_replace("UA-CODE-X", $ga_tracking_code, $ga_js_async);
        }

        return $output;
    }

    /**
     * Returns the url of the custom favicon.
     */
    public function favicon()
    {
        $favicon = \theme_remui\toolbox::setting_file_url('faviconurl', 'faviconurl');
        if (empty($favicon)) {
            return \theme_remui\toolbox::image_url('favicon', 'theme');
        } else {
            return $favicon;
        }
    }

    /*
     * This renders the navbar.
     * Uses bootstrap compatible html.
     */
    public function navbar()
    {
        return $this->render_from_template('core/navbar', $this->page->navbar);
    }

    /**
     * Returns a search box icon in nav bar.
     *
     * @param  string $id     The search box wrapper div id, defaults to an autogenerated one.
     * @return string         HTML with the search form icon.
     */
    public function search_box_icon($id = false)
    {
        global $CFG;

        // Accessing $CFG directly as using \core_search::is_global_search_enabled would
        // result in an extra included file for each site, even the ones where global search
        // is disabled.
        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', context_system::instance())) {
            return '';
        }

        // JS to animate the form.
        //   $this->page->requires->js_call_amd('core/search-input', 'init', array($id));

        if ($id == false) {
            $id = uniqid();
        } else {
            // Needs to be cleaned, we use it for the input id.
            $id = clean_param($id, PARAM_ALPHANUMEXT);
        }

        $searchicon = html_writer::link('#', $this->pix_icon('a/search', get_string('search', 'search')), array('role' => 'button', 'class' => 'nav-link', 'data-target'=>'#site-navbar-search', 'data-toggle' => 'collapse'));

        return html_writer::tag('li', $searchicon, array('class' => 'nav-item hidden-float', 'id' => $id));
    }

    // search box icon for collapsed header/mobile view
    public function search_box_icon_collapsed($id = false)
    {
        global $CFG;

        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', context_system::instance())) {
            return '';
        }

        $searchicon = html_writer::tag('button', html_writer::tag('span', get_string('togglesearch', 'theme_remui'), array('class' => 'sr-only')).$this->pix_icon('a/search', get_string('search', 'search')), array('class' => 'navbar-toggler collapsed mr-0', 'data-target'=>'#site-navbar-search', 'data-toggle' => 'collapse'));

        return $searchicon;
    }

    /**
     * Returns a search box.
     *
     * @param  string $id     The search box wrapper div id, defaults to an autogenerated one.
     * @return string         HTML with the search form hidden by default.
     */
    public function search_box($id = false)
    {
        global $CFG;

        // Accessing $CFG directly as using \core_search::is_global_search_enabled would
        // result in an extra included file for each site, even the ones where global search
        // is disabled.
        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', context_system::instance())) {
            return '';
        }

        if ($id == false) {
            $id = uniqid();
        } else {
            // Needs to be cleaned, we use it for the input id.
            $id = clean_param($id, PARAM_ALPHANUMEXT);
        }

        $formattrs = array('role' => 'search', 'class' => '', 'action' => $CFG->wwwroot . '/search/index.php');
        $inputattrs = array('type' => 'text', 'name' => 'q', 'placeholder' => get_string('search', 'search'),
             'size' => 13, 'tabindex' => -1, 'id' => 'id_q_' . $id, 'class' => 'form-control');

        $formcontent = html_writer::tag(
            'div',
            html_writer::start_tag('div', array('class' => 'input-search')).
            $this->pix_icon('a/search', '', '', array('class' => 'input-search-icon')).
            html_writer::tag('input', '', $inputattrs).
            html_writer::tag('input', '', array('type' => 'submit', 'class' => 'hidden')).
            html_writer::tag('button', '', array('class' => 'input-search-close icon fa-times', 'data-target' => '#site-navbar-search', 'data-toggle' => 'collapse', 'aria-label' => 'Close')).
            html_writer::end_tag('div'),
            array('for' => 'id_q_' . $id, 'class' => 'form-group')
        );

        $form = html_writer::tag('form', $formcontent, $formattrs);

        $contentwrapper = html_writer::tag(
            'div',
            $form,
            array('id' => 'site-navbar-search', 'class' => 'collapse navbar-search-overlap')
        );

        return $contentwrapper;
    }

    /**
     * We don't like these...
     *
     */
    public function edit_button(moodle_url $url)
    {
        return '';
    }

    /**
      * Renders the header bar.
      *
      * @param context_header $contextheader Header bar object.
      * @return string HTML for the header bar.
      */
    protected function render_context_header(\context_header $contextheader)
    {
        global $PAGE;

        // All the html stuff goes here.
        $html = '';

        // get page heading button
        // moved from full_header for proper remui html structure
        $pageheadingbutton = $this->page_heading_button();

        // Headings
        if (!isset($contextheader->heading)) {
            $headings = $this->heading($this->page->heading, $contextheader->headinglevel, 'page-title');
        } else {
            $headings = $this->heading($contextheader->heading, $contextheader->headinglevel, 'page-title');
        }

        $html .= $headings;

        if (empty($PAGE->layout_options['nonavbar'])) {
            $html .= $this->navbar();
        }

        // page header actions
        $html .= html_writer::start_div('page-header-actions');
        $html .= $pageheadingbutton;
        $html .= html_writer::end_div();


        // header settings menu
        $classes = array();
        $settings_menu = $this->context_header_settings_menu();
        if (!empty($settings_menu)) {
            $classes = array();
        }
        $html .= html_writer::start_div('row additional-actions');
        $html .= html_writer::start_div('col-12');
        // Additional context header buttons.
        if (isset($contextheader->additionalbuttons)) {
            foreach ($contextheader->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'togglecontact') {
                        \core_message\helper::togglecontact_requirejs();
                    }

                    $image = $this->pix_icon($button['formattedimage'], $button['title'], 'moodle', array(
                        'class' => 'iconsmall',
                        'role' => 'presentation'
                    ));

                    $image = html_writer::span($image.'&nbsp;&nbsp;'.$button['title']);
                } else {
                    $image = html_writer::empty_tag('img', array(
                        'src' => $button['formattedimage'],
                        'role' => 'presentation'
                    ));
                }

                // add additional class
                $button['linkattributes']['class'] .= ' btn btn-inverse mr-5';
                $html .= html_writer::link($button['url'], $image, $button['linkattributes']);
            }
        }
        $html .= $this->context_header_settings_menu();

        $html .= html_writer::end_div();
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Get the compact logo URL.
     *
     * @return string
     */
    public function get_compact_logo_url($maxwidth = 100, $maxheight = 100)
    {
        return parent::get_compact_logo_url(null, 70);
    }

    /**
     * Whether we should display the main logo.
     *
     * @return bool
     */
    public function should_display_main_logo($headinglevel = 1)
    {
        global $PAGE;

        // Only render the logo if we're on the front page or login page and the we have a logo.
        $logo = $this->get_logo_url();
        if ($headinglevel == 1 && !empty($logo)) {
            if ($PAGE->pagelayout == 'frontpage' || $PAGE->pagelayout == 'login') {
                return true;
            }
        }

        return false;
    }
    /**
     * Whether we should display the logo in the navbar.
     *
     * We will when there are no main logos, and we have compact logo.
     *
     * @return bool
     */
    public function should_display_logo()
    {
        global $SITE;

        $logoorsitename = \theme_remui\toolbox::get_setting('logoorsitename');
        $context = array('islogo' => false, 'issitename' => false, 'isiconsitename' => false);
        $checklogo = \theme_remui\toolbox::setting_file_url('logo', 'logo');
        $checklogomini = \theme_remui\toolbox::setting_file_url('logomini', 'logomini');

        if (!empty($checklogo)) {
            $logo = $checklogo;
        } else {
            $logo = \theme_remui\toolbox::image_url('logo', 'theme');
        }

        if (!empty($checklogomini)) {
            $logomini = $checklogomini;
        } else {
            $logomini = \theme_remui\toolbox::image_url('logomini', 'theme');
        }

        if ($logoorsitename == 'logo') {
            $context['islogo'] = true;
            $context['logourl'] = $logo;
            $context['logominiurl'] = $logomini;
        } else {
            $context['isiconsitename'] = true;
            $context['siteicon'] = \theme_remui\toolbox::get_setting('siteicon');
            $context['sitename'] = format_string($SITE->shortname);
        }

        return $context;
    }

    /**
     * Whether we should display the logo in the navbar.
     *
     * We will when there are no main logos, and we have compact logo.
     *
     * @return bool
     */
    public function get_logo_html()
    {
        global $SITE;

        $logoorsitename = \theme_remui\toolbox::get_setting('logoorsitename');
        $siteicon = \theme_remui\toolbox::get_setting('siteicon');
        $checklogo = \theme_remui\toolbox::setting_file_url('logo', 'logo');
        $logohtml = '';
        if (!empty($checklogo)) {
            $logo = $checklogo;
        } else {
            $logo = \theme_remui\toolbox::image_url('logo', 'theme');
        }

        $checklogomini = \theme_remui\toolbox::setting_file_url('logomini', 'logomini');
        if (!empty($checklogomini)) {
            $logomini = $checklogomini;
        } else {
            $logomini = \theme_remui\toolbox::image_url('logomini', 'theme');
        }

        if ($logoorsitename == 'logo') {
            ?>
            $logohtml .= "<a href='$CFG->wwwroot;' class='logo'>
            <span class='navbar-brand-logo' style='background-image: url($logomini);background-position: center; height:50px; background-size: contain; background-repeat: no-repeat;'></span>
            <span class='navbar-brand-logo-mini' style='background-image: url($logomini);
                    background-position: center; height:50px; background-size: contain; background-repeat: no-repeat;'></span>
            </a>";
        <?php
        } elseif ($logoorsitename == 'sitename') {
            ?>
            <a class="logo" href="<?php echo $CFG->wwwroot; ?>">
              <span class="logo-mini"><i class="fa fa-<?php echo $siteicon; ?>"></i></span>
              <span class="logo-lg">
                <?php echo format_string($SITE->shortname); ?>
              </span>
            </a>
        <?php
        } else {
            ?>
            <a class="logo" href="<?php echo $CFG->wwwroot; ?>">
              <span class="logo-mini"><i class="fa fa-<?php echo $siteicon; ?>"></i></span>
              <span class="logo-lg">
                  <i class="fa fa-<?php echo $siteicon; ?>"></i>
                    <?php echo format_string($SITE->shortname); ?>
              </span>
            </a>
        <?php
        }

        // $logo = $this->get_compact_logo_url();
        // return !empty($logo) && !$this->should_display_main_logo();

        echo $logohtml;
    }

    /**
     * Returns lang menu or '', this method also checks forcing of languages in courses.
     *
     * This function calls {@link core_renderer::render_single_select()} to actually display the language menu.
     *
     * @return string The lang menu HTML or empty string
     */
    public function lang_menu()
    {
        global $CFG;
        if (empty($CFG->langmenu)) {
            return '';
        }

        if ($this->page->course != SITEID and !empty($this->page->course->lang)) {
            // do not show lang menu if language forced
            return '';
        }

        $currlang = current_language();
        $langs = get_string_manager()->get_list_of_translations();

        if (count($langs) < 2) {
            return '';
        }

        $s = new \single_select($this->page->url, 'lang', $langs, $currlang, null);
        $s->label = get_accesshide(get_string('language'));
        $s->class = 'langmenu';
        return $this->render($s);
    }

    /*
     * Overriding the custom_menu function ensures the custom menu is
     * always shown, even if no menu items are configured in the global
     * theme settings page.
     */
    public function custom_menu($custommenuitems = '')
    {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * We want to show the custom menus as a list of links in the footer on small screens.
     * Just return the menu object exported so we can render it differently.
     */
    public function custom_menu_flat()
    {
        global $CFG;
        $custommenuitems = '';

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $custommenu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        return $custommenu->export_for_template($this);
    }

    /*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
     */
    protected function render_custom_menu(custom_menu $menu)
    {
        global $CFG;

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $menu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

        return $content;
    }

    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null)
    {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');
        require_once($CFG->dirroot . '/lib/moodlelib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = array('class'=> 'usermenu nav-item dropdown user-menu login-menu');
        if (!$withlinks) {
            $usermenuclasses = array('class'=> ' nav-item dropdown user-menu login-menu withoutlinks');
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $login_dropdown = \theme_remui\toolbox::get_setting('navlogin_popup');
        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        $forgotpasswordurl = new moodle_url('/login/forgot_password.php');
        $loginurl_datatoggle = '';
        if ($login_dropdown) {
            $loginurl = '#';
            $loginurl_datatoggle = 'data-toggle="dropdown"';
        }
        // sign in popup
        $signinformhtml = '<ul class="dropdown-menu w-350 p-15" role="menu">
                    <form class="mb-0" action="'.get_login_url().'" method="post" id="login">
                        <div class="form-group">
                            <label for="username" class="sr-only">'.get_string('username').'</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="'.get_string('username').'">
                        </div>

                        <div class="form-group">
                            <label for="password" class="sr-only">'.get_string('password').'</label>
                            <input type="password" name="password" id="password" value="" class="form-control"placeholder='.get_string('password').'>
                        </div>

                        <div class="form-group clearfix">
                            <div class="checkbox-custom checkbox-inline checkbox-primary float-left rememberpass">
                                <input type="checkbox" id="rememberusername" name="rememberusername" value="1" />
                                <label for="rememberusername">'.get_string('rememberusername', 'admin').'</label>
                            </div>
                            <a class="float-right" href="'.$forgotpasswordurl.'">'.get_string("forgotpassword", "theme_remui").'</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" id="loginbtn">'.get_string('login').'</button>
                    </form>';

        // $signinformhtml .= '</ul>';

        $authsequence = get_enabled_auth_plugins(true); // Get all auths, in sequence.

        $potentialidps = array();
        foreach ($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $potentialidps = array_merge($potentialidps, $authplugin->loginpage_idp_list($this->page->url->out(false)));
        }
        if (!empty($potentialidps)) {
            $signinformhtml .= '<div class="potentialidp my-10 text-center">';
            $signinformhtml .= '<label class="w-full m-5">Log in using your account on</label>';
            $signinformhtml .= '<div class="button-group ">';
            foreach ($potentialidps as $idp) {
                $signinformhtml .= '<button type="button" class="btn btn-icon m-5">';
                $signinformhtml .= '<a href="' . $idp['url']->out() . '" title="' . s($idp['name']) . ' Login">';
                if (!empty($idp['iconurl'])) {
                    $signinformhtml .= '<img src="' . s($idp['iconurl']) . '" width="24" height="24" />';
                }
                $signinformhtml .= '</a>';
                $signinformhtml .= '</button>';
            }
            $signinformhtml .= '</div>';
            $signinformhtml .= '</div>';
        }
        $signinformhtml .= '</ul>';


        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            //$returnstr = get_string('loggedinnot', 'moodle');
            $returnstr = '';
            if (!$loginpage) {
                $returnstr = '<a href="'.$loginurl.'" class="nav-link" '.$loginurl_datatoggle.' data-animation="scale-up">
                <i class="icon fa-user"></i>&nbsp;'.get_string('login').'</a>';

                if ($login_dropdown) {
                    $returnstr  .= $signinformhtml;
                }
            }

            return html_writer::tag('li', $returnstr, $usermenuclasses);
        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            //$returnstr = get_string('loggedinasguest');
            $returnstr = '';
            if (!$loginpage && $withlinks) {
                $returnstr = '<a href="'.$loginurl.'" class="nav-link" '.$loginurl_datatoggle.' data-animation="scale-up">
                <i class="icon fa-user"></i>&nbsp;'.get_string('login').'</a>';

                if ($login_dropdown) {
                    $returnstr  .= $signinformhtml;
                }
            }

            //return html_writer::tag('li', '<span class="text-white" style="line-height:66px;">'.get_string('loggedinasguest').'</span>', array('class' => 'nav-item'))
            return html_writer::tag('li', $returnstr, $usermenuclasses);
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);

        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                ' ('. $opts->metadata['rolename'] .')',
                'meta text-uppercase font-size-12 role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider
        $divider = '<div class="dropdown-divider" role="presentation"></div>';

        $usermenu = '';
        $usermenu .= '<a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false" data-animation="scale-up" role="button">
            <span class="username">'.$usertextcontents.'</span>
            <span class="avatar avatar-online current">
            '.$opts->metadata['useravatar'].'
            <i></i>
            </span>
        </a>';

        $usermenu .= '<div class="dropdown-menu" role="menu">';
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {
                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $usermenu .= $divider;
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, $value->title, null, array('class' => 'iconsmall'));
                        } elseif (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                $value->imgsrc,
                                $value->title,
                                array('class' => 'iconsmall')
                            ) . $value->title;
                        }

                        $icon = $this->pix_icon($pix->pix, '', 'moodle', $pix->attributes);
                        $usermenu .= '<a class="dropdown-item" href="'.$value->url.'" role="menuitem">'.$icon.$value->title.'</a>';
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $usermenu .= $divider;
                }
            }
        }
        $usermenu .= '</div>';

        return html_writer::tag('li', $usermenu, $usermenuclasses);
    }

    /**
     * This code renders the navbar button to control the display of the custom menu
     * on smaller screens.
     *
     * Do not display the button if the menu is empty.
     *
     * @return string HTML fragment
     */
    public function navbar_button()
    {
        global $CFG;

        if (empty($CFG->custommenuitems) && $this->lang_menu() == '') {
            return '';
        }

        $iconbar = html_writer::tag('span', '', array('class' => 'icon-bar'));
        $button = html_writer::tag('a', $iconbar . "\n" . $iconbar. "\n" . $iconbar, array(
            'class'       => 'btn btn-navbar',
            'data-toggle' => 'collapse',
            'data-target' => '.nav-collapse'
        ));
        return $button;
    }

    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(tabtree $tabtree)
    {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $data = $tabtree->export_for_template($this);
        return $this->render_from_template('core/tabtree', $data);
    }

    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from {@link core_renderer::render_tabtree()}
     * and also it calls itself when printing the $tabobject subtree recursively.
     *
     * @param tabobject $tabobject
     * @return string HTML fragment
     */
    protected function render_tabobject(tabobject $tab)
    {
        throw new coding_exception('Tab objects should not be directly rendered.');
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region)
    {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = $bc->blockinstanceid;
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
        $context->type = $bc->attributes['data-block'];
        $context->title = $bc->title;
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->hascontrols = !empty($bc->controls);
        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        return $this->render_from_template('core/block', $context);
    }

    /**
     * Returns the CSS classes to apply to the body tag.
     *
     * @since Moodle 2.5.1 2.6
     * @param array $additionalclasses Any additional classes to apply.
     * @return string
     */
    public function body_css_classes(array $additionalclasses = array())
    {
        return $this->page->bodyclasses . ' ' . implode(' ', $additionalclasses);
    }

    /**
     * Renders preferences groups.
     *
     * @param  preferences_groups $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_groups(preferences_groups $renderable)
    {
        return $this->render_from_template('core/preferences_groups', $renderable);
    }

    /**
     * Renders an action menu component.
     *
     * @param action_menu $menu
     * @return string HTML
     */
    public function render_action_menu(action_menu $menu)
    {

        // We don't want the class icon there!
        foreach ($menu->get_secondary_actions() as $action) {
            if ($action instanceof \action_menu_link && $action->has_class('icon')) {
                $action->attributes['class'] = preg_replace('/(^|\s+)icon(\s+|$)/i', '', $action->attributes['class']);
            }
        }

        if ($menu->is_empty()) {
            return '';
        }
        $context = $menu->export_for_template($this);

        return $this->render_from_template('core/action_menu', $context);
    }

    /**
     * Implementation of user image rendering.
     *
     * @param help_icon $helpicon A help icon instance
     * @return string HTML fragment
     */
    protected function render_help_icon(help_icon $helpicon)
    {
        $context = $helpicon->export_for_template($this);
        return $this->render_from_template('core/help_icon', $context);
    }

    /**
     * Renders a single button widget.
     *
     * This will return HTML to display a form containing a single button.
     *
     * @param single_button $button
     * @return string HTML fragment
     */
    protected function render_single_button(single_button $button)
    {
        return $this->render_from_template('core/single_button', $button->export_for_template($this));
    }

    /**
     * Renders a paging bar.
     *
     * @param paging_bar $pagingbar The object.
     * @return string HTML
     */
    protected function render_paging_bar(paging_bar $pagingbar)
    {
        // Any more than 10 is not usable and causes wierd wrapping of the pagination in this theme.
        $pagingbar->maxdisplay = 5;
        return $this->render_from_template('core/paging_bar', $pagingbar->export_for_template($this));
    }

    /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form)
    {
        global $SITE;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]);

        $context->loginpage_context = $this->should_display_logo();
        $context->loginsocial_context = \theme_remui\utility::get_footer_data(1);
        $context->logopos = get_config('theme_remui', 'brandlogopos');
        $sitetext = get_config('theme_remui', 'brandlogotext');
        if ($sitetext != '') {
            $context->sitedesc = $sitetext;
        }
        return $this->render_from_template('core/loginform', $context);
    }

    /**
     * Render the login signup form into a nice template for the theme.
     *
     * @param mform $form
     * @return string
     */
    public function render_login_signup_form($form)
    {
        global $SITE;

        $context = $form->export_for_template($this);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context['logourl'] = $url;
        $context['sitename'] = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID), "escape" => false]);

        // modify form html
        $context['formhtml'] = str_replace(array('form-inline', 'col-md-9', 'col-md-3', 'btn-primary', 'btn-secondary'), array('', 'col-md-11 p-0', 'col-md-1 p-0', 'btn-primary btn-block', 'btn-default btn-block'), $context['formhtml']);
        $context['loginpage_context'] = $this->should_display_logo();
        $context['logopos'] = get_config('theme_remui', 'brandlogopos');
        $sitetext = get_config('theme_remui', 'brandlogotext');
        if ($sitetext != '') {
            $context['sitedesc'] = $sitetext;
        }
        return $this->render_from_template('core/signup_form_layout', $context);
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the course administration, only on the course main page.
     *
     * @return string
     */
    public function context_header_settings_menu()
    {
        $context = $this->page->context;
        $menu = new action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (($context->contextlevel == CONTEXT_COURSE) &&
                !empty($currentnode) &&
                ($currentnode->type == navigation_node::TYPE_COURSE || $currentnode->type == navigation_node::TYPE_SECTION)) {
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if ($context->contextlevel == CONTEXT_MODULE &&
                !$courseformat->has_view_page()) {
            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } elseif (!empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                    $activenode->type == navigation_node::TYPE_RESOURCE)) {
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the site front page.
        if ($context->contextlevel == CONTEXT_COURSE &&
                !empty($currentnode) &&
                $currentnode->key === 'home') {
            $showfrontpagemenu = true;
        }

        // This is the user profile page.
        if ($context->contextlevel == CONTEXT_USER &&
                !empty($currentnode) &&
                ($currentnode->key === 'myprofile')) {
            $showusermenu = true;
        }


        if ($showfrontpagemenu) {
            $settingsnode = $this->page->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } elseif ($showcoursemenu) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } elseif ($showusermenu) {
            // Get the course admin node from the settings navigation.
            $settingsnode = $this->page->settingsnav->find('useraccount', navigation_node::TYPE_CONTAINER);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $this->build_action_menu_from_navigation($menu, $settingsnode);
            }
        }

        return $this->render($menu);
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the most specific thing from the settings block. E.g. Module administration.
     *
     * @return string
     */
    public function region_main_settings_menu()
    {
        $context = $this->page->context;
        $menu = new action_menu();

        if ($context->contextlevel == CONTEXT_MODULE) {
            $this->page->navigation->initialise();
            $node = $this->page->navigation->find_active_node();
            $buildmenu = false;
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $buildmenu = true;
            } elseif (!empty($node) && ($node->type == navigation_node::TYPE_ACTIVITY ||
                    $node->type == navigation_node::TYPE_RESOURCE)) {
                $items = $this->page->navbar->get_items();
                $navbarnode = end($items);
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($navbarnode && ($navbarnode->key === $node->key && $navbarnode->type == $node->type)) {
                    $buildmenu = true;
                }
            }
            if ($buildmenu) {
                // Get the course admin node from the settings navigation.
                $node = $this->page->settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        } elseif ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, show category settings menu, if we're on the course category page.
            if ($this->page->pagetype === 'course-index-category') {
                $node = $this->page->settingsnav->find('categorysettings', navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        } else {
            $items = $this->page->navbar->get_items();
            $navbarnode = end($items);

            if ($navbarnode && ($navbarnode->key === 'participants')) {
                $node = $this->page->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        }
        return $this->render($menu);
    }

    /**
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    private function build_action_menu_from_navigation(
        action_menu $menu,
        navigation_node $node,
        $indent = false,
        $onlytopleafnodes = false
    ) {
        $skipped = false;
        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                        // Give preference to setting icon over action icon.
                        if (!empty($menuitem->icon)) {
                            $link->icon = $menuitem->icon;
                        }
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = new action_link(new moodle_url('#'), $menuitem->text, null, ['disabled' => true], $menuitem->icon);
                }
                if ($indent) {
                    $link->add_class('m-l-1');
                }
                if (!empty($menuitem->classes)) {
                    $link->add_class(implode(" ", $menuitem->classes));
                }

                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }
        }
        return $skipped;
    }

    /**
     * Allow plugins to provide some content to be rendered in the navbar.
     * The plugin must define a PLUGIN_render_navbar_output function that returns
     * the HTML they wish to add to the navbar.
     *
     * @return string HTML for the navbar
     */
    public function navbar_plugin_output()
    {
        global $CFG, $PAGE;
        $output = '';

        if ($pluginsfunction = get_plugins_with_function('render_navbar_output')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $output .= $pluginfunction($this);
                }
            }
        }

        return $output;
    }

    public function navbar_plugin_output_custom_icons()
    {
        global $CFG, $PAGE;
        $output = '';

        // toggle chat sidebar nav menu item
        // for RemUI
        // get chat sidebar partial url
        $slidepaneltemplateurl = $CFG->wwwroot . '/theme/remui/request_handler.php?action=get_remui_sidebar';
        if (isloggedin() && !isguestuser()) {
            $output .= '<li class="nav-item" id="toggleChat">
            <a class="nav-link" data-toggle="site-sidebar" href="javascript:void(0)" title="Chat"
            data-url="'.$slidepaneltemplateurl.'">
              <i class="icon wb-chevron-left" aria-hidden="true"></i>
            </a>
          </li>';
        }

        return $output;
    }

    /**
     * Secure login info.
     *
     * @return string
     */
    public function secure_login_info()
    {
        return $this->login_info(false);
    }

    /*
     * For bottom links in sidebar
     *
     * Will be removed later
     * Just for convenience
     */
    public function check_user_admin_cap()
    {
        return \theme_remui\utility::check_user_admin_cap();
    }

    /*
     * For bottom links in sidebar, to check if user is admin
     *
     * Will be removed later
     * Just for convenience
     */
    public function check_user_site_admin()
    {
        global $USER;

        if (is_siteadmin()) {
            return true;
        }
    }

    /*
     * For bottom links in sidebar, to check blog enable / disable
     *
     * Will be removed later
     * Just for convenience
     */
    public function check_blog_enable()
    {
        global $CFG;

        if ($CFG->enableblogs == 1) {
            return true;
        }
    }
}
