<?php

add_action('init', 'cptui_register_my_taxes_slinumbers');
function cptui_register_my_taxes_slinumbers()
{
	/**
	 * Taxonomy: SLI Numbers.
	 */

	$labels = [
		"name"                  => __( "SLI Numbers", THEME_TD ),
		"singular_name"         => __( "SLI Number", THEME_TD ),
	];

	$args = [
		"label"                 => __( "SLI Numbers", THEME_TD ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => false,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'slinumbers', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"rest_base"             => "slinumbers",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		'meta_box_cb'           => false,
	];
	register_taxonomy( "slinumbers", [ "signatory-report" ], $args );	
}
