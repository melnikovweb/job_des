<?php
/*
Block Name: Signatory carousel
Description: signatory-carousel block
Category: custom-blocks
Icon: wordpress-alt
Keywords: signatory-carousel block
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
Example: {"preview_image_help": "/blocks/signatory-carousel/preview.jpg"}
*/

if( isset( $block['data']['preview_image_help'] )  ) :
	echo '<img src="'. get_template_directory_uri() . $block['data']['preview_image_help'] .'" style="width:100%; height:auto;">';
	return;
endif;

$context = Timber::context();
$fields = get_fields();

$context['title'] = checker_value($fields, 'title');
$context['class']= 'signatory-carousel';
$context['block'] = $block;
$context['block_classes'] = $block['className'] ?? null;

$signatories = get_terms( array(
    'taxonomy' => 'signatory',
    'hide_empty' => 0,
	'parent' => 0,
	'order' => 'ASC',
) );

$signatories_arr = [];

foreach ($signatories as $signatory) {

	$posts = get_posts([
		'posts_per_page' => -1,
		'post_type'      => 'signatory-report',
		'tax_query' => array(
			array(
				'taxonomy' => 'signatory',
				'field'    => 'term_id',
				'terms'    => $signatory->term_id,
			)
		)
	]);

	$mainPost = array_values(array_filter($posts, function ($post) {
		if ($post->post_parent === 0) {
			return $post;
		}
	}));

	$signatories_arr[] = [
		'url' => (!empty($mainPost)) ? get_the_permalink($mainPost[0]->ID) : '',
		'image' => get_field('signatory_white_image', 'term_'.$signatory->term_id),
	];
}

$context['signatories_arr'] = $signatories_arr;
$context['signatories_count'] = sizeof($signatories_arr);

Timber::render( 'signatory-carousel/signatory-carousel.twig', $context );