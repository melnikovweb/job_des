<?php

if (!function_exists('skeleton_theme_vars')) {

	function skeleton_theme_vars()
	{
		// Throw variables from back to front end.
		$theme_vars = array(
			'home'   => get_home_url(),
			'isHome' => is_front_page(),
			'ajax'   => admin_url('admin-ajax.php'),
			'textLoadMore'   => __('Load more', THEME_TD),
			'textGoToReport'   => __('Go to report', THEME_TD),
			'root' => esc_url_raw(rest_url()),
			'nonce' => wp_create_nonce('wp_rest'),
		);

		wp_localize_script('manifest', 'themeVars', $theme_vars);
	}

	add_action('wp_enqueue_scripts', 'skeleton_theme_vars');
}

if (!function_exists('admin_styles')) {

	function admin_styles()
	{
		if (!wp_style_is('admin-stylesheet', 'enqueued')) {
			wp_enqueue_style('admin-stylesheet', get_template_directory_uri() . '/public/css/wp-dashboard/admin.css', false, '1.0.0', 'all');
		}

		if (!wp_style_is('editor-stylesheet', 'enqueued')) {
			wp_enqueue_style('editor-stylesheet', get_template_directory_uri() . '/public/css/wp-dashboard/editor.css', false, '1.0.0', 'all');
		}
	}

	add_action('admin_enqueue_scripts', 'admin_styles', 999);
}
