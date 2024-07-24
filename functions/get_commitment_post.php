<?php

function get_commitment_post($args)
{
    if (empty($args['commitment']) || empty($args['chapter']) || empty($args['years'])) {
        return;
    }

    $commitmentID = $args['commitment'];
    $chapterID = $args['chapter'];
    $yearsID = $args['years'];

    $queryArgs = [
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_type'      => 'signatory-report',
        'tax_query'      => [
            'relation' => 'AND',
            [
                'taxonomy' => 'commitment',
                'field'    => 'term_id',
                'terms'    => $commitmentID,
            ],
            [
                'taxonomy' => 'chapter',
                'field'    => 'term_id',
                'terms'    => $chapterID,
            ],
            [
                'taxonomy' => 'years',
                'field'    => 'term_id',
                'terms'    => $yearsID,
            ],
        ],
    ];

    // for single signatory report
    if (isset($args['parentPost'])) {
        $parentPostID = $args['parentPost'];
        $queryArgs['post_parent'] = $parentPostID;
        $signatory_taxonomy = get_field('signatory', $parentPostID);
        $nameTemplate = 'singleSignatoryReport';
    }

    // for single report
    if (isset($args['signatory'])) {
        $queryArgs['tax_query'][] = [
            'taxonomy' => 'signatory',
            'field'    => 'term_id',
            'terms'    => $args['signatory'],
        ];

        $signatory_taxonomy = get_term_by('id', $args['signatory'], 'signatory');

        $nameTemplate = 'singleReport';
    }

    $posts = get_posts($queryArgs);

    if (empty($posts)) {
        $data['postsData'] = Timber::compile('parts/layout/report-parts/message-error.twig', ['classPageContent' => 'page-content']);
        return $data;
    }

    $currentPostID = $posts[0];

    $postsData = DataCollection::generateData($currentPostID)[0];

    if (isset($args['signatory'])) {
        $parentPostID = get_post_parent($currentPostID)->ID;
    }

    $argsTemplate = [
        'classPageContent' => 'page-content',
        'signatoryName' => $signatory_taxonomy->name,
        'commitmentName' => $postsData['commitment'],
        'shortContent' => !empty($postsData['specific_measures']) ? $postsData['specific_measures'] : [],
        'measuresGroup' => !empty($postsData['measures_group']) ? $postsData['measures_group'] : [],
        'measures_group_10' => !empty($postsData['measures_group_10']) ? $postsData['measures_group_10'] : [],
        'commitmentID' => $commitmentID,
        'currentPostID' => $currentPostID,
        'parentPostID' => $parentPostID,
        'pdfClass' => !get_field('pdf_file', $currentPostID) ? 'sk-btn--disabled' : '',
        'yearsID' => $yearsID,
        'nameTemplate' => $nameTemplate,
        'context' => Timber::context(),
    ];

    $data['postsData'] = Timber::compile('parts/layout/report-parts/main-content.twig', $argsTemplate);

    return $data;
}
