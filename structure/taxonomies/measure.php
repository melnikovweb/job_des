<?php

add_action('init', 'cptui_register_my_taxes_measures');
function cptui_register_my_taxes_measures()
{
	/**
	 * Taxonomy: Measures.
	 */

	$labels = [
		"name"                  => __( "Measures", THEME_TD ),
		"singular_name"         => __( "Measure", THEME_TD ),
	];

	$args = [
		"label"                 => __( "Measures", THEME_TD ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => false,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'measure', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"rest_base"             => "measure",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		'meta_box_cb'           => false,
	];
	register_taxonomy( "measure", [ "signatory-report" ], $args );	
}
