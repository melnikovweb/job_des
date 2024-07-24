<?php
/*
Block Name: Task force
Description: task-force block
Category: custom-blocks
Icon: wordpress-alt
Keywords: task-force block
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
Example: {"preview_image_help": "/blocks/task-force/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context = Timber::context();
$fields = get_fields();

$left_column = checker_value($fields, 'left_column', true);

$context['img_desktop'] = checker_value($left_column, 'img_desktop');
$context['img_mobile'] = checker_value($left_column, 'img_mobile');

$right_column = checker_value($fields, 'right_column', true);

$context['subtitle'] = checker_value($right_column, 'subtitle');
$context['title'] = checker_value($right_column, 'title');
$context['description'] = checker_value($right_column, 'description');


$context['button'] = checker_value($fields, 'button');
$context['button_sec'] = checker_value($fields, 'button_sec');

$context['class'] = 'task-force';
$context['block'] = $block;

Timber::render( 'task-force/task-force.twig', $context );
