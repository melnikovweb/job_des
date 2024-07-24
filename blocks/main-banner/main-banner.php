<?php
/*
Block Name: Main banner
Description: main-banner block
Category: custom-blocks
Icon: wordpress-alt
Keywords: main-banner block
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
Example: {"preview_image_help": "/blocks/main-banner/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;


$context = Timber::context();
$fields = get_fields();

$left_column = checker_value($fields, 'left_column', true);

$context['title'] = checker_value($left_column, 'title');
$context['description'] = checker_value($left_column, 'description');
$context['button'] = checker_value($left_column, 'button');
$context['button_sec'] = checker_value($left_column, 'button_sec');
$context['scroll'] = checker_value($left_column, 'scroll');

$right_column = checker_value($fields, 'right_column', true);

$context['img_desktop'] = checker_value($right_column, 'img_desktop');
$context['img_mobile'] = checker_value($right_column, 'img_mobile');

$context['class'] = 'main-banner';
$context['block'] = $block;

Timber::render( 'main-banner/main-banner.twig', $context );