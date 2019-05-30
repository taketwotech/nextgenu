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
 * Renderers for outputting blog data
 *
 * @package    core_blog
 * @subpackage blog
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_remui\output;
use blog_entry;
use blog_listing;
use html_writer;
use blog_entry_attachment;
use context_system;
use stdClass;
use moodle_url;
use core_tag_tag;
defined('MOODLE_INTERNAL') || die();

/**
 * Blog renderer
 */
class core_blog_renderer extends \core_blog_renderer {

    /**
     * Renders a blog entry
     *
     * @param blog_entry $entry
     * @return string The table HTML
     */
    public function render_blog_entry(blog_entry $entry) {
        global $CFG;
        $entryid = optional_param('entryid', 'none', PARAM_INT);
        include_once($CFG->dirroot .'/blog/locallib.php');
        $blogobj = new blog_listing();
        $totalentries = $blogobj->count_entries(); // Get the total number of blogs
        $limit = get_user_preferences('blogpagesize', 10); // Limit the blog from user preferences  
        $page  = optional_param('blogpage', 0, PARAM_INT); // get the page number
        if (($totalentries - $limit * $page) < $limit ) { // if this is last page having less number of blog then Limit
            $totalblog = $totalentries % $limit;        
        } else {
            $totalblog = $limit; // display this number of blog
        }
        //echo $totalblog;
        static $firstentry;
        static $blogcount = 0;
        if ($entryid == 'none') {
            $blogcount ++;
             $ou = "";
            // Function has already run
            if ( $firstentry == null ) { 
                // Starting of ul tag only once per page
                    $ou = html_writer::start_tag('ui',array('class' => 'blocks no-space blocks-100 blocks-xlg-3 blocks-lg-3 blocks-md-2 blocks-sm-2'));
                    $firstentry = true;
            }
            //echo $blogcount;
            $ou .= $this->render_blog_archive($entry);
            if ($blogcount == $totalblog) {
             $ou .= html_writer::end_tag('ui'); // ending of ul tag after the last blog for current page.
             $ou .= html_writer::empty_tag('br');
             $ou .= html_writer::empty_tag('br');            
            }
        } else {
            $ou = $this->render_individual_blog($entry);
        }
        return $ou;
    }


    /**
     * Renders an entry attachment
     *
     * Print link for non-images and returns images as HTML
     *
     * @param blog_entry_attachment $attachment
     * @return string List of attachments depending on the $return input
     */
    public function blog_entry_attachment_image(blog_entry_attachment $attachment) {

        // Image attachments don't get printed as links.
        if (file_mimetype_in_typegroup($attachment->file->get_mimetype(), 'web_image')) {
            $attrs = array('src' => $attachment->url, 'alt' => '');
            return $attrs;
        }
    }

    public function render_blog_archive(blog_entry $entry) {
        global $OUTPUT;
        $syscontext = context_system::instance();

        // Header.
        $mainclass = 'forumpost blog_entry blog clearfix ';
        if ($entry->renderable->unassociatedentry) {
            $mainclass .= 'draft';
        } else {
            $mainclass .= $entry->publishstate;
        }

        // Determine text for publish state.
        switch ($entry->publishstate) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
                break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
                break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
                break;
            default:
                $blogtype = '';
                break;

        }
        // $ou .= $this->output->container($blogtype, 'audience');

        // Attachments.
        $imageurl = "";
        $imagealt = "";
        if ($entry->renderable->attachments) {
            foreach ($entry->renderable->attachments as $attachment) {
                $imageattr = $this->blog_entry_attachment_image($attachment);
                if (is_array($imageattr)) {
                    $imageurl = $imageattr["src"];
                    $imagealt = $imageattr["alt"];
                }
            }
        }
        if (!$imageurl) {
            $imageurl  =  $OUTPUT->image_url('placeholder', 'theme');
        }
        // Title.
        $titlelink = html_writer::link(new moodle_url('/blog/index.php',array('entryid' => $entry->id)),format_string($entry->subject), array('style' => 'text-decoration:none;color:#eee'));
        $link = new moodle_url('/blog/index.php',array('entryid' => $entry->id));
              // Post by.
        $by = new stdClass();
        $fullname = fullname($entry->renderable->user, has_capability('moodle/site:viewfullnames', $syscontext));
        $userurlparams = array('id' => $entry->renderable->user->id, 'course' => $this->page->course->id);
        $by->name = html_writer::link(new moodle_url('/user/view.php', $userurlparams), $fullname, array('style' => 'text-decoration:none'));

        $by->date = userdate($entry->created);
        $postby = $this->output->container(get_string('bynameondate', 'forum', $by), 'author text-muted');
        $description = strip_tags(format_text($entry->summary, $entry->summaryformat,
            array('overflowdiv' => true)));

              // Post by.
            if (strlen($description) > 50) {
                $description = substr($description, 0, 50) . "..";
            }

            $ou = html_writer::start_tag('li',array('class' => 'widget'));
                $ou .= html_writer::start_div('cover overlay overlay-hover'); 
                $ou .= html_writer::img($imageurl, $imagealt, array('style' =>'min-height: 275px', 'class' => 'cover-image overlay-scale'));
                    $ou .= html_writer::start_div('overlay-panel overlay-fade overlay-background overlay-background-fixed text-center vertical-align'); 
                        $ou .= html_writer::start_div('vertical-align-middle');                     
                            $ou .= html_writer::start_div('widget-time widget-divider');                                             
                                $ou .= html_writer::tag('span', $by->date);
                            $ou .= html_writer::end_div();
                        $ou .= html_writer::tag('h3', $titlelink, array('class' => 'widget-title margin-bottom-20'));
                            $ou .= html_writer::tag('p', get_string('author', 'theme_remui') . ' - ' . $by->name);
                            $ou .= html_writer::link($link, get_string('readmore', 'theme_remui'), array("class" => "btn btn-primary waves-effect waves-light"));
                            $ou .= html_writer::tag('p', $description);
                        $ou .= html_writer::end_div();
                    $ou .= html_writer::end_div();
                $ou .= html_writer::end_div();
            $ou .= html_writer::end_tag('li');

        return $ou;
    }

    /**
     * Renders a blog entry
     *
     * @param blog_entry $entry
     * @return string The table HTML
     */
    public function render_individual_blog(blog_entry $entry) {

        global $CFG;

        $syscontext = context_system::instance();

        $stredit = get_string('edit');
        $strdelete = get_string('delete');

        // Header.
        $mainclass = 'forumpost blog_entry blog clearfix a ';
        if ($entry->renderable->unassociatedentry) {
            $mainclass .= 'draft';
        } else {
            $mainclass .= $entry->publishstate;
        }
        $o = $this->output->container_start($mainclass, 'b' . $entry->id);
        $o .= $this->output->container_start('row header clearfix a');

        // User picture.
        $o .= $this->output->container_start('left picture header');
        $o .= $this->output->user_picture($entry->renderable->user);
        $o .= $this->output->container_end();

        $o .= $this->output->container_start('topic starter header clearfix');

        // Title.
        $titlelink = html_writer::link(new moodle_url('/blog/index.php',
                                                       array('entryid' => $entry->id)),
                                                       format_string($entry->subject));
        $o .= $this->output->container($titlelink, 'subject');

        // Post by.
        $by = new stdClass();
        $fullname = fullname($entry->renderable->user, has_capability('moodle/site:viewfullnames', $syscontext));
        $userurlparams = array('id' => $entry->renderable->user->id, 'course' => $this->page->course->id);
        $by->name = html_writer::link(new moodle_url('/user/view.php', $userurlparams), $fullname);

        $by->date = userdate($entry->created);
        $o .= $this->output->container(get_string('bynameondate', 'forum', $by), 'author');

        // Adding external blog link.
        if (!empty($entry->renderable->externalblogtext)) {
            $o .= $this->output->container($entry->renderable->externalblogtext, 'externalblog');
        }

        // Closing subject tag and header tag.
        $o .= $this->output->container_end();
        $o .= $this->output->container_end();

        // Post content.
        $o .= $this->output->container_start('row maincontent clearfix a');

        // Entry.
        $o .= $this->output->container_start('no-overflow content ');

        // Determine text for publish state.
        switch ($entry->publishstate) {
            case 'draft':
                $blogtype = get_string('publishtonoone', 'blog');
                break;
            case 'site':
                $blogtype = get_string('publishtosite', 'blog');
                break;
            case 'public':
                $blogtype = get_string('publishtoworld', 'blog');
                break;
            default:
                $blogtype = '';
                break;

        }
        $o .= $this->output->container($blogtype, 'audience');

        // Attachments.
        $attachmentsoutputs = array();
        if ($entry->renderable->attachments) {
            foreach ($entry->renderable->attachments as $attachment) {
                $o .= $this->render($attachment, false);
            }
        }

        // Body.
        $o .= format_text($entry->summary, $entry->summaryformat, array('overflowdiv' => true));

        if (!empty($entry->uniquehash)) {
            // Uniquehash is used as a link to an external blog.
            $url = clean_param($entry->uniquehash, PARAM_URL);
            if (!empty($url)) {
                $o .= $this->output->container_start('externalblog');
                $o .= html_writer::link($url, get_string('linktooriginalentry', 'blog'));
                $o .= $this->output->container_end();
            }
        }

        // Links to tags.
        $o .= $this->output->tag_list(core_tag_tag::get_item_tags('core', 'post', $entry->id));

        // Add associations.
        if (!empty($CFG->useblogassociations) && !empty($entry->renderable->blogassociations)) {

            // First find and show the associated course.
            $assocstr = '';
            $coursesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel == CONTEXT_COURSE) {
                    $coursesarray[] = $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                }
            }
            if (!empty($coursesarray)) {
                $assocstr .= get_string('associated', 'blog', get_string('course')) . ': ' . implode(', ', $coursesarray);
            }

            // Now show mod association.
            $modulesarray = array();
            foreach ($entry->renderable->blogassociations as $assocrec) {
                if ($assocrec->contextlevel == CONTEXT_MODULE) {
                    $str = get_string('associated', 'blog', $assocrec->type) . ': ';
                    $str .= $this->output->action_icon($assocrec->url, $assocrec->icon, null, array(), true);
                    $modulesarray[] = $str;
                }
            }
            if (!empty($modulesarray)) {
                if (!empty($coursesarray)) {
                    $assocstr .= '<br/>';
                }
                $assocstr .= implode('<br/>', $modulesarray);
            }

            // Adding the asociations to the output.
            $o .= $this->output->container($assocstr, 'tags');
        }

        if ($entry->renderable->unassociatedentry) {
            $o .= $this->output->container(get_string('associationunviewable', 'blog'), 'noticebox');
        }

        // Commands.
        $o .= $this->output->container_start('commands');
        if ($entry->renderable->usercanedit) {

            // External blog entries should not be edited.
            if (empty($entry->uniquehash)) {
                $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                        array('action' => 'edit', 'entryid' => $entry->id)),
                                                        $stredit) . ' | ';
            }
            $o .= html_writer::link(new moodle_url('/blog/edit.php',
                                                    array('action' => 'delete', 'entryid' => $entry->id)),
                                                    $strdelete) . ' | ';
        }

        $entryurl = new moodle_url('/blog/index.php', array('entryid' => $entry->id));
        $o .= html_writer::link($entryurl, get_string('permalink', 'blog'));

        $o .= $this->output->container_end();

        // Last modification.
        if ($entry->created != $entry->lastmodified) {
            $o .= $this->output->container(' [ '.get_string('modified').': '.userdate($entry->lastmodified).' ]');
        }

        // Comments.
        if (!empty($entry->renderable->comment)) {
            $o .= $entry->renderable->comment->output(true);
        }

        $o .= $this->output->container_end();

        // Closing maincontent div.
        $o .= $this->output->container('&nbsp;', 'side options');
        $o .= $this->output->container_end();

        $o .= $this->output->container_end();

        return $o;
    }

    /**
     * Renders an entry attachment
     *
     * Print link for non-images and returns images as HTML
     *
     * @param blog_entry_attachment $attachment
     * @return string List of attachments depending on the $return input
     */
    public function render_blog_entry_attachment(blog_entry_attachment $attachment) {

        $syscontext = context_system::instance();

        // Image attachments don't get printed as links.
        if (file_mimetype_in_typegroup($attachment->file->get_mimetype(), 'web_image')) {
            $attrs = array('src' => $attachment->url, 'alt' => '');
            $o = html_writer::empty_tag('img', $attrs);
            $class = 'attachedimages';
        } else {
            $image = $this->output->pix_icon(file_file_icon($attachment->file),
                                             $attachment->filename,
                                             'moodle',
                                             array('class' => 'icon'));
            $o = html_writer::link($attachment->url, $image);
            $o .= format_text(html_writer::link($attachment->url, $attachment->filename),
                              FORMAT_HTML,
                              array('context' => $syscontext));
            $class = 'attachments';
        }

        return $this->output->container($o, $class);
    }
}
