<?php
/*
Block Name: Project
Description: project block
Category: custom-blocks
Icon: wordpress-alt
Keywords: project block
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
Example: {"preview_image_help": "/blocks/project/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;



$context = Timber::context();
$fields = get_fields();


$context['subtitle'] = checker_value($fields, 'subtitle');
$context['title'] = checker_value($fields, 'title');
$context['description'] = checker_value($fields, 'description');

$context['list_items'] = checker_value($fields, 'list_items');


$context['class'] = 'project';
$context['block'] = $block;



Timber::render( 'project/project.twig', $context );
