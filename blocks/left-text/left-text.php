<?php
/*
Block Name: Left Text
Description: left-text block
Category: custom-blocks
Icon: wordpress-alt
Keywords: left-text block
PostTypes: page post
Mode: edit
Align: full
SupportsAlign: left center right wide full
SupportsAnchor: true
SupportsCustomClassName: true
SupportsMode: true
SupportsMultiple: true
SupportsReusable: true
SupportsJSX: false
Example: {"preview_image_help": "/blocks/left-text/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context = Timber::context();
$fields = get_fields();

$left_column = checker_value($fields, 'left_column', true);

$context['icon'] = checker_value($left_column, 'icon');
$context['subtitle'] = checker_value($left_column, 'subtitle');
$context['title'] = checker_value($left_column, 'title');
$context['description'] = checker_value($left_column, 'description');


$right_column = checker_value($fields, 'right_column', true);

$context['img_desktop'] = checker_value($right_column, 'img_desktop');
$context['img_mobile'] = checker_value($right_column, 'img_mobile');

$context['class'] = 'left-text';
$context['block'] = $block;

Timber::render( 'left-text/left-text.twig', $context );

