<?php
// …
 
namespace theme_remui\privacy;

use core_privacy\local\metadata\collection;
 
class provider implements
        // This plugin does store personal user data.
    \core_privacy\local\metadata\provider,
        // This plugin stores user preferences
    \core_privacy\local\request\user_preference_provider
{
        // \core_privacy\local\request\preference_provider {
 
    public static function get_metadata(collection $collection) : collection
    {
 
        // Here you will add more items into the collection.
        // for files saved in moodle tables
        // $collection->add_subsystem_link(
        //     'core_files',
        //     [],
        //     'privacy:metadata:core_files'
        // );

        // for user preferences
        $collection->add_user_preference(
            'course_view_state',
            'privacy:metadata:preference:course_view_state'
        );
        $collection->add_user_preference(
            'viewCourseCategory',
            'privacy:metadata:preference:viewCourseCategory'
        );
        $collection->add_user_preference(
            'aside_right_state',
            'privacy:metadata:preference:aside_right_state'
        );
        $collection->add_user_preference(
            'menubar_state',
            'privacy:metadata:preference:menubar_state'
        );
        

        // Might need this for Google analytics - below is just an example
        // $collection->add_external_location_link('lti_client', [
        //     'userid' => 'privacy:metadata:lti_client:userid',
        //     'fullname' => 'privacy:metadata:lti_client:fullname',
        // ], 'privacy:metadata:lti_client');
 
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int         $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences(int $userid)
    {
        $preferences = array();
        $preferences['course_view_state'] = get_user_preference('course_view_state', null, $userid);
        $preferences['viewCourseCategory'] = get_user_preference('viewCourseCategory', null, $userid);
        $preferences['aside_right_state'] = get_user_preference('aside_right_state', null, $userid);
        $preferences['menubar_state'] = get_user_preference('menubar_state', null, $userid);

        foreach ($preferences as $preference => $value) {
            switch ($value) {
                // for 'course_view_state' and 'viewCourseCategory'
                case "grid":
                    $preference_description = get_string($preference . '_grid', 'theme_remui');
                    break;
                case "list":
                    $preference_description = get_string($preference . '_list', 'theme_remui');
                    break;
                
                // for 'aside_right_state'
                case "":
                    // check via which preference
                    if ($preference == 'aside_right_state') {
                        $preference_description = get_string('aside_right_state_', 'theme_remui');
                    }
                    break;
                case "overrideaside":
                    $preference_description = get_string('aside_right_state_overrideaside', 'theme_remui');
                    break;
                
                // for 'menubar_state'
                case "fold":
                    $preference_description = get_string('menubar_state_fold', 'theme_remui');
                    break;
                case "unfold":
                    $preference_description = get_string('menubar_state_unfold', 'theme_remui');
                    break;
                case "open":
                    $preference_description = get_string('menubar_state_open', 'theme_remui');
                    break;
                case "hide":
                    $preference_description = get_string('menubar_state_hide', 'theme_remui');
                    break;
            }
            if (isset($preference_description)) {
                writer::export_user_preference('theme_remui', $preference, $value, $preference_description);
            }
        }
    }
}
