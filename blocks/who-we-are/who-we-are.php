<?php
/*
Block Name: Who we are
Description: who-we-are block
Category: custom-blocks
Icon: wordpress-alt
Keywords: who-we-are block
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
Example: {"preview_image_help": "/blocks/who-we-are/preview.jpg"}
*/

if (isset($block['data']['preview_image_help'])) :
    echo '<img src="' . get_template_directory_uri() . $block['data']['preview_image_help'] . '" style="width:100%; height:auto;">';
    return;
endif;

$context = Timber::context();
$fields = get_fields();

$left_column = checker_value($fields, 'left_column', true);

$context['subtitle'] = checker_value($left_column, 'subtitle');
$context['title'] = checker_value($left_column, 'title');
$context['description'] = checker_value($left_column, 'description');

$right_column = checker_value($fields, 'right_column', true);
$context['list_items'] = checker_value($right_column, 'list_items');

$context['img_desktop'] = checker_value($fields, 'img_desktop');
$context['img_mobile'] = checker_value($fields, 'img_mobile');

$context['classes'] = 'who-we-are';
$context['block'] = $block;

Timber::render('who-we-are/who-we-are.twig', $context);
