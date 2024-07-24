<?php

/**
 * The Template for displaying all single-signatory posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::context();
$currentPost     = Timber::get_post();
$context['post'] = $currentPost;

$postID = $currentPost->ID;
$signatory_taxonomy = get_field('signatory');
$report_year = get_field('report_year');
$context['pdfClass'] = !get_field('pdf_file') ? 'sk-btn--disabled' : '';

// hero
$context['title'] = $signatory_taxonomy->name;
$context['report_year'] = [
    'term_id' => $report_year->term_id,
    'name' => $report_year->name,
];
$context['intro'] = get_field('intro');
// END hero

// sidebar
$sidebar = [];

$currentSR = get_posts([
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'post_type'      => 'signatory-report',
    'post_parent'    => $postID,
    'tax_query' => array(
        array(
            'taxonomy' => 'years',
            'field'    => 'term_id',
            'terms'    => $report_year->term_id,
        ),
    )
]);

$chapters = get_terms([
    'taxonomy'       => 'chapter',
    'hide_empty'     => true,
    'object_ids'     => $currentSR,
    'order'          => 'ASC',
    'posts_per_page' => -1,
]);

foreach ($chapters as $key => $chapter) {

    $currentSRByChapter = get_posts([
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_type'      => 'signatory-report',
        'post_parent'    => $postID,
        'tax_query' => array(
            array(
                'taxonomy' => 'chapter',
                'field'    => 'term_id',
                'terms'    => $chapter->term_id,
            ),
        )
    ]);

    $commitments = get_terms([
        'taxonomy'   => 'commitment',
        'hide_empty' => true,
        'object_ids' => $currentSRByChapter,
        'order'      => 'ASC',
    ]);

    $chapterIndex = 'chapter-0' . ($key + 1);
    $sidebar[$chapterIndex] = [
        'term_id' => $chapter->term_id,
        'name' => $chapter->name,
    ];

    foreach ($commitments as $key => $commitment) {
        $sidebar[$chapterIndex]['commitments'][] = [
            'term_id' => $commitment->term_id,
            'name' => $commitment->name,
        ];
    }
}

$sortedChapter = array_map(function ($chapter) {
    return [
        'term_id' => $chapter['term_id'],
        'name' => $chapter['name'],
        'commitments' => sortTitleWithNumbers($chapter['commitments'])
    ];
}, $sidebar);

$sortedChapter = sortTitleWithNumbers($sortedChapter);

$context['sidebar'] = $sortedChapter;
// END sidebar

$context['classes'] = 'single-signatory-report';
$context['postID'] = $postID;
$context['classPageContent'] = 'page-content';

Timber::render('single-signatory-report.twig', $context);
