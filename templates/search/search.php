<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context          = Timber::context();
$search           = get_search_query();

$currentPage             = !empty($_GET['currentPage']) ? $_GET['currentPage'] : 1;
$context['currentPage']  = $currentPage;
$postsPerPage            = !empty($_GET['postsPerPage']) ? $_GET['postsPerPage'] : 6;
$context['postsPerPage'] = $postsPerPage;
$offset                  = $postsPerPage * ($currentPage - 1);


$termSignatory           = AdvancedSearch::searchSignatory($search, $offset, $_GET);
$countTermSignatory      = count($termSignatory);
$allTermSignatory        = AdvancedSearch::getSignatoryCount($search, $_GET);

$posts_offset            = $offset - $countTermSignatory > 0 ? $offset - $allTermSignatory : 0;
$posts_per_page          = $countTermSignatory > $postsPerPage ? 0 : ($postsPerPage - $countTermSignatory);

$posts                   = get_search_list($_GET, $posts_offset, $posts_per_page);

$args = array(
    'post_type'        => 'any',
    'post_status'      => 'publish',
    'orderby'          => 'date',
    'order'            => 'ASC',
    'offset'                 => $posts_offset,
    'posts_per_page'         => $posts_per_page,
    'suppress_filters'       => 0,
    'post_parent__not_in'    => [0],
    'update_post_term_cache' => false, // Improves Query performance
    'update_post_meta_cache' => false, // Improves Query performance
);

$queryCount       = AdvancedSearch::getReportCount($args, $search);

$searchResult     = array_merge($termSignatory, $posts);
$context['posts'] = $searchResult;
$count            = count($searchResult);

$totalPosts            = $queryCount + $allTermSignatory;
$totalPages            = ceil( $totalPosts / $postsPerPage);
$context['totalPosts'] = $totalPosts;
$context['totalPages'] = $totalPages;

$context['title']      = "Found $totalPosts search results for" . ': ' . get_search_query();

function get_search_list($args, $posts_offset, $posts_per_page) {
    $query = [];

    if ( $args['s'] ) {

        $args = array(
            'post_type'        => 'any',
            'post_status'      => 'publish',
            'orderby'          => 'date',
            'order'            => 'ASC',
            'offset'                 => $posts_offset,
            'posts_per_page'         => $posts_per_page,
            'suppress_filters'       => 0,
            'post_parent__not_in'    => [0],
            'search_title'           => $args['s'],
            'update_post_term_cache' => false, // Improves Query performance
            'update_post_meta_cache' => false, // Improves Query performance
        );


        add_filter( 'posts_where', 'title_filter', 10, 2 );

        $query = new WP_Query ($args);

		remove_filter( 'posts_where', 'title_filter', 10 );

        $query = AdvancedSearch::preparePostData($query->posts);
    }

    return $query;
}


Timber::render('search.twig', $context);
