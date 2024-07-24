<?php

add_action('init', 'cptui_register_my_taxes_qrenumbers');
function cptui_register_my_taxes_qrenumbers()
{
	/**
	 * Taxonomy: QRE Numbers.
	 */

	$labels = [
		"name"                  => __( "QRE Numbers", THEME_TD ),
		"singular_name"         => __( "QRE Number", THEME_TD ),
	];

	$args = [
		"label"                 => __( "QRE Numbers", THEME_TD ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => false,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'qrenumbers', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"rest_base"             => "qrenumbers",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		'meta_box_cb'           => false,
	];
	register_taxonomy( "qrenumbers", [ "signatory-report" ], $args );	
}
