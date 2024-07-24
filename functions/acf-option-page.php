<?php

if(function_exists('acf_add_options_page')) {
	acf_add_options_page(array(
		'page_title' => 'Options',
		'menu_title' => 'Options',
		'menu_slug'  => 'theme-settings',
		'capability' => 'edit_posts',
		'redirect'   => false
	));
}

/**
 * Advanced Custom Fields Options function
 * Always fetch an Options field value from the default language
 */
if( ! function_exists('cl_acf_set_language') && function_exists('acf_get_setting')) {
    function cl_acf_set_language() {
        return acf_get_setting('default_language');
    }
}

if( ! function_exists('get_global_option')) {
    function get_global_option($name) {
        add_filter('acf/settings/current_language', 'cl_acf_set_language', 100);
        $option = get_field($name, 'option');
        remove_filter('acf/settings/current_language', 'cl_acf_set_language', 100);
        return $option;
    }
}
