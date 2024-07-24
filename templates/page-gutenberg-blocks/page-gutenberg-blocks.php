<?php
/**
 * The template for displaying all page-gutenberg-blocks.
 *
 * This is the template that displays all page-style-guides by default.
 * Please note that this is the WordPress construct of page-style-guides
 * and that other 'page-style-guides' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your page-style-guides you can use:
 * /mytheme/templates/page-style-guide-mypage-style-guide.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-style-guide-mypage-style-guide.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::context();

$timber_post     = new Timber\Post();
$context['post'] = $timber_post;

$pages = get_posts( array(
	'numberposts' => - 1,
	'post_type'   => 'any',
) );

$all_blocks = [];
if ( ! empty( $pages ) ) {
	foreach ( $pages as $data ) {

		$blocks = parse_blocks( $data->post_content );
		foreach ( $blocks as $block ) {
			if ( isset( $block['blockName'] ) && str_contains( $block['blockName'], 'acf' ) && $block['blockName'] !== 'acf/example' ) {
				$all_blocks[ $block['blockName'] ][] = $block;
			}
		}
	}
}
ob_start();
if ( ! empty( $all_blocks ) ) {
	foreach ( $all_blocks as $block ) {
		echo '<hr><h3>Block ' . $block[0]['blockName'] . '</h3><hr>';

		foreach ( $block as $data ) {
			echo render_block( $data );
		}
	}
}
$context['components'] = ob_get_clean();

Timber::render( 'page-gutenberg-blocks.twig', $context );
