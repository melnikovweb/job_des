<?php
/*
Block Name: Signatories filter
Description: signatories-filter block
Category: custom-blocks
Icon: wordpress-alt
Keywords: signatories-filter block
PostTypes: page post game_post
Mode: edit
Align: full
SupportsAlign: left center right wide full
SupportsAnchor: true
SupportsCustomClassName: true
SupportsMode: true
SupportsMultiple: true
SupportsReusable: true
SupportsJSX: false
Example: {"preview_image_help": "/blocks/signatories-filter/preview.jpg"}
*/

if (isset($block['data']['preview_image_help'])) :
    echo '<img src="' . get_template_directory_uri() . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    return;
endif;

$context = Timber::context();

$signatoriesData = get_signatories_posts($_GET);

$context['signatories'] = checker_value($signatoriesData, 'postsData');
$context['classes'] = 'signatories-filter';
$context['block'] = $block;

Timber::render('signatories-filter/signatories-filter.twig', $context);
