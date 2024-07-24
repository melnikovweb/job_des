<?php

/**
 * Redirect child report to parent
 *
 */

add_action('template_redirect', 'child_report_redirect');
function child_report_redirect()
{
	global $post;

	if (is_search()) return;
	if (!isset($post->post_type)) return;

	if ($post->post_type === "signatory-report") {
		if (isset($post->post_parent) && is_numeric($post->post_parent) && ($post->post_parent != 0)) {
			wp_redirect(get_permalink($post->post_parent), 301);
			exit();
			wp_reset_postdata();
		}
	}
}
