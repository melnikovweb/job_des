<?php

/**
 * The Template for displaying all single-report posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

$report_year = get_field('report_year');

// hero
$context['title'] = $timber_post->name;
$context['report_year'] = [
    'term_id' => $report_year->term_id,
    'name' => $report_year->name,
];
// END hero

// sidebar
$sidebar = [];

$parentSignatoryReports = get_posts([
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'post_type'      => 'signatory-report',
    'post_parent'    => 0,
    'tax_query' => array(
        array(
            'taxonomy' => 'years',
            'field'    => 'term_id',
            'terms'    => $report_year->term_id,
        ),
    )
]);

$childSignatoryReports = get_posts([
    'posts_per_page' => -1,
    'post_type'      => 'signatory-report',
    'post__not_in'    => $parentSignatoryReports,
    'tax_query' => array(
        array(
            'taxonomy' => 'years',
            'field'    => 'term_id',
            'terms'    => $report_year->term_id,
        ),
    )
]);

$signatoryReportsData = array_map(function ($post) {
    $postID = $post->ID;
    $chapter = wp_get_post_terms($postID, 'chapter')[0];
    $commitment = wp_get_post_terms($postID, 'commitment')[0];

    return [
        'id'        => $postID,
        'post_type' => $post->post_type,
        'post_title' => $post->post_title,
        'chapter' => [
            'term_id' => $chapter->term_id,
            'name' => $chapter->name,
            'title' => get_field('chapter_title', 'term_' . $chapter->term_id),
        ],
        'commitment' => [
            'term_id' => $commitment->term_id,
            'name' => $commitment->name,
        ]
    ];
}, $childSignatoryReports);

$chaptersData = [];

foreach ($signatoryReportsData as $key => $report) {
    $chapterID = $report['chapter']['term_id'];
    $commitmentID = $report['commitment']['term_id'];

    if (!isset($chaptersData[$chapterID])) $chaptersData[$chapterID] = $report['chapter'];
    if (!isset($chaptersData[$chapterID]['commitments'][$commitmentID])) $chaptersData[$chapterID]['commitments'][$commitmentID] = $report['commitment'];
}

$sortedChapter = array_map(function ($chapter) {
    return [
        'term_id' => $chapter['term_id'],
        'name' => $chapter['name'],
        'title' => $chapter['title'],
        'commitments' => sortTitleWithNumbers($chapter['commitments'])
    ];
}, $chaptersData);

$sortedChapter = sortTitleWithNumbers($sortedChapter);

$context['chaptersData'] = $sortedChapter;
// END sidebar

$context['classPageContent'] = 'page-content';

$context['classes'] = 'single-report';

Timber::render('single-report.twig', $context);
