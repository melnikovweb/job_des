<?php

add_action('init', 'cptui_register_my_cpts_reports');
function cptui_register_my_cpts_reports()
{
  	/**
	 * Post Type: Report.
	 */

	$labels = [
		"name"                  => __( "Reports Archive", THEME_TD ),
		"singular_name"         => __( "Report", THEME_TD ),
		"menu_name"             => __( "Reports Archive", THEME_TD ),
	];

	$args = [
		"label"                 => __( "Reports", THEME_TD ),
		"labels"                => $labels,
		"description"           => "",
		"public"                => true,
		"publicly_queryable"    => true,
		"show_ui"               => true,
		"show_in_rest"          => false,
		"rest_base"             => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive"           => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"delete_with_user"      => false,
		"exclude_from_search"   => false,
		"capability_type"       => "post",
		"map_meta_cap"          => true,
		"hierarchical"          => true,
    	"menu_icon"             => 'dashicons-media-spreadsheet',
		"rewrite"               => [ "slug" => "report", "with_front" => true ],
		"query_var"             => true,
		"menu_position"         => 47,
		"supports"              => [ "title", "editor", "excerpt", "trackbacks", "custom-fields", "comments", "revisions", "thumbnail", "author", "page-attributes" ],
	];

	register_post_type( "report", $args );
}
