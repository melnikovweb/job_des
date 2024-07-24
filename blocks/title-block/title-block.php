<?php
/*
Block Name: Title Block
Description: title-block block
Category: custom-blocks
Icon: wordpress-alt
Keywords: title-block block
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
Example: {"preview_image_help": "/blocks/title-block/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context                  = Timber::context();
$context['demo']          = false;
$context['fields']        = ! $context['demo'] ? get_fields() : json_decode( file_get_contents( __DIR__ . '/demo.json' ) );
$context['class']         = 'title-block';
$context['classes']       = 'gutenberg-block';
$context['block']         = ! empty( $block ) ? $block : null;
$context['block_classes'] = $block['className'] ?? null;

Timber::render( 'title-block/title-block.twig', $context );
