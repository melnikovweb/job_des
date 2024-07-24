<?php
add_filter('timber/twig', 'add_to_twig');

/**
 * My custom Twig functionality.
 *
 * @param \Twig\Environment $twig
 *
 * @return \Twig\Environment
 */
function add_to_twig($twig)
{

	//Helpers
	$twig->addFunction(new Timber\Twig_Function('pvd', 'pvd'));
	$twig->addFunction(new Timber\Twig_Function('get_image', 'get_image'));
	$twig->addFunction(new Timber\Twig_Function('inline_svg', 'inline_svg'));
	$twig->addFunction(new Timber\Twig_Function('get_clean_phone', 'get_clean_phone'));
	$twig->addFunction(new Timber\Twig_Function('regex_text_to_span', 'regex_text_to_span'));
	$twig->addFunction(new Timber\Twig_Function('remote_img_file_exists', 'remote_img_file_exists'));
	$twig->addFunction(new Timber\Twig_Function('wp_get_attachment_image', 'wp_get_attachment_image'));
	$twig->addFunction(new Timber\Twig_Function('firstTitleWithNumber', 'firstTitleWithNumber'));
	$twig->addFunction(new Timber\Twig_Function('generateTable', 'generateTable'));
	$twig->addFunction(new Timber\Twig_Function('measuresGroupItem', 'measuresGroupItem'));
	return $twig;
}
