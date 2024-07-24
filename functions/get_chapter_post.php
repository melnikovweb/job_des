<?php

function get_chapter_post($args)
{
    if (empty($args['commitment']) || empty($args['chapter']) || empty($args['years'])) {
        return;
    }

    $commitmentID = $args['commitment'];
    $chapterID = $args['chapter'];
    $yearsID = $args['years'];

    $signatoryReports = get_posts([
        'posts_per_page' => -1,
        'post_type'      => 'signatory-report',
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'years',
                'field'    => 'term_id',
                'terms'    => $yearsID,
            ),
            array(
                'taxonomy' => 'commitment',
                'field'    => 'term_id',
                'terms'    => $commitmentID,
            ),
            array(
                'taxonomy' => 'chapter',
                'field'    => 'term_id',
                'terms'    => $chapterID,
            ),
        )
    ]);

    $signatories = array_map(function ($post) {
        $postID = $post->ID;
        $signatory = wp_get_post_terms($postID, 'signatory')[0];

        return [
            'term_id' => $signatory->term_id,
            'name' => $signatory->name,
        ];
    }, $signatoryReports);

    $signatories = sortTitleWithNumbers($signatories);

    $chapter = get_term_by('id', $chapterID, 'chapter');
    $commitment = get_term_by('id', $commitmentID, 'commitment');

    $argsTemplate = [
        'classPageContent' => 'page-content',
        'chapterName' => $chapter->name,
        'chapterTitle' => get_field('chapter_title', 'term_' . $chapter->term_id),
        'commitmentName' => $commitment->name,
        'commitmentDesc' => $commitment->description,
        'signatories' => $signatories,
        'context' => Timber::context(),
    ];

    $data['postsData'] = Timber::compile('parts/layout/report-parts/chapter-content.twig', $argsTemplate);

    return $data;
}
