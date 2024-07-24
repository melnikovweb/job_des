<?php

function get_signatories_posts($args)
{
    $search = $args['search'] ?? '';

    $queryArgs = [
        'taxonomy'   => 'signatory',
        'hide_empty' => false,
        'parent'     => 0,
        'order'      => 'ASC',
    ];

    if (!empty($search)) $queryArgs['search'] = $search;

    $signatories = get_terms($queryArgs);

    $signatoriesData = [];

    foreach ($signatories as $signatory) {

        $posts = get_posts([
            'posts_per_page' => -1,
            'post_type'      => 'signatory-report',
            'tax_query' => array(
                array(
                    'taxonomy' => 'signatory',
                    'field'    => 'term_id',
                    'terms'    => $signatory->term_id,
                )
            )
        ]);

        $mainPost = array_values(array_filter($posts, function ($post) {
            if ($post->post_parent === 0) {
                return $post;
            }
        }));

        $signatoriesData[] = [
            'title' => $signatory->name,
            'link' => (!empty($mainPost)) ? get_the_permalink($mainPost[0]->ID) : '',
            'imageID' => get_field('archive_image', 'term_' . $signatory->term_id),
        ];
    }

    if (!empty($signatoriesData)) {
        $postsData = Timber::compile('blocks/signatories-filter/template-parts/signatories-list.twig', [
            'signatories' => $signatoriesData,
        ]);
    } else {
        $postsData = Timber::compile('parts/layout/not-found.twig', [
            'type' => 'signatories'
        ]);
    }

    $data['postsData'] = $postsData;

    return $data;
}
