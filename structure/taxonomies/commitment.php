<?php

add_action('init', 'cptui_register_my_taxes_commitment');
function cptui_register_my_taxes_commitment()
{
	/**
	 * Taxonomy: Commitment.
	 */

	$labels = [
		"name"                  => __( "Commitments", THEME_TD ),
		"singular_name"         => __( "Commitment", THEME_TD ),
	];

	$args = [
		"label"                 => __( "Commitments", THEME_TD ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => false,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'commitment', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"rest_base"             => "commitment",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		'meta_box_cb'           => false,
	];
	register_taxonomy( "commitment", [ "signatory-report" ], $args );	
}
