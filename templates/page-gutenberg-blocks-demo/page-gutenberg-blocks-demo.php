<?php
/**
 * The template for displaying all page-gutenberg-blocks-demos.
 *
 * This is the template that displays all page-gutenberg-blocks-demos by default.
 * Please note that this is the WordPress construct of page-gutenberg-blocks-demos
 * and that other 'page-gutenberg-blocks-demos' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your page-gutenberg-blocks-demos you can use:
 * /mytheme/templates/page-gutenberg-blocks-demo-mypage-gutenberg-blocks-demo.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-gutenberg-blocks-demo-mypage-gutenberg-blocks-demo.php
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

ob_start();


foreach ( glob( get_template_directory() . '/blocks/*/demo.json' ) as $filename ) {
	$block_path = str_replace( '/demo.json', '', $filename );
	$block_name = substr( strrchr( $block_path, "/" ), 1 );
	$block_path = $block_path . '/' . $block_name . '.php';

	$demo = json_decode( file_get_contents( $filename ) );

	$css_path = get_template_directory_uri() . '/public/css/blocks/' . $block_name . '.css';
	$js_path  = get_template_directory_uri() . '/public/js/blocks/' . $block_name . '.js';

	$block_modification = explode( ';', $demo->block_modification );
	?>

	<section class="sk-container sk-container--fl">
		<hr>
		<div class="sk-container">
			<h2>Block: <?php echo $demo->repo_block_name; ?></h2>

			<?php if ( ! empty( $block_modification ) ) : ?>
				<h4>Modification:</h4>
				<ul>
					<?php foreach ( $block_modification as $item ): ?>
						<li><?php echo $item; ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
		<hr>
	</section>

	<?php require_once $block_path; ?>

	<link rel="stylesheet" href=" <?php echo $css_path; ?>">
	<script src="<?php echo $js_path; ?>"></script>

	<?php
}

$context['components'] = ob_get_clean();

Timber::render( 'page-gutenberg-blocks-demo.twig', $context );
