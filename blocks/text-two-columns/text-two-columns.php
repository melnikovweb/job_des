<?php
/*
Block Name: Text Two Columns
Description: text-two-columns block
Category: custom-blocks
Icon: wordpress-alt
Keywords: text-two-columns block
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
Example: {"preview_image_help": "/blocks/text-two-columns/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context = Timber::context();
$fields = get_fields();

$context['subtitle'] = checker_value($fields, 'subtitle');
$context['title'] = checker_value($fields, 'title');

$left_column = checker_value($fields, 'left_column', true);
$right_column = checker_value($fields, 'right_column', true);

$context['left_column_text'] = checker_value($left_column, 'left_column_text');
$context['right_column_text'] = checker_value($right_column, 'right_column_text');

$context['classes']         = 'text-two-columns';
$context['block'] = $block;


Timber::render( 'text-two-columns/text-two-columns.twig', $context );
