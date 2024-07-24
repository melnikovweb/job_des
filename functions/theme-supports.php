<?php

/**
 * Text domain definition
 */
defined( 'THEME_TD' ) ? THEME_TD : define( 'THEME_TD', 'transparency-centre' );

/**
 * Disable the theme / plugin text editor in Admin
 */
define( 'DISALLOW_FILE_EDIT', true );

/**
 * Register theme support for languages, menus, post-thumbnails, post-formats etc.
 */
add_theme_support('menus');
add_theme_support('post-thumbnails');
add_theme_support( 'title-tag');

function alphabet_widgets_init() {

	register_sidebar( array(
		'name'          => 'Home right sidebar',
		'id'            => 'home_right_1',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="rounded">',
		'after_title'   => '</h2>',
	) );

}
add_action( 'widgets_init', 'alphabet_widgets_init' );

/**
 * Register navigation menus
 */
add_action( 'after_setup_theme', 'register_theme_menus' );

function register_theme_menus() {
	register_nav_menus(
		array(
			'primary_menu'  => __( 'Primary Menu', THEME_TD ),
			'footer_menu'   => __( 'Footer Menu', THEME_TD ),
		)
	);
}