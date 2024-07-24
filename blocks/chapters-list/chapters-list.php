<?php
/*
Block Name: Сhapters List
Description: chapters-list block
Category: custom-blocks
Icon: wordpress-alt
Keywords: chapters-list block
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
Example: {"preview_image_help": "/blocks/chapters-list/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context                  = Timber::context();
$context['demo']          = false;
$context['fields']        = ! $context['demo'] ? get_fields() : json_decode( file_get_contents( __DIR__ . '/demo.json' ) );
$context['class']         = 'chapters-list';
$context['classes']       = 'gutenberg-block';
$context['block']         = ! empty( $block ) ? $block : null;
$context['block_classes'] = $block['className'] ?? null;

// $chapters = Timber::get_terms('chapter');

$chapters = get_terms( array(
    'taxonomy' => 'chapter',
    'hide_empty' => 0,
	'orderby'    => 'term_id',
	'order' => 'ASC'
) );

$context['chapters_test'] = $chapters;
$chapters_arr = [];

foreach ($chapters as $chapter) {
	$chapters_arr[] = [
		'image' => get_field('chapter_image', 'term_'.$chapter->term_id),
		'title' => get_field('chapter_title', 'term_'.$chapter->term_id),
		'description' =>  $chapter->description,
		'url' => get_term_link($chapter->term_id, 'chapter')
	];
}

$context['chapters_arr'] = $chapters_arr;

// $term = get_queried_object();
// $context['chapter_image'] = get_field('chapter_image', $term);

Timber::render( 'chapters-list/chapters-list.twig', $context );
