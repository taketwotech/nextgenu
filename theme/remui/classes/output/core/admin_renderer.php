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
 * Admin renderer.
 *
 * @package    theme_noanme
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_remui\output\core;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use html_table;
use html_table_cell;
use html_table_row;
use html_writer;
use core_plugin_manager;

require_once($CFG->dirroot . '/' . $CFG->admin . '/renderer.php');

/**
 * Admin renderer class.
 *
 * @package    theme_noanme
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_renderer extends \core_admin_renderer {

    /**
     * Output a warning message.
     *
     * @param string $message the message to display.
     * @param string $type type class
     * @return string HTML to output.
     */
    protected function warning($message, $type = 'warning') {
        return $this->box($message, 'generalbox m-b-1 admin' . $type);
    }

        /**
     * Displays all known plugins and information about their installation or upgrade
     *
     * This default implementation renders all plugins into one big table. The rendering
     * options support:
     *     (bool)full = false: whether to display up-to-date plugins, too
     *     (bool)xdep = false: display the plugins with unsatisified dependecies only
     *
     * @param core_plugin_manager $pluginman provides information about the plugins.
     * @param int $version the version of the Moodle code from version.php.
     * @param array $options rendering options
     * @return string HTML code
     */
    public function plugins_check_table(\core_plugin_manager $pluginman, $version, array $options = array()) {

        $plugininfo = $pluginman->get_plugins();

        if (empty($plugininfo)) {
            return '';
        }

        $options['full'] = isset($options['full']) ? (bool)$options['full'] : false;
        $options['xdep'] = isset($options['xdep']) ? (bool)$options['xdep'] : false;

        $table = new html_table();
        $table->id = 'plugins-check';
        // $table->align = 'left';
        $table->head = array(
            get_string('displayname', 'core_plugin').' / '.get_string('rootdir', 'core_plugin'),
            get_string('versiondb', 'core_plugin'),
            get_string('versiondisk', 'core_plugin'),
            get_string('requires', 'core_plugin'),
            get_string('source', 'core_plugin').' / '.get_string('status', 'core_plugin'),
        );
        $table->colclasses = array(
            'displayname', 'versiondb', 'versiondisk', 'requires', 'status',
        );
        $table->data = array();

        // Number of displayed plugins per type.
        $numdisplayed = array();
        // Number of plugins known to the plugin manager.
        $sumtotal = 0;
        // Number of plugins requiring attention.
        $sumattention = 0;
        // List of all components we can cancel installation of.
        $installabortable = $pluginman->list_cancellable_installations();
        // List of all components we can cancel upgrade of.
        $upgradeabortable = $pluginman->list_restorable_archives();

        foreach ($plugininfo as $type => $plugins) {

            $header = new html_table_cell($pluginman->plugintype_name_plural($type));
            $header->header = true;
            $header->colspan = count($table->head);
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'plugintypeheader type-' . $type;

            $numdisplayed[$type] = 0;

            if (empty($plugins) and $options['full']) {
                $msg = new html_table_cell(get_string('noneinstalled', 'core_plugin'));
                $msg->colspan = count($table->head);
                $row = new html_table_row(array($msg));
                $row->attributes['class'] .= 'msg msg-noneinstalled';
                $table->data[] = $header;
                $table->data[] = $row;
                continue;
            }

            $plugintyperows = array();

            foreach ($plugins as $name => $plugin) {
                $sumtotal++;
                $row = new html_table_row();
                $row->attributes['class'] = 'type-' . $plugin->type . ' name-' . $plugin->type . '_' . $plugin->name;

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name, null)) {
                    $icon = $this->output->pix_icon('icon', '', $plugin->type . '_' . $plugin->name, array('class' => 'smallicon pluginicon'));
                } else {
                    $icon = '';
                }

                $displayname = new html_table_cell(
                    $icon.
                    html_writer::span($plugin->displayname, 'pluginname').
                    html_writer::div($plugin->get_dir(), 'plugindir')
                );

                $versiondb = new html_table_cell($plugin->versiondb);
                $versiondisk = new html_table_cell($plugin->versiondisk);

                if ($isstandard = $plugin->is_standard()) {
                    $row->attributes['class'] .= ' standard';
                    $sourcelabel = html_writer::span(get_string('sourcestd', 'core_plugin'), 'sourcetext label');
                } else {
                    $row->attributes['class'] .= ' extension';
                    $sourcelabel = html_writer::span(get_string('sourceext', 'core_plugin'), 'sourcetext label label-info');
                }

                $coredependency = $plugin->is_core_dependency_satisfied($version);
                $otherpluginsdependencies = $pluginman->are_dependencies_satisfied($plugin->get_other_required_plugins());
                $dependenciesok = $coredependency && $otherpluginsdependencies;

                $statuscode = $plugin->get_status();
                $row->attributes['class'] .= ' status-' . $statuscode;
                $statusclass = 'statustext label ';
                switch ($statuscode) {
                    case core_plugin_manager::PLUGIN_STATUS_NEW:
                        $statusclass .= $dependenciesok ? 'label-success' : 'label-warning';
                        break;
                    case core_plugin_manager::PLUGIN_STATUS_UPGRADE:
                        $statusclass .= $dependenciesok ? 'label-info' : 'label-warning';
                        break;
                    case core_plugin_manager::PLUGIN_STATUS_MISSING:
                    case core_plugin_manager::PLUGIN_STATUS_DOWNGRADE:
                    case core_plugin_manager::PLUGIN_STATUS_DELETE:
                        $statusclass .= 'label-important';
                        break;
                    case core_plugin_manager::PLUGIN_STATUS_NODB:
                    case core_plugin_manager::PLUGIN_STATUS_UPTODATE:
                        $statusclass .= $dependenciesok ? '' : 'label-warning';
                        break;
                }
                $status = html_writer::span(get_string('status_' . $statuscode, 'core_plugin'), $statusclass);

                if (!empty($installabortable[$plugin->component])) {
                    $status .= $this->output->single_button(
                        new moodle_url($this->page->url, array('abortinstall' => $plugin->component)),
                        get_string('cancelinstallone', 'core_plugin'),
                        'post',
                        array('class' => 'actionbutton cancelinstallone')
                    );
                }

                if (!empty($upgradeabortable[$plugin->component])) {
                    $status .= $this->output->single_button(
                        new moodle_url($this->page->url, array('abortupgrade' => $plugin->component)),
                        get_string('cancelupgradeone', 'core_plugin'),
                        'post',
                        array('class' => 'actionbutton cancelupgradeone')
                    );
                }

                $availableupdates = $plugin->available_updates();
                if (!empty($availableupdates)) {
                    foreach ($availableupdates as $availableupdate) {
                        $status .= $this->plugin_available_update_info($pluginman, $availableupdate);
                    }
                }

                $status = new html_table_cell($sourcelabel.' '.$status);

                $requires = new html_table_cell($this->required_column($plugin, $pluginman, $version));

                $statusisboring = in_array($statuscode, array(
                        core_plugin_manager::PLUGIN_STATUS_NODB, core_plugin_manager::PLUGIN_STATUS_UPTODATE));

                if ($options['xdep']) {
                    // we want to see only plugins with failed dependencies
                    if ($dependenciesok) {
                        continue;
                    }

                } else if ($statusisboring and $dependenciesok and empty($availableupdates)) {
                    // no change is going to happen to the plugin - display it only
                    // if the user wants to see the full list
                    if (empty($options['full'])) {
                        continue;
                    }

                } else {
                    $sumattention++;
                }

                // The plugin should be displayed.
                $numdisplayed[$type]++;
                $row->cells = array($displayname, $versiondb, $versiondisk, $requires, $status);
                $plugintyperows[] = $row;
            }

            if (empty($numdisplayed[$type]) and empty($options['full'])) {
                continue;
            }

            $table->data[] = $header;
            $table->data = array_merge($table->data, $plugintyperows);
        }

        // Total number of displayed plugins.
        $sumdisplayed = array_sum($numdisplayed);

        if ($options['xdep']) {
            // At the plugins dependencies check page, display the table only.
            return html_writer::table($table);
        }

        $out = $this->output->container_start('', 'plugins-check-info');

        if ($sumdisplayed == 0) {
            $out .= $this->output->heading(get_string('pluginchecknone', 'core_plugin'));

        } else {
            if (empty($options['full'])) {
                $out .= $this->output->heading(get_string('plugincheckattention', 'core_plugin'));
            } else {
                $out .= $this->output->heading(get_string('plugincheckall', 'core_plugin'));
            }
        }

        $out .= $this->output->container_start('actions');

        $installableupdates = $pluginman->filter_installable($pluginman->available_updates());
        if ($installableupdates) {
            $out .= $this->output->single_button(
                new moodle_url($this->page->url, array('installupdatex' => 1)),
                get_string('updateavailableinstallall', 'core_admin', count($installableupdates)),
                'post',
                array('class' => 'singlebutton updateavailableinstallall')
            );
        }

        if ($installabortable) {
            $out .= $this->output->single_button(
                new moodle_url($this->page->url, array('abortinstallx' => 1)),
                get_string('cancelinstallall', 'core_plugin', count($installabortable)),
                'post',
                array('class' => 'singlebutton cancelinstallall')
            );
        }

        if ($upgradeabortable) {
            $out .= $this->output->single_button(
                new moodle_url($this->page->url, array('abortupgradex' => 1)),
                get_string('cancelupgradeall', 'core_plugin', count($upgradeabortable)),
                'post',
                array('class' => 'singlebutton cancelupgradeall')
            );
        }

        $out .= html_writer::div(html_writer::link(new moodle_url($this->page->url, array('showallplugins' => 0)),
            get_string('plugincheckattention', 'core_plugin')).' '.html_writer::span($sumattention, 'badge badge-info'));

        $out .= html_writer::div(html_writer::link(new moodle_url($this->page->url, array('showallplugins' => 1)),
            get_string('plugincheckall', 'core_plugin')).' '.html_writer::span($sumtotal, 'badge badge-info'));

        $out .= $this->output->container_end(); // End of .actions container.
        $out .= $this->output->container_end(); // End of #plugins-check-info container.

        if ($sumdisplayed > 0 or $options['full']) {
            $out .= html_writer::table($table);
        }

        return $out;
    }

}
