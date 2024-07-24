<?php

add_action('init', 'cptui_register_my_cpts_signatory_report');
function cptui_register_my_cpts_signatory_report()
{
	/**
	 * Post Type: Signatory Report.
	 */

	$labels = [
		"name"                  => __("Signatory Reports", THEME_TD),
		"singular_name"         => __("Signatory Report", THEME_TD),
		"menu_name"             => __("Signatory Reports", THEME_TD),
	];

	$args = [
		"label"                 => __("Signatory Reports", THEME_TD),
		"labels"                => $labels,
		"description"           => "",
		"public"                => true,
		"publicly_queryable"    => true,
		"show_ui"               => true,
		"show_in_rest"          => false,
		"rest_base"             => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive"           => false,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"delete_with_user"      => false,
		"exclude_from_search"   => false,
		"capability_type"       => ["signatoryReport", "signatoryReports"],
		"map_meta_cap"          => true,
		"hierarchical"          => true,
		"menu_icon"             => 'dashicons-media-spreadsheet',
		"rewrite"               => ["slug" => "signatory-report", "with_front" => true],
		"query_var"             => true,
		"menu_position"         => 47,
		"supports"              => ["title", "editor", "revisions", "page-attributes"],
	];

	register_post_type("signatory-report", $args);
}


/**
 * A simple trick
 * use: add ?reload_caps=1 after /wp-admin/ and reload the page
 * /wp-admin/?reload_caps=1
 */
add_action('admin_init', 'update_administrator_caps');

function update_administrator_caps()
{
	if (is_admin() && isset($_GET['reload_caps']) && '1' == $_GET['reload_caps']) {
		$administrator = get_role('administrator');
		$additionalCapabilities = [
			'publish',
			'delete',
			'delete_others',
			'delete_private',
			'delete_published',
			'edit',
			'edit_others',
			'edit_private',
			'edit_published',
			'read_private',
		];

		foreach ($additionalCapabilities as $cap) {
			$administrator->add_cap("{$cap}_signatoryReports");
		}
	}
}
