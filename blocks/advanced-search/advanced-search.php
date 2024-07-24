<?php
/*
Block Name: Advanced Search
Description: Advanced Search block
Category: custom-blocks
Icon: wordpress-alt
Keywords: advanced search block
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
Example: {"preview_image_help": "/blocks/advanced-search/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context                  = Timber::context();
$context['class']         = 'advanced-search';
$context['classes']       = 'gutenberg-block';
$context['block']         = ! empty( $block ) ? $block : null;
$context['block_classes'] = $block['className'] ?? null;
$context['postsPerPage']  = 6;
$context['currentPage']   = !empty($_GET['currentPage']) ? $_GET['currentPage'] : 1;

Timber::render( 'advanced-search/advanced-search.twig', $context );