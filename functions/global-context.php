<?php

if (class_exists('Timber')) {
	$timber = new Timber\Timber();
	$timber::$dirname = array('templates', 'blocks', 'layouts');

	add_filter('timber/context', 'add_to_context');
}

function add_to_context($context)
{
	$context['homelink'] = get_home_url();
	$context['menu']  = new Timber\Menu();
	$context['primary_menu']   = new Timber\Menu('primary_menu');
	$context['options'] = get_fields('options');
	$context['footer_menu']  = new Timber\Menu('footer_menu');
	$context['footer_top_menu']  = new Timber\Menu('footer_top_menu');
	$context['search_query']   = get_search_query();

	$context['years'] = get_terms([
		'taxonomy'   => 'years',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'DESC',
	]);

	$signatories = get_terms([
		'taxonomy'   => 'signatory',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
	]);
	$context['signatories'] = sortTitleWithNumbers($signatories);

	$context['signatory_types'] = get_terms([
		'taxonomy'   => 'signatory-type',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
	]);

	$chapters = get_terms([
		'taxonomy'   => 'chapter',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
	]);
	$context['chapters'] = sortTitleWithNumbers($chapters);

	$commitments = get_terms([
		'taxonomy'   => 'commitment',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
	]);
	$context['commitments'] = sortTitleWithNumbers($commitments);

	$context['countries'] = get_terms([
		'taxonomy'   => 'country',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
	]);

	$context['languages'] = get_terms([
		'taxonomy'   => 'language',
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
	]);

	return $context;
}
