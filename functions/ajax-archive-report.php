<?php

/**
 * WP-Ajax handler for report
 */

if (!function_exists('archive_reports')) {
	add_action('wp_ajax_archive_reports', 'archive_reports');
	add_action('wp_ajax_nopriv_archive_reports', 'archive_reports');

	function archive_reports()
	{
		$years = $_POST['years'];

		$allReports = (new WP_Query(
			array(
				'post_type' => ['report', 'signatory-report'],
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order'   => 'ASC',
				'suppress_filters' => 0,
				'tax_query' => array(
					array(
						'taxonomy' => 'years',
						'field'    => 'term_id',
						'terms'    => $years,
					)
				)
			)
		))->posts;

		// Choose just main reports (parent)
		$mainReports = array_filter($allReports, function ($post) {
			if ($post->post_parent === 0) {
				return $post;
			}
		});

		// Create data
		$mainReports = array_map(function ($post) {
			$signatory = wp_get_post_terms($post->ID, 'signatory');
			$signatory = isset($signatory) && isset($signatory[0]);
			return [
				'id'        => $post->ID,
				'post_type' => $post->post_type,
				'post_title' => $post->post_title,
				'signatory' => $signatory ? wp_get_post_terms($post->ID, 'signatory')[0]->name : '',
				'year'      => wp_get_post_terms($post->ID, 'years')[0]->name,
			];
		}, $mainReports);

		/* Sort by Signatory */
		usort($mainReports, function ($a, $b) {
			if ($a['signatory'] == $b['signatory'])
				return 0;
			return $a['signatory'] > $b['signatory'] ? 1 : -1;
		});

		// Map by year
		$response = [];
		foreach ($mainReports as $i => $post) {
			if ($post['post_type'] === 'report') {
				$response[$post['year']]['title']    = $post['post_title'];
				$response[$post['year']]['subtitle'] = get_field('subtitle', $post['id']);
				$response[$post['year']]['link']     = get_the_permalink($post['id']);
				$response[$post['year']]['year']     = $post['year'];
			}

			//Skip CPT: report
			if ($post['post_type'] !== 'report') {
				$response[$post['year']]['reports'][$i] = $post;
				$response[$post['year']]['count'] += 1;
			}
		}

		// Skip Report (case status Draft)
		$response = array_filter($response, function ($post) {
			return isset($post['year']);
		});

		// Sort by year
		usort($response, function ($a, $b) {
			if (is_array($a) && is_array($b)) {
				if ($a['year'] == $b['year'])
					return 0;
				return $a['year'] > $b['year'] ? 1 : -1;
			}
			return 0;
		});

		wp_reset_postdata();

		wp_send_json($response);
		die;
	}
}
