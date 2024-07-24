<?php

/**
 * WP-Ajax handler for advanced search
 */

 if( ! function_exists('advanced_search')) {
    add_action( 'wp_ajax_advanced_search', 'advanced_search' );
    add_action( 'wp_ajax_nopriv_advanced_search', 'advanced_search' );

    function advanced_search() {
		$currentPage  = !empty($_POST['currentPage']) ? (int)$_POST['currentPage'] : 1;
		$postsPerPage = !empty($_POST['postsPerPage']) ? (int)$_POST['postsPerPage'] : 6;
		$offset       = $postsPerPage * ($currentPage - 1);
		$search       = !empty($_POST['search']) ? $_POST['search'] : null;
		$taxonomies   = get_object_taxonomies( 'signatory-report' );

		// Search by Signatory terms
		$termSignatory = AdvancedSearch::searchSignatory($search, $offset, $_POST);

		$countTermSignatory = count($termSignatory);

		$allTermSignatory = AdvancedSearch::getSignatoryCount($search, $_POST);

		$posts_offset   = $offset - $countTermSignatory > 0 ? $offset - $allTermSignatory : 0;

		$posts_per_page = $countTermSignatory > $postsPerPage ? 0 : ($postsPerPage - $countTermSignatory);

		$args = array(
			'post_type'              => ['signatory-report'],
			'post_status'            => 'publish',
			'orderby'                => 'date',
			'order'                  => 'ASC',
			'offset'                 => $posts_offset,
		    'posts_per_page'         => $posts_per_page,
			'suppress_filters'       => 0,
			'post_parent__not_in'    => [0],
			'update_post_term_cache' => false, // Improves Query performance
			'update_post_meta_cache' => false, // Improves Query performance
		);

		$postsArgs = AdvancedSearch::searchByParamsAndTitle($args, $search, $taxonomies, $_POST);

		$termsArgs = !empty($search) ?  AdvancedSearch::searchByTermsTitle($args, $search, $taxonomies) : [];

		add_filter( 'posts_where', 'title_filter', 10, 2 );

		$query = new WP_Query ($postsArgs);

		$queryCount = AdvancedSearch::getReportCount($args, $search);

		remove_filter( 'posts_where', 'title_filter', 10 );

		$termQuery = new WP_Query ($termsArgs);

		$reports = AdvancedSearch::mergeValues($query->posts, $termQuery->posts);

		// Choose just commitment reports
		$reports = array_filter($reports, function($post) {
			if( $post->post_parent !== 0 ) {
				return $post;
			}
		});

		// Create data
		$reports = AdvancedSearch::preparePostData($reports);

		$mergedReports = array_merge($termSignatory, $reports);

		$totalPosts = isset($_POST['signatory']) ? count($mergedReports) : $queryCount + $allTermSignatory;

		$totalPages = ceil( $totalPosts / $postsPerPage);

		wp_reset_postdata();

        wp_send_json( [
			'reports'     => $mergedReports,
			'currentPage' => $currentPage,
			'totalPosts'  => count($mergedReports),
			'totalPages'  => count($mergedReports) > 0 ? $totalPages : 0,
		] );
        die;
    }
}
